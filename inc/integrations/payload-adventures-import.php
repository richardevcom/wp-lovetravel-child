<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adventures Import Tool (Payload CMS -> WP CPT `nd_travel_cpt_1`)
 * - Paged fetch from https://tribetravel.eu/api/adventures/
 * - Overwrite existing toggle (by payload_adventure_id)
 * - Media: featured (thumbnail or sliderImage), gallery (attach images)
 * - Taxonomies: Duration, Difficulty, Month (create 12 months if missing)
 * - UI: WP postbox layout consistent with other tools
 */
class Lovetravel_Adventures_Import
{
    const MENU_SLUG = 'payload-adventures-import';
    const CPT = 'nd_travel_cpt_1';
    const TAX_DURATION = 'nd_travel_cpt_1_tax_1';
    const TAX_DIFFICULTY = 'nd_travel_cpt_1_tax_2';
    const TAX_MONTH = 'nd_travel_cpt_1_tax_3'; // renamed to Month per user
    const META_SOURCE_ID = 'payload_adventure_id';
    const API_BASE = 'https://tribetravel.eu';
    const API_PATH = '/api/adventures/';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        
        add_action('wp_ajax_lovetravel_adventures_get_stats', [$this, 'ajax_get_stats']);
    add_action('wp_ajax_lovetravel_adventures_import_page', [$this, 'ajax_import_page']);
    // Month terms creation was one-time; no longer exposed in UI
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=' . self::CPT,
            __('Import Adventures', 'lovetravel-child'),
            __('Import Adventures', 'lovetravel-child'),
            'manage_options',
            self::MENU_SLUG,
            [$this, 'render_admin_page']
        );
    }

    public function enqueue_assets($hook)
    {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== self::CPT . '_page_' . self::MENU_SLUG) {
            return;
        }

        if (!wp_style_is('lovetravel-admin-tools', 'registered')) {
            wp_register_style(
                'lovetravel-admin-tools',
                LOVETRAVEL_CHILD_URI . '/assets/css/admin-tools.css',
                [],
                LOVETRAVEL_CHILD_VERSION
            );
        }
        wp_enqueue_style('lovetravel-admin-tools');

        wp_register_script(
            'lovetravel-adventures-import',
            LOVETRAVEL_CHILD_URI . '/assets/js/admin-adventures-import.js',
            ['jquery'],
            LOVETRAVEL_CHILD_VERSION,
            true
        );
        wp_localize_script('lovetravel-adventures-import', 'adventuresImport', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lovetravel_adventures_import'),
            'apiBase' => self::API_BASE,
            'defaults' => [
                'limit' => 10,
                'overwrite' => false,
            ],
        ]);
        wp_enqueue_script('lovetravel-adventures-import');
    }

    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'lovetravel-child'));
        }
        include __DIR__ . '/payload-adventures-import.page.php';
    }

    private function verify_request()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'lovetravel_adventures_import')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }
    }

    public function ajax_get_stats()
    {
        $this->verify_request();

        $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
        $limit = isset($_POST['limit']) ? max(1, intval($_POST['limit'])) : 10;

        $resp = wp_remote_get(add_query_arg([
            'page' => $page,
            'limit' => $limit,
        ], self::API_BASE . self::API_PATH), ['timeout' => 30]);

        if (is_wp_error($resp)) {
            wp_send_json_error(['message' => $resp->get_error_message()]);
        }

        $data = json_decode(wp_remote_retrieve_body($resp), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON']);
        }

        $stats = [
            'totalDocs' => intval($data['totalDocs'] ?? 0),
            'totalPages' => intval($data['totalPages'] ?? 0),
            'page' => intval($data['page'] ?? $page),
            'hasNextPage' => (bool) ($data['hasNextPage'] ?? false),
            'nextPage' => isset($data['nextPage']) ? intval($data['nextPage']) : null,
        ];

        wp_send_json_success(['stats' => $stats]);
    }

    public function ajax_import_page()
    {
        $this->verify_request();

    $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $limit = isset($_POST['limit']) ? max(1, intval($_POST['limit'])) : 10;
    $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] === '1';
    $dry_run = isset($_POST['dry_run']) && $_POST['dry_run'] === '1';

        $resp = wp_remote_get(add_query_arg([
            'page' => $page,
            'limit' => $limit,
        ], self::API_BASE . self::API_PATH), ['timeout' => 60]);

        if (is_wp_error($resp)) {
            wp_send_json_error(['message' => $resp->get_error_message()]);
        }

        $data = json_decode(wp_remote_retrieve_body($resp), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON']);
        }

        $docs = $data['docs'] ?? [];

        $results = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'messages' => [],
        ];

        foreach ($docs as $doc) {
            $r = $this->upsert_adventure($doc, $overwrite, $dry_run);
            $results[$r['bucket']]++;
            if (!empty($r['message'])) {
                $results['messages'][] = $r['message'];
            }
        }

        $results['hasNextPage'] = (bool) ($data['hasNextPage'] ?? false);
        $results['nextPage'] = isset($data['nextPage']) ? intval($data['nextPage']) : null;

        wp_send_json_success($results);
    }

    // ajax_create_month_terms removed per request (one-time action was done)

    private function upsert_adventure($doc, $overwrite = false, $dry_run = false)
    {
        $payload_id = isset($doc['id']) ? sanitize_text_field($doc['id']) : null;
        if (!$payload_id) {
            return ['bucket' => 'failed', 'message' => '❌ Missing id'];
        }

        $existing_id = $this->find_post_by_meta(self::META_SOURCE_ID, $payload_id);
        $postarr = $this->build_postarr($doc, $existing_id);

        if ($existing_id && !$overwrite) {
            // Update only non-destructive fields? For now, skip if overwrite disabled
            return ['bucket' => 'skipped', 'message' => '⏭️ Skipped ' . esc_html($postarr['post_title']) . ' (exists)'];
        }

        if ($dry_run) {
            $bucket = $existing_id ? 'updated' : 'imported';
            return ['bucket' => $bucket, 'message' => '🧪 (dry-run) ' . esc_html($postarr['post_title'])];
        }

        if ($existing_id) {
            $post_id = wp_update_post($postarr, true);
            $bucket = 'updated';
        } else {
            $post_id = wp_insert_post($postarr, true);
            $bucket = 'imported';
        }

        if (is_wp_error($post_id)) {
            return ['bucket' => 'failed', 'message' => '❌ ' . $post_id->get_error_message()];
        }

        // Ensure source id meta
        update_post_meta($post_id, self::META_SOURCE_ID, $payload_id);

        // Meta & taxonomies
        $this->sync_meta($post_id, $doc);
        $this->sync_taxonomies($post_id, $doc);

        // Media
        $this->sync_media($post_id, $doc);

        return ['bucket' => $bucket, 'message' => ($bucket === 'imported' ? '✅ ' : '♻️ ') . esc_html(get_the_title($post_id))];
    }

    private function build_postarr($doc, $existing_id)
    {
        $title = isset($doc['title']) ? wp_strip_all_tags($doc['title']) : 'Adventure';
        $slug = isset($doc['slug']) ? sanitize_title($doc['slug']) : null;
        $status = (isset($doc['status']) && $doc['status'] === 'published') ? 'publish' : 'draft';
        $content = $this->richtext_to_html($doc['description'] ?? []);

        $postarr = [
            'ID' => $existing_id ?: 0,
            'post_type' => self::CPT,
            'post_title' => $title,
            'post_name' => $slug,
            'post_status' => $status,
            'post_content' => $content,
        ];

        return $postarr;
    }

    private function richtext_to_html($blocks)
    {
        if (!is_array($blocks)) return '';
        $html = '';
        foreach ($blocks as $node) {
            if (isset($node['children'])) {
                foreach ($node['children'] as $child) {
                    if (isset($child['text'])) {
                        $html .= wp_kses_post($child['text']);
                    } elseif (isset($child['type']) && $child['type'] === 'link' && !empty($child['url'])) {
                        $url = esc_url($child['url']);
                        $t = isset($child['children'][0]['text']) ? esc_html($child['children'][0]['text']) : $url;
                        $html .= '<a href="' . $url . '" target="_blank" rel="noopener">' . $t . '</a>';
                    }
                }
                $html .= "\n\n";
            }
        }
        return $html;
    }

    private function sync_meta($post_id, $doc)
    {
        // Destination name
        if (!empty($doc['destination']['name'])) {
            update_post_meta($post_id, 'nd_travel_meta_box_destination_name', sanitize_text_field($doc['destination']['name']));
        }

        // Accent color
        $color = $doc['tripStatus']['color']['code'] ?? '';
        if (!$color) {
            // fallback to theme accent (cannot read here) -> fallback constant
            $color = '#EA5B10';
        }
        $safe_color = sanitize_hex_color($color);
        update_post_meta($post_id, 'nd_travel_meta_box_color', $safe_color ?: '#EA5B10');

        // Prices (store both legacy nd_travel_* keys for Elementor and our custom breakdown)
        $reservation_price = isset($doc['reservationPrice']) ? floatval($doc['reservationPrice']) : null;
        $full_price_existing = isset($doc['existingCustomerFullPrice']) ? floatval($doc['existingCustomerFullPrice']) : null;
        $full_price_new = isset($doc['newCustomerFullPrice']) ? floatval($doc['newCustomerFullPrice']) : null;
        $discount_price = isset($doc['discountPrice']) ? floatval($doc['discountPrice']) : null;
        $discount_until = !empty($doc['discountPriceUntil']) ? sanitize_text_field($doc['discountPriceUntil']) : '';

        // Persist custom detailed fields
        if (!is_null($reservation_price)) update_post_meta($post_id, 'reservation_price', $reservation_price);
        if (!is_null($full_price_existing)) update_post_meta($post_id, 'full_price_existing', $full_price_existing);
        if (!is_null($full_price_new)) update_post_meta($post_id, 'full_price_new', $full_price_new);
        if (!is_null($discount_price)) update_post_meta($post_id, 'discount_price', $discount_price);
        if ($discount_until !== '') update_post_meta($post_id, 'discount_until', $discount_until);

        // Populate Elementor-visible price fields used by parent theme/templates
        // Choose a display price priority: reservation -> new customer full -> existing customer full
        $display_price = $reservation_price ?? $full_price_new ?? $full_price_existing;
        if (!is_null($display_price)) {
            // Raw numeric; Elementor heading handles presentation
            update_post_meta($post_id, 'nd_travel_meta_box_show_price', $display_price);
            // Base price (legacy key seen in demo data)
            update_post_meta($post_id, 'nd_travel_meta_box_price', $display_price);
            // New price key (some templates read this)
            update_post_meta($post_id, 'nd_travel_meta_box_new_price', $display_price);
        }
        if (!is_null($discount_price)) {
            // Cover both promotion keys found in demo exports
            update_post_meta($post_id, 'nd_travel_meta_box_promotion_price', $discount_price);
            update_post_meta($post_id, 'nd_travel_meta_box_promo_price', $discount_price);
        }

        // Dates, length, stay
        $date_from = !empty($doc['dateFrom']) ? sanitize_text_field($doc['dateFrom']) : '';
        $date_to = !empty($doc['dateTo']) ? sanitize_text_field($doc['dateTo']) : '';
        if ($date_from !== '') {
            update_post_meta($post_id, 'date_from', $date_from);
            update_post_meta($post_id, 'nd_travel_meta_box_availability_from', $date_from);
        }
        if ($date_to !== '') {
            update_post_meta($post_id, 'date_to', $date_to);
            update_post_meta($post_id, 'nd_travel_meta_box_availability_to', $date_to);
        }

        // Derive length in days if not provided: inclusive day count based on from/to
        $length = null;
        if (!empty($doc['length'])) {
            $length = intval($doc['length']);
        } elseif ($date_from && $date_to) {
            $from_ts = strtotime($date_from);
            $to_ts = strtotime($date_to);
            if ($from_ts && $to_ts && $to_ts >= $from_ts) {
                $length = max(1, (int) round(($to_ts - $from_ts) / DAY_IN_SECONDS) + 1);
            }
        }
        if (!is_null($length)) {
            update_post_meta($post_id, 'length_days', $length);
        }

        if (!empty($doc['stay'])) update_post_meta($post_id, 'stay', sanitize_text_field($doc['stay']));
    }

    private function sync_taxonomies($post_id, $doc)
    {
        // Duration taxonomy from length
        if (!empty($doc['length'])) {
            $term = $this->map_duration_term(intval($doc['length']));
            if ($term) {
                wp_set_object_terms($post_id, $term, self::TAX_DURATION, true);
            }
        }

        // Difficulty taxonomy
        if (!empty($doc['difficulty'])) {
            $diff_term = $this->map_difficulty_term($doc['difficulty']);
            if ($diff_term) {
                wp_set_object_terms($post_id, $diff_term, self::TAX_DIFFICULTY, true);
            }
        }

        // Month taxonomy from dateFrom
        if (!empty($doc['dateFrom'])) {
            $month_num = (int) date('n', strtotime($doc['dateFrom']));
            $month_name = $this->get_month_name_lv($month_num);
            if ($month_name) {
                $this->ensure_month_terms();
                wp_set_object_terms($post_id, $month_name, self::TAX_MONTH, true);
            }
        }
    }

    private function sync_media($post_id, $doc)
    {
        $featured_url = '';
        if (!empty($doc['thumbnail']['url'])) {
            $featured_url = $doc['thumbnail']['url'];
        } elseif (!empty($doc['sliderImage']['url'])) {
            $featured_url = $doc['sliderImage']['url'];
        }
        if ($featured_url) {
            $attachment_id = $this->sideload_image($featured_url, $doc['thumbnail']['filename'] ?? 'featured.jpg');
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        // Gallery: attach images as media to post; actual display via Elementor template on frontend
        if (!empty($doc['images']) && is_array($doc['images'])) {
            $gallery_ids = [];
            foreach ($doc['images'] as $img) {
                $url = $img['url'] ?? '';
                if (!$url) continue;
                $aid = $this->sideload_image($url, $img['filename'] ?? 'image.jpg');
                if ($aid) $gallery_ids[] = $aid;
            }
            if (!empty($gallery_ids)) {
                update_post_meta($post_id, '_adventure_gallery', $gallery_ids);
            }
        }
    }

    private function sideload_image($url, $filename = 'image.jpg')
    {
        if (!function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        if (!function_exists('media_handle_sideload')) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
        }
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            return 0;
        }
        $file = [
            'name' => sanitize_file_name($filename ?: basename(parse_url($url, PHP_URL_PATH))),
            'type' => wp_check_filetype($filename)['type'] ?? 'image/jpeg',
            'tmp_name' => $tmp,
            'error' => 0,
            'size' => filesize($tmp),
        ];
        $aid = media_handle_sideload($file, 0);
        if (is_wp_error($aid)) {
            @unlink($tmp);
            return 0;
        }
        return (int) $aid;
    }

    private function map_duration_term($length_days)
    {
        // User-provided duration buckets (LV names already exist):
        // Īsie ceļojumi, Nedēļas ceļojumi, Garie ceļojumi
        if ($length_days <= 4) return 'Īsie ceļojumi';
        if ($length_days <= 9) return 'Nedēļas ceļojumi';
        return 'Garie ceļojumi';
    }

    private function map_difficulty_term($difficulty)
    {
        // Provided names with leading index
        $map = [
            '1' => '1. Viegla',
            '2' => '2. Vidēja',
            '3' => '3. Sarežģīta',
            '4' => '4. Izaicinoša',
        ];
        $key = (string) $difficulty;
        return $map[$key] ?? null;
    }

    private function ensure_month_terms()
    {
        $months = [
            1 => 'Janvāris', 2 => 'Februāris', 3 => 'Marts', 4 => 'Aprīlis', 5 => 'Maijs', 6 => 'Jūnijs',
            7 => 'Jūlijs', 8 => 'Augusts', 9 => 'Septembris', 10 => 'Oktobris', 11 => 'Novembris', 12 => 'Decembris'
        ];
        $created = [];
        foreach ($months as $i => $name) {
            $slug = sanitize_title($name);
            $term = term_exists($name, self::TAX_MONTH);
            if (!$term) {
                $res = wp_insert_term($name, self::TAX_MONTH, ['slug' => $slug]);
                if (!is_wp_error($res)) {
                    $created[] = $name;
                }
            }
        }
        return $created;
    }

    private function get_month_name_lv($n)
    {
        $map = [
            1 => 'Janvāris', 2 => 'Februāris', 3 => 'Marts', 4 => 'Aprīlis', 5 => 'Maijs', 6 => 'Jūnijs',
            7 => 'Jūlijs', 8 => 'Augusts', 9 => 'Septembris', 10 => 'Oktobris', 11 => 'Novembris', 12 => 'Decembris'
        ];
        return $map[$n] ?? null;
    }

    private function find_post_by_meta($key, $value)
    {
        global $wpdb;
        $post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
            $key,
            $value
        ));
        return $post_id ? intval($post_id) : 0;
    }
}

// Initialize
new Lovetravel_Adventures_Import();
