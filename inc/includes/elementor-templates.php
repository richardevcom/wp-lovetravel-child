<?php
// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Elementor Templates Helper
 * - Stores JSON under elementor-templates/
 * - Provides importer function with idempotency
 * - Adds optional WP-CLI command for batch import
 */
class Lovetravel_Elementor_Templates_Helper {
    const DIR = 'elementor-templates';

    public static function template_dir() {
        return trailingslashit(LOVETRAVEL_CHILD_DIR . '/' . self::DIR);
    }

    public static function template_uri() {
        return trailingslashit(LOVETRAVEL_CHILD_URI . '/' . self::DIR);
    }
}

/**
 * Add "Import Templates" action link at the bottom of Elementor Library listing.
 * Target screen: edit.php?post_type=elementor_library
 */
add_action('manage_posts_extra_tablenav', function($which){
    if ($which !== 'bottom') {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || empty($screen->id) || $screen->id !== 'edit-elementor_library') {
        return;
    }
    $url = add_query_arg([], admin_url('admin.php?page=lovetravel-elementor-import'));
    echo '<div class="alignleft actions" style="margin-top:8px">';
    echo '<a class="button button-primary" href="' . esc_url($url) . '">' . esc_html__('Import Templates', 'lovetravel-child') . '</a>';
    echo '</div>';
}, 20);

/**
 * Import an Elementor template JSON file from the child theme directory.
 * - Does not overwrite if a template having the same title already exists.
 *
 * @param string $file Filename within elementor-templates/
 * @return array { success: bool, message: string, template_id?: int }
 */
function lovetravel_child_import_elementor_template($file) {
    $path = Lovetravel_Elementor_Templates_Helper::template_dir() . $file;
    if (! file_exists($path)) {
        return [ 'success' => false, 'message' => 'File not found: ' . $file ];
    }

    // Read JSON
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
        return [ 'success' => false, 'message' => 'Invalid JSON in ' . $file ];
    }

    // Elementor import expects the JSON structure as provided by Elementor exports.
    // Here, we minimally handle both full templates and section exports.
    $title = isset($data['title']) ? sanitize_text_field($data['title']) : basename($file, '.json');
    $type  = isset($data['type']) ? sanitize_key($data['type']) : 'section';

    // Check if a template with this title already exists in elementor_library
    $existing = get_page_by_title($title, OBJECT, 'elementor_library');
    if ($existing) {
        return [ 'success' => true, 'message' => 'Template exists: ' . $title . ' (ID ' . $existing->ID . ')', 'template_id' => (int) $existing->ID ];
    }

    // Create elementor_library post
    $postarr = [
        'post_title'   => $title,
        'post_type'    => 'elementor_library',
        'post_status'  => 'publish',
    ];
    $post_id = wp_insert_post($postarr, true);
    if (is_wp_error($post_id)) {
        return [ 'success' => false, 'message' => $post_id->get_error_message() ];
    }

    // Store template type and data
    update_post_meta($post_id, '_elementor_template_type', $type); // e.g., section, page

    // Elementor stores content in _elementor_data meta as JSON string
    update_post_meta($post_id, '_elementor_data', wp_slash($json));

    // Mark as section/page
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');

    // Ensure taxonomy term is set for Elementor library filtering
    // Taxonomy: elementor_library_type; terms like 'section', 'page'
    if (taxonomy_exists('elementor_library_type')) {
        wp_set_object_terms($post_id, $type, 'elementor_library_type', false);
    }

    return [ 'success' => true, 'message' => 'Imported template: ' . $title, 'template_id' => (int) $post_id ];
}

// Optional: WP-CLI command for batch import
if (defined('WP_CLI') && WP_CLI) {
    /**
     * wp lovetravel import-elementor-template <filename>
     */
    WP_CLI::add_command('lovetravel import-elementor-template', function($args) {
        list($file) = $args;
        $res = lovetravel_child_import_elementor_template($file);
        if ($res['success']) {
            WP_CLI::success($res['message']);
        } else {
            WP_CLI::error($res['message']);
        }
    });
}

/**
 * Admin UI: Elementor → Templates Import (submenu, placed at bottom)
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'elementor',
        __('Elementor Templates Import', 'lovetravel-child'),
        __('Templates Import', 'lovetravel-child'),
        'manage_options',
        'lovetravel-elementor-import',
        function() {
            if (! current_user_can('manage_options')) {
                wp_die(__('Insufficient permissions', 'lovetravel-child'));
            }
            $dir = Lovetravel_Elementor_Templates_Helper::template_dir();
            $files = is_dir($dir) ? array_values(array_filter(scandir($dir), function($f){
                return substr($f, -5) === '.json';
            })) : [];
            echo '<div class="wrap"><h1>' . esc_html__('Elementor Templates Import', 'lovetravel-child') . '</h1>';
            if (isset($_POST['lt_import_nonce']) && wp_verify_nonce($_POST['lt_import_nonce'], 'lt_import_tpl') && !empty($_POST['template_file'])) {
                $file = sanitize_file_name(wp_unslash($_POST['template_file']));
                $res = lovetravel_child_import_elementor_template($file);
                if ($res['success']) {
                    echo '<div class="notice notice-success"><p>' . esc_html($res['message']) . '</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>' . esc_html($res['message']) . '</p></div>';
                }
            }
            echo '<form method="post">';
            wp_nonce_field('lt_import_tpl', 'lt_import_nonce');
            echo '<p><label for="template_file">' . esc_html__('Select template JSON:', 'lovetravel-child') . '</label> ';
            echo '<select name="template_file" id="template_file">';
            foreach ($files as $file) {
                echo '<option value="' . esc_attr($file) . '">' . esc_html($file) . '</option>';
            }
            echo '</select></p>';
            submit_button(__('Import Template', 'lovetravel-child'));
            echo '</form></div>';
        }
    );
}, 99);
