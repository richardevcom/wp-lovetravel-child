---
mode: agent
---
This prompts purpose is to do anything related to update, fix, create, delete, etc. Wordpress UI in theme or plugin. Ask questions about the task/tasks (can be multiple) that need to be done related to UI edit, update, creation, deletion, fixing, etc., then check against the provided guide (below) of default wordpress admin UI, layout, styles etc. and based on that guide, proceed with  analyzing, researching, planing and executing.

Here is the guide:
---
# Comprehensive Guide to Reusing WordPress Admin UI, Layout, and Styles in Child Themes and Plugins

---

## Introduction

**Reusing the built-in WordPress admin UI, layout, and styles** is crucial for theme and plugin developers who aim for seamless integration, efficient development, and a consistent user experience within WordPress. Rather than creating custom admin interfaces from scratch—with all the resulting maintenance, accessibility, and compatibility headaches—leveraging native styles and components saves substantial resources and ensures compliance with WordPress’s design and usability standards.

This in-depth, actionable guide provides everything you need to optimize your child theme’s admin area or plugin settings by reusing, extending, and customizing the existing WordPress admin UI. The report covers admin UI structure, essential CSS and HTML patterns, script/style loading, best practices for forms, tables, notices, and responsive layouts, as well as how to provide a familiar, accessible experience for users. The detailed instructions and code snippets are ready for copy-pasting into your Copilot agent chat or project codebase, and the layout strictly mirrors best-practice WordPress conventions.

---

## 1. Core Layout Structure of the WordPress Admin UI

The WordPress admin dashboard is systematically architected to foster a robust, flexible, and consistent environment for managing content, settings, users, and extensions. Its core layout relies on a set of foundational containers and patterns:

- **`#wpwrap`**: The whole admin interface wrapper, housing all inner elements.
- **`#wpcontent`**: The main content area, excluding the navigation sidebar.
- **`#adminmenuwrap` / `#adminmenu`**: The left sidebar for navigation (Admin menu).
- **`#wpbody` / `#wpbody-content`**: Wrappers for the main page content and admin screens.
- **`#wpfooter`**: The administrative footer bar.
- **`.wrap`**: A general-purpose container for individual admin page content.

**Content Layout Patterns:**
- **Single Column Layout**: Used for most admin screens (full-width content).
- **Two-Column Layout**: Found on post edit screens—uses `.columns-2`, `.inner-sidebar`, and distinct containers for main and side columns.
- **List Table Layout**: Deployed in core data management screens (e.g., posts, pages, plugins)—uses `.wp-list-table` and associated utility classes.

---

### Example Markup for Admin Page Wrapper

```php
<div class="wrap">
    <h1 class="wp-heading-inline">My Custom Settings Page</h1>
    <!-- Your form, notices, settings, etc. go here -->
</div>
```

**Explanation:**  
Wrapping your custom content in a `<div class="wrap">` ensures correct spacing, background, and alignment, and matches the core admin screen appearance. The heading should use proper semantics (`<h1>`) with the `wp-heading-inline` class if needed (for action buttons inline with the title).

---

## 2. WordPress Admin UI CSS Components

The admin UI is composed of numerous reusable CSS classes and IDs that create its visual language. Familiarize yourself with the following essential classes and elements to design interfaces that match WordPress core:

### Key Layout and Utility Classes

| CSS Selector/Class   | Purpose                                       |
|----------------------|-----------------------------------------------|
| `.wrap`              | Main settings page wrapper                    |
| `.columns-2`         | Two-column layout for edit/meta box pages     |
| `.inner-sidebar`     | The sidebar section in a 2-column layout      |
| `.clear`             | Clearfix utility                              |
| `.alignleft`, `.alignright` | Float positioning                     |
| `.hidden` , `.show-if-js`   | Visibility toggling                   |
| `.howto`, `.description`    | Helper text below form fields         |

**Layout Examples:**

```html
<div id="post-body" class="columns-2">
    <div id="post-body-content">
        <!-- Main content here -->
    </div>
    <div id="side-sortables" class="inner-sidebar">
        <!-- Meta boxes or sidebar widgets -->
    </div>
</div>
```

**Analysis:**  
Using prescribed WordPress layout classes ensures a familiar interface, alignment, and spacing, enabling meta boxes, widgets, and forms to visually integrate with WordPress admin conventions.

### Buttons and Controls

Use these classes for buttons to maintain a native admin look:

| Class             | Function                                                       |
|-------------------|---------------------------------------------------------------|
| `.button`         | Standard gray button, general actions                         |
| `.button-primary` | Bold blue, primary/submit actions                             |
| `.button-secondary`| Lighter alternative action                                   |
| `.button-link`    | Looks like a hyperlink; behaves as a button                   |
| `.page-title-action` | Inline header button (e.g., "Add New" on list pages)       |

**Button Example:**
```html
<input type="submit" class="button button-primary" value="Save Changes">
```
or
```php
<?php submit_button('Update Options', 'primary'); ?>
```

**Explanation:**  
Button classes can be freely mixed (`button button-secondary my-custom-class`). WordPress defines their CSS, handling hover/focus/active states, so they remain accessible and visually consistent with the rest of the admin.

### Notices and Message Boxes

For information, success, error, or warning messages, use `.notice` and its variants:

| Class                  | Purpose                                 |
|------------------------|-----------------------------------------|
| `.notice`              | General notice base class               |
| `.notice-success`      | Success messages (green)                |
| `.notice-error`        | Errors (red)                            |
| `.notice-warning`      | Warnings (yellow/orange)                |
| `.notice-info`         | Informational messages (blue)           |
| `.is-dismissible`      | Adds a dismiss ("x") button             |

**Notice Example (PHP):**
```php
echo '<div class="notice notice-success is-dismissible"><p>Settings saved!</p></div>';
```

**Custom PHP Notice Snippet:**
```php
function my_plugin_admin_notice() {
    ?>
    <div class="notice notice-info is-dismissible">
        <p><?php _e('Information message', 'textdomain'); ?></p>
    </div>
    <?php
}
add_action('admin_notices', 'my_plugin_admin_notice');
```
**Analysis:**  
Notices appear under the page title (not above) and follow accessibility and dismissibility conventions. Use these classes for *all* notifications rather than inventing custom markup.

### Typography and Form Elements

WordPress uses a robust typographic scale and consistent form styling:

- **Heading levels:** `h1` for page titles, `h2` for section titles, `h3` for meta box titles, `p` for paragraphs.
- **Font family:**  
  `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif`
- **Form field classes:**  
  - `.regular-text` for text inputs (standard width)  
  - `.small-text` for small inputs  
  - `.widefat` for full-width tables or inputs  
  - Labels: Always use `<label>`’s for accessibility
- **Admin table styling:**  
  `.form-table` for settings tables, `.wp-list-table` for data lists

**Form Example:**
```html
<table class="form-table">
  <tr>
    <th scope="row">
      <label for="setting_option">Option Label</label>
    </th>
    <td>
      <input type="text" name="setting_option" id="setting_option" value="" class="regular-text">
      <p class="description">Optional helper text here.</p>
    </td>
  </tr>
</table>
```

**Analysis:**  
This ensures correct spacing, background, and responsive behaviors. Never invent custom form element styling for settings—rely on `.form-table`, `.regular-text`, auxiliaries, and descriptive `<label>`/`.description` elements.

---

## 3. Admin Menu and Toolbar Components

The admin menu and top admin toolbar are vital for navigation and user workflows. Reusing their structural paradigms helps users quickly adapt to plugin or theme interfaces.

### Adding Custom Admin Pages

Use `add_menu_page()` or `add_submenu_page()` to insert top-level or nested pages. Always pass a callback function that outputs properly structured admin markup.

**Top-level Menu Example:**
```php
function my_theme_admin_menu() {
    add_menu_page(
        'Theme Settings',         // Page title
        'Theme Settings',         // Menu label
        'manage_options',         // Capability
        'my_theme_settings',      // Menu slug
        'my_theme_settings_page', // Callback
        'dashicons-admin-generic',// Icon
        2                         // Position (optional)
    );
}
add_action('admin_menu', 'my_theme_admin_menu');

function my_theme_settings_page() {
    echo '<div class="wrap"><h1>Theme Settings</h1><!-- Settings form here --></div>';
}
```
**Submenu Page Example:**
```php
add_submenu_page(
    'options-general.php',      // Parent menu slug
    'Extra Settings',           // Page title
    'Extra Settings',           // Menu label
    'manage_options',
    'my_extra_settings',
    'my_extra_settings_page'
);
```
**Analysis:**  
Leverage menu and toolbar actions to locate settings where users expect them (Settings, Appearance), or invest in a proper top-level menu only for major plugins or comprehensive themes.

---

## 4. Enqueueing Admin Styles and Scripts

### When and How to Enqueue

Always enqueue CSS and JS using the `admin_enqueue_scripts` action. This loads styles/scripts **only** in the admin area, preventing unnecessary bloat on the front end.

```php
function my_childtheme_admin_assets($hook) {
    // Only load on a custom settings page, e.g. "toplevel_page_my_theme_settings"
    if ($hook !== 'toplevel_page_my_theme_settings') {
        return;
    }
    wp_enqueue_style('my-theme-admin', get_theme_file_uri('admin-style.css'), array(), '1.0.0');
    wp_enqueue_script('my-theme-admin-js', get_theme_file_uri('admin.js'), array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'my_childtheme_admin_assets');
```

**Key Points:**
- `$hook` is a unique identifier for the current admin page. Use it to limit where scripts/styles are loaded.
- Use `get_theme_file_uri()` (for child themes), `get_stylesheet_directory_uri()`, or `plugin_dir_url(__FILE__)` to resolve file locations correctly.
- Avoid including assets globally unless absolutely required—target your settings/admin page(s) for performance.

**Example for Child Theme:**
```php
function child_theme_admin_styles($hook) {
    wp_enqueue_style('child-admin-style', get_stylesheet_directory_uri() . '/admin-style.css');
}
add_action('admin_enqueue_scripts', 'child_theme_admin_styles');
```
**Troubleshooting:**  
If your style doesn’t load, check the path and ensure that you use `get_theme_file_uri()` for child theme assets, not `get_template_directory_uri()` (which points to the parent theme).

---

## 5. Building Custom Admin Pages Using Native WordPress UI Elements

### Wrapping Content

- Always use the `<div class="wrap">` structure for custom admin pages.
- Use `<h1>` for page titles, additional `<h2>` for tabs or sections.

**Minimal Example:**
```php
function my_child_settings_page() {
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Child Theme Settings</h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('my_child_settings_group');
            do_settings_sections('my-child-settings-slug');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
```

The `settings_fields()` and `do_settings_sections()` functions output nonces and registered settings fields according to the Settings API, while `submit_button()` renders a standard WordPress button.

### Tabs, Buttons, and Actions

For page-action buttons (e.g., “Add New”), use:

```php
<a href="admin.php?page=my-plugin-add" class="page-title-action">Add New</a>
```
For tabbed navigation:

```php
<h2 class="nav-tab-wrapper">
    <a href="?page=one" class="nav-tab nav-tab-active">Tab One</a>
    <a href="?page=two" class="nav-tab">Tab Two</a>
</h2>
```

**Analysis:**  
This produces the familiar “tab” navigation look, as seen in Posts, Pages, and most plugin screens. Use `.nav-tab-wrapper` and `.nav-tab` for tabs—avoid custom tabs.

---

## 6. WordPress Settings API and Form Layout

### Why Use the Settings API

The Settings API enforces security (nonces), standard layout, data storage, and extensibility—ensuring your settings pages are future-proof and compliant.

**Registration Example:**
```php
function my_child_settings_init() {
    register_setting('my_child_settings_group', 'my_option');
    add_settings_section(
        'my_child_section',
        'Main Settings',
        function() { echo '<p>Section description.</p>'; },
        'my-child-settings-slug'
    );
    add_settings_field(
        'my_option',
        'Option Label',
        'my_child_option_field_cb',
        'my-child-settings-slug',
        'my_child_section'
    );
}
add_action('admin_init', 'my_child_settings_init');

function my_child_option_field_cb() {
    $option = get_option('my_option');
    echo '<input type="text" name="my_option" value="' . esc_attr($option) . '" class="regular-text">';
}
```

**Form Output:**
```php
<form method="post" action="options.php">
    <?php
      settings_fields('my_child_settings_group');
      do_settings_sections('my-child-settings-slug');
      submit_button();
    ?>
</form>
```

**Best practices:**
- Use a table with the class `form-table` for settings rows.
- Each field row is a `<tr>` with `<th>` for the label and `<td>` for the input.
- Always include helper text (`<p class="description">`) as needed.

---

## 7. Meta Boxes Styling and Structure

Meta boxes are collapsible containers, commonly seen in the post editing screens, and can be leveraged for grouped settings, advanced options, or content management.

**Register a Meta Box:**
```php
add_meta_box(
    'my_child_meta_id',
    'Meta Box Title',
    'my_child_meta_cb',
    'post', // or custom post type or your own settings page
    'side', // location: normal, side, advanced
    'default'
);

function my_child_meta_cb($post) {
    echo '<label for="meta_field">Label</label>';
    echo '<input type="text" id="meta_field" name="meta_field" value="" class="widefat">';
}
```

**Standard Structure:**
- `.postbox`: The container of the meta box
- `.hndle`: The drag/collapse header element
- `.inside`: The container for box content/input fields

**Analysis:**  
Wrap fields in the `.inside` div for proper padding and collapse behavior (WordPress adds the necessary JavaScript). Use the `add_meta_box()` function on custom admin pages/blogic for modular settings structure.

---

## 8. List Tables Layout and Styling

For tabular data (entries, logs, custom objects), use or extend the core `WP_List_Table` class, which provides sortable, paginated, and action-ready native tables.

**Basic Usage:**
```php
// Require if not loaded yet:
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class My_Custom_List_Table extends WP_List_Table {
    function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'date'  => 'Date'
        );
    }
    function prepare_items() {
        // Populate $this->items with data (arrays)
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = array(/* ... */);
    }
    function column_default($item, $column_name) {
        return $item[$column_name];
    }
}

$list_table = new My_Custom_List_Table();
$list_table->prepare_items();
$list_table->display();
```
**Classes Used:**
- `.wp-list-table`, `.widefat`, `.fixed`, `.striped`
- Row actions appear on hover (`.row-actions`), sortable columns (`.sortable`, `.sorted`)

**Analysis:**  
This ensures all control elements (bulk select, pagination, filtering) and styling will match WordPress conventions and provide an integrated experience.

---

## 9. Buttons and Controls Classes

Apply WordPress button classes for interactive elements, always using semantic `<button>` or `<input type="submit">` where applicable.

| Class            | Use-case                        | Example syntax                                |
|------------------|---------------------------------|-----------------------------------------------|
| `button`         | Base for all admin buttons      | `<input type="button" class="button">`        |
| `button-primary` | Primary/submit actions          | `<button class="button button-primary">Save</button>` |
| `button-secondary` | Secondary/alternative actions | `<a class="button button-secondary">Cancel</a>` |
| `button-link`    | Text-only (link-style) button   | `<a class="button button-link">Link Look</a>` |

WordPress automatically provides correct colors, border, focus, disabled states, and sizing for maximum accessibility and consistency.

---

## 10. Notices and Messages Styling

Display notices and feedback to users using the `.notice` system, ensuring messages are accessible, styled correctly, and can be dismissed if desired.

**Display a Success Notice:**
```php
echo '<div class="notice notice-success is-dismissible"><p>Settings saved!</p></div>';
```

**Display an Error Notice:**
```php
echo '<div class="notice notice-error"><p>There was a problem.</p></div>';
```

**Add via Hook:**
```php
add_action('admin_notices', function () {
    echo '<div class="notice notice-info"><p>Information message.</p></div>';
});
```

WordPress manages the color, left-bar, padding, and close mechanisms via CSS and JS. Dismissible notices require the `is-dismissible` class, and the close button is automatically injected.

---

## 11. Typography and Form Elements

Follow WordPress type hierarchy and input styles:

- **Headings:** Use semantic headings (`h1`, `h2`, `h3`) as prescribed.
- **Font stack:** Use the default admin fonts for all admin UI elements.  
  -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif
- **Inputs:**  
  Use `regular-text` for standard width, `small-text` for compact fields, or `widefat` for full-width tables.
- **Labels:**  
  Always connect `<label for="input_id">` to each form element for screen readers and accessibility.

**Accessibility Best Practices:**
- Ensure contrast ratios (4.5:1 minimum for body text).
- Use descriptive placeholders, but *never* as a substitute for labels.
- Provide focus indicators and keyboard navigation.

---

## 12. Responsive and Accessible Admin UI

WordPress implements responsive breakpoints and ARIA accessibility throughout admin screens. Native admin layouts automatically adapt from wide desktop screens to narrow mobile browsers:

**Responsive Examples:**
- Collapsible admin menu for small screens.
- Responsive tables: `.wp-list-table` adapts to mobile using row expansion.
- Adjusted form elements: Flex/grid layouts, stacking at breakpoints.

**Add Custom CSS for Responsiveness (if needed):**
```css
@media (max-width: 782px) {
    .form-table th, .form-table td {
        display: block;
        width: 100%;
    }
}
```
**Accessibility Notes:**
- Use `.screen-reader-text` for extra instructions.
- Use ARIA attributes (`aria-label`, `aria-describedby`) as appropriate.
- Never remove default focus outlines without providing alternatives.

---

## 13. Utility and Helper Classes

WordPress exposes small, powerful utility classes for layout and accessibility:

| Class                | Use-case                      |
|----------------------|------------------------------|
| `.wrap`              | Admin page content wrapper    |
| `.clear`             | Clearfix after floated items  |
| `.alignleft`/`.alignright`| Float content left/right|
| `.screen-reader-text`| Accessible visual hiding      |
| `.howto`/`.description`| Helper text                |
| `.hidden`            | Hide element, e.g., JS toggling |

---

## 14. Integrating Admin UI in Child Themes

**Best Practices:**

- Use `admin_enqueue_scripts` to load any extra CSS/JS for admin features.
- Reference files in the child theme with `get_theme_file_uri()`.
- Always wrap content in `.wrap` and use standard heading/form structures.
- Use the Settings API for any persistent options/configuration screens.
- *Do not* override admin layouts unless strictly necessary—extend them.
- Use Dashicons (WordPress icons) for consistency:  
  `<span class="dashicons dashicons-admin-generic"></span>`

**Custom Admin CSS Example:**
```php
function childtheme_admin_styles($hook) {
    wp_enqueue_style('child-admin', get_theme_file_uri('admin-style.css'));
}
add_action('admin_enqueue_scripts', 'childtheme_admin_styles');
```
**Plugin Recommendations for Reference:**
- [bueltge/WordPress-Admin-Style](https://github.com/bueltge/WordPress-Admin-Style): See every admin UI element and its markup.
- [Bracketspace WP Admin Mockup](https://wpadmin.bracketspace.com): Preview and inspect native admin elements visually.

---

## 15. WordPress Admin UI Best Practices and Guidelines

- **Embrace consistency**: Always reuse existing styles, classes, and patterns whenever possible.
- **Follow accessibility**: Ensure keyboard navigation, screen reader text, and color contrast.
- **No unnecessary custom styles**: Only override or add CSS when you must—for branding or new feature visuals.
- **Security**: Never forget to add nonces for forms and use capabilities checks (`current_user_can()`).
- **Performance**: Only load scripts/styles where strictly needed.
- **Native components before custom ones**: Minimize custom admin GUIs; leverage `WP_List_Table`, meta boxes, `.notice`s, etc..
- **Settings API for all settings screens**: It handles sanitization, validation, nonces, and extensibility.
- **Responsiveness:** Test on real devices and screen sizes.

---

## 16. WordPress Developer Resources for Admin UI

- [WordPress Developer Handbook](https://developer.wordpress.org/) – all theme/plugin dev documentation.
- [Admin UI Components DeepWiki](https://deepwiki.com/WordPress/wordpress-develop/6.1-admin-ui-components)
- [WP_List_Table – Class](https://developer.wordpress.org/reference/classes/wp_list_table/)
- [Settings API](https://developer.wordpress.org/plugins/settings/settings-api/)
- [Meta Boxes](https://developer.wordpress.org/plugins/metadata/custom-meta-boxes/)
- [Admin Notices](https://developer.wordpress.org/reference/hooks/admin_notices/)
- [WordPress-Admin-Style (plugin)](https://github.com/bueltge/WordPress-Admin-Style) – Visual CSS/class reference.
- [Bracketspace Dummy Dashboard](https://wpadmin.bracketspace.com) – Visual frontend for admin mockups.

---

## 17. Summary Table: Core Elements and Classes

| UI Element           | Class/Selector              | Notes                                                   |
|----------------------|-----------------------------|---------------------------------------------------------|
| Wrapper              | `.wrap`                     | Main content area for settings/admin pages              |
| Page Title           | `h1`, `.wp-heading-inline`  | Use `<h1>` for screen titles, with optional inline style|
| Tab Navigation       | `.nav-tab-wrapper`, `.nav-tab` | For tabs (e.g., post/page screens)                   |
| Buttons              | `.button`, `.button-primary`, `.button-secondary`, `.button-link` | WordPress core button styling   |
| Form Table           | `.form-table`               | Settings fields, key for forms                          |
| Notices              | `.notice`, `.notice-success`, `.notice-error`, `.notice-info`, `.notice-warning`, `.is-dismissible` | Admin notices |
| Meta Box             | `.postbox`, `.hndle`, `.inside` | Structuring grouped or advanced settings               |
| List Table           | `.wp-list-table`, `.widefat`, `.striped`, `.column-*` | Data management tables  |
| Helper Classes       | `.screen-reader-text`, `.description`, `.alignleft`, `.alignright`, `.hidden` | Accessibility and layout      |

---

## 18. Frequently Used Code Snippets

### Enqueueing an Admin Style in a Child Theme

```php
function my_childtheme_admin_styles($hook) {
    wp_enqueue_style('child-admin-style', get_theme_file_uri('admin-style.css'), array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'my_childtheme_admin_styles');
```

### Wrapping a Custom Settings Page

```php
function my_child_settings_page_cb() {
    ?>
    <div class="wrap">
        <h1>Child Theme Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('my_child_settings_group');
            do_settings_sections('my-child-settings-slug');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
```

### Displaying a Success Notice the WordPress Way

```php
add_action('admin_notices', function () {
    echo '<div class="notice notice-success is-dismissible"><p>Options updated.</p></div>';
});
```

---

## 19. Advanced Topics: Responsive Design and Accessibility

**Responsive Breakpoints:**
- Desktop: min-width: 1200px
- Tablet: min-width: 782px
- Mobile: max-width: 600px

**Custom Responsive CSS Example:**
```css
@media (max-width: 782px) {
    .form-table th, .form-table td {
        display: block;
        width: 100%;
    }
    .wp-list-table th, .wp-list-table td {
        display: block;
        width: 100%;
    }
}
```

**Accessibility Reminders:**
- Add `.screen-reader-text` for hidden, accessible instructions.
- Do not use color as sole means to convey important information.
- Always associate `<label>` with inputs and use proper tab order.

---

## 20. Recommendations and Checklist

- **Always use existing admin CSS classes and markup patterns** for UI consistency and maintainability.
- **Enqueue custom admin CSS/JS only via `admin_enqueue_scripts`**, never globally.
- **Wrap pages in `.wrap`, use `h1` headings, and structure settings with `form-table`**.
- **Use `.notice` for feedback, `.button` classes for actions**.
- **For data, always use `WP_List_Table` or extend it**.
- **Implement Settings API for all options/storage**.
- **Test your UI with different admin color schemes, screen sizes, and accessibility tools**.
- **Refer to the [WordPress-Admin-Style](https://github.com/bueltge/WordPress-Admin-Style) plugin and [Bracketspace Dummy Dashboard](https://wpadmin.bracketspace.com) for living CSS and markup examples**.

---

## Conclusion

Reusing and extending the native WordPress admin UI—and adhering to its design language, forms, notices, tables, and utility classes—is **the most effective path to making your child theme or plugin both user-friendly and future-proof**. Not only does this strategy reduce your maintenance burden and risk of design regressions, but it also protects accessibility, responsiveness, and cross-version compatibility.

By following the code examples, patterns, and principles laid out here, you ensure every admin interface you build feels instantly familiar, professional, and robust to your users—while saving substantial development resources. For any further customizations, always reference and extend the core UI classes before turning to custom CSS or JavaScript.

Your Copilot or development agents can use these detailed instructions to refactor or optimize your WordPress admin screens to fully comply with—and leverage—the power of the native WordPress admin UI.

---
Here are anti-hallucination, anti-abstraction, anti-speculation instructions:
# Anti-Hallucination, Anti-Abstraction, and Clean-Code Prompt Engineering: Research-Driven Optimized Guidelines for Copilot Instructions in WordPress Child Theme Development

---

## Introduction

The proliferation of AI-assisted coding and prompt engineering, especially in the era of large-scale language models (LLMs) like Claude Sonnet 4, GPT-4o, and Microsoft Copilot, has dramatically accelerated the pace of software development. However, this acceleration comes at a cost: AI-generated responses, if not meticulously guided, can suffer from hallucinations (plausible but wrong statements), abstraction layers that obscure intent, and the rapid growth of “spaghetti code” that undermines project maintainability. These issues are especially critical in high-stakes environments, such as enterprise WordPress child theme integrations, where code quality, reliability, and administrative UI/UX consistency are non-negotiable.

This report synthesizes the latest research and industry best practices for anti-hallucination prompt engineering, anti-abstraction measures, clean modular coding, and critical-thinking frameworks in prompt creation. It applies these findings to the context of refactoring and optimizing a WordPress child theme repository using Copilot, focusing on how to formalize these guidelines into unified Copilot instructions. The report concludes with a reference-ready, actionable prompt for updating `.github/copilot-instructions.md`—providing clear, research-backed directives on truthfulness labeling, anti-hallucination strategies, code clarity, and critical-thinking processes for Copilot-driven refactoring.

---

## 1. Understanding the Core Risks: Hallucination, Abstraction, and Spaghetti Code in AI-Assisted WordPress Development

### 1.1 AI Hallucinations: Causes and Impact

Hallucinations in language model outputs are statements presented with confidence, yet are untrue, unsupported, or outright fabricated. In the WordPress plugin/theme context, this manifests in generated code that claims to implement hooks, options pages, or integration points that don’t exist, or generates documentation/content with inaccurate facts. As LLMs (like Claude Sonnet 4) are probabilistic engines trained on massive but imperfect corpora, they are error-prone when:

- Prompt instructions are too vague, open-ended, or lack anchoring in verifiable sources.
- The system is tasked outside its training scope (e.g., new or niche WordPress APIs).
- Critical UI/UX patterns (admin interfaces, settings, widgets) lack reference documentation or canonical examples.

The risk is compounded as LLMs prioritize fluency and coherence over truthfulness, making confident hallucinations a subtle but pernicious risk in mission-critical codebases.

### 1.2 Abstraction—The Anti-Pattern of Obscured Logic

Abstraction becomes an anti-pattern when structured prompts (or AI outputs) are so generic or high-level that they yield code with:

- Vague placeholders or “black-box” functions with unclear semantics or boundaries
- Overuse of generic helpers (“process_data”, “handle_request”) instead of domain-specific logic (e.g., WordPress-specific nomenclature or custom post type handlers)
- Non-semantic file/folder organization that fails to communicate responsibility and contract

In WordPress, where plugin/theme modularity is paramount and admin-side tools must be highly predictable, abstraction for abstraction’s sake derails maintainability, onboarding, and troubleshooting.

### 1.3 Spaghetti Code in AI-Generated Projects

AI-assisted tools are notorious for producing code that—unless explicitly architected—evolves into a web of interdependent, monolithic, and difficult-to-test structures. Spaghetti code is characterized by:

- Lack of architectural plan: Features/fixes inserted arbitrarily, spanning multiple concerns in a single file or function.
- Over-accommodation for edge cases: Excessive branching and nested conditionals in one location.
- Lack of modularity: Repeated patterns with no attempt to extract reusable functions/classes.
- Absence of refactor triggers: AI does not “feel” code quality disintegration over time, so incremental ad-hoc changes proliferate.

In WordPress development, where themes or plugins evolve rapidly and must integrate seamlessly with the core system, unchecked spaghetti code can render updates, bugfixes, and customizations slow and error-prone.

---

## 2. Research Synthesis: Best Practices in Anti-Hallucination, Anti-Abstraction, Clean-Code, and Critical-Thinking Prompt Engineering

### 2.1 Anti-Hallucination Strategies in Prompt Engineering

#### 2.1.1 Grounding and Retrieval-Augmented Generation (RAG)

Injecting real, up-to-date or domain-specific context into prompts (“grounding”) substantially reduces hallucination rates in LLM outputs. The technique of Retrieval-Augmented Generation (RAG) involves:

- Explicitly providing source context (e.g., WordPress codex links, theme documentation, code snippets) within the prompt.
- Instructing the model to use only the provided context for its response, labeling missing information explicitly (“I cannot answer based on the provided information.”).
- Citing sources as evidence where possible (using inline references, e.g., `[source: WP Codex - register_setting]`).

Empirical research demonstrates that RAG-based prompting, combined with output labeling, can cut hallucinations by up to 76% in high-stakes environments.

#### 2.1.2 Explicit Output Labeling

Prompt instructions must direct the AI to label each claim per its status:

- ✅ **Verified**: Supported by evidence (references, context, official docs)
- ⚠️ **Unverified**: Plausible but not currently backed by explicit data
- 🤔 **Speculation**: Educated guess or model-generated content with no factual/semantic grounding

Such labeling increases reviewer trust, boosts downstream auditability, and allows automated or human-in-the-loop curation of outputs for inclusion in production code or documentation.

#### 2.1.3 Chain-of-Verification (CoVe) and Chain-of-Thought (CoT) Reasoning

Leading frameworks, such as CoVe and CoT, instruct the AI to:

- **First** generate a stepwise rationale for each answer or code suggestion (CoT), exposing the logic trail.
- **Then** break down the answer into verifiable sub-claims, iteratively check consistency with context or external references (CoVe).
- **Finally** label any unsubstantiated/inconsistent outputs for further review or correction.

This method not only reduces error rates but also provides structured output patterns that are easily parsed or validated programmatically.

#### 2.1.4 Output Abandonment and Fallbacks

If adequate grounding context is not available, prompts must instruct the model to abstain from guessing, returning “Information not available” or “Speculation; verify before use” as required fallback outputs. This is critical in highly dynamic administrative interfaces or when newly published WordPress APIs aren’t represented in the model’s training data.

### 2.2 Anti-Abstraction and Clarity Rules

#### 2.2.1 Ban Vague Placeholders

Prompts must forbid the use of non-semantic code blocks, ambiguous helper names, or “magic” variables. Instead, all abstractions must be both justified and named/described in domain terms, e.g., use `update_option_admin_email()` instead of `handle_admin_action()`.

#### 2.2.2 Explicit Module Boundaries

Divide complex admin features (e.g., dashboard widgets, settings pages, custom post type controls) into clearly named, single-responsibility modules or files. Every AI-generated function/class must:

- Live in a semantically meaningful location (folder/file)
- Be documented with purpose, boundaries, and expected input/output contracts
- Reference dependencies via domain-specific description, not hand-waving comments like “// does something with settings”

#### 2.2.3 No Black-Box/“DoEverything” Functions

Forbid generation of giant procedural code blobs with no logical partition—require modular decomposition, even if “just for now” according to the DRY principle.

### 2.3 Clean-Code/Modular/DRY Structure Principles

#### 2.3.1 Modular Project Architecture

AI code assistants should be prompted to:

- Propose/refactor a high-level file and folder structure before implementation
- Map each component (settings page, widget, customizer section, etc.) to an isolated module/class/file
- Use modern, idiomatic WordPress/JavaScript/PHP/REST architecture (avoid deprecated practices)

#### 2.3.2 DRY and Reusability

All utilities, hooks, and shared logic should be refactored into reusable functions or classes with stable interfaces. Repetition of code must be flagged for review and consolidation.

#### 2.3.3 Documentation as First-Class Citizen

Each function, module, and file must carry concise, context-rich doc comments explaining:

- Purpose and contracts
- Key dependencies or assumptions
- Versioning or contextual caveats (e.g., when a function should or should not be used)

#### 2.3.4 Iterative Refactoring Support

Prompt Copilot to treat code generation as an iterative process—generate initial versions, review for anti-patterns (e.g., excessive nesting, unclear boundaries), and prompt for continuous refactoring.

### 2.4 Critical-Thinking, Verification and Chain-of-Thought Inclusion

#### 2.4.1 Stepwise Verification

Instruct the model to always perform a verification pass:

- Break its output into atomic claims or code achievements
- Cross-verify each element with the provided context or retrieved docs
- Clearly note claims or steps that remain unverified

#### 2.4.2 Uncertainty Quantification

Instruct Copilot to surface uncertainty via annotations or comments:

- If model confidence is low or if there is a possibility of hallucination, insert explicit TODOs or Reveal blocks in code/comments for human review
- Use both epistemic (model-based) and aleatoric (data-based) uncertainty techniques where supported

#### 2.4.3 Chain-of-Thought Output Format

Instruct Copilot to optionally output step-by-step reasoning (explanation first, output second), especially for complex tasks, code reviews, or major architectural changes.

---

## 3. Unified, Consistent Admin UI/UX for WordPress Integrations

A recurring challenge in custom WordPress child themes (especially with multiple admin tools or extension points) is the proliferation of inconsistent UI elements, leading to fragmentation and cognitive load for users. Research and plugin/UI theme vendors recommend the following:

### 3.1 WordPress-Only Default UI/Style/Layout

- **Mandate** use of native WordPress admin design patterns and CSS classes (e.g., `wp-admin`, `wp-list-table`, `submit-box`, `notice-success`) for all admin panels, tool integrations, and custom settings pages.
- Forbid introduction of novel, non-standard UI kit elements unless justified by context or requirements.
- Require use of built-in UI components (e.g., metaboxes, screen options, dashboard widgets) whenever possible.
- Prompt Copilot to audit all admin-side tools for UI consistency at each refactoring checkpoint.

---


## 5. Prompt Files: When to Create, Remove, or Audit

Based on recent documentation from GitHub and Microsoft, prompt files (`.prompt.md`) are supplementary, highly localized guides for customized, one-off tasks (such as onboarding, special compliance, or function-specific code generation).

**Guidelines**:

- Remove generic, redundant, or outdated prompt files that are:
    - Addressed by the core `copilot-instructions.md` or a path-specific `.instructions.md`
    - Specific to scenarios or modules no longer present (e.g., old plugins/settings pages)
    - Encouraging inconsistent code style, naming conventions, or disabilities compliance

- **Create/select prompt files** only for:
    - Temporary onboarding for new major features with unique requirements not encapsulated in existing Copilot instructions
    - Specialized workflows (e.g., first-run migrations, database seeding scripts) that require detailed, local prompting
    - Per-module tasks where context is both unique and not well-served by general instructions

**Before adding prompt files**, ask:

- Is the context too narrow/specific for inclusion in main instructions?
- Is this a one-off/process onboarding script, not a persistent coding standard?
- Will the instructions become obsolete after module launch or refactoring?

---

## 6. Synthesis: Actionable Guidelines for Copilot Instructions

The following actionable guidelines integrate the findings above:

**Truthfulness and Evidence:**
- Require the model to label all claims as one of: ✅ Verified, ⚠️ Unverified, or 🤔 Speculation
- For any code, comment, or documentation not directly supported by known context, label as unverified or speculative
- Always cite verifiable reference (e.g., comment: `// ✅ Verified: See WP Codex on register_setting`)
- If no citation is possible, clearly state "Information not available" in comments, or flag as TODO

**Anti-Hallucination:**
- Only generate code/outputs based on provided or referenced context (“Use only content in the repo, core docs, or explicit instructions”)
- Use RAG techniques where possible; do not invent settings, hooks, or features not found in the repo or official documentation
- For settings, APIs, or integrations with unknown behavior, output verification comments and do not invent plausible-sounding but non-existent features

**Anti-Abstraction:**
- No generic function or variable placeholders (e.g., no `process_data()` unless context is clear and documented)
- All abstractions must be domain-justified: explain what, why, and boundaries in code comments
- Modularize logic: do not allow long, procedural, monolithic code dumps—each logical function/class/module must be isolated and documented
- Explicitly define module boundaries, dependencies, and point of extension

**Anti-Spaghetti-Code:**
- Propose clean architecture/file/folder structure before generating implementation code
- Enforce DRY: any repeated logic must be refactored into shared helpers, with documentation
- Break large functions into small, composable, reusable units; prompt for refactoring if code length or complexity grows
- Require documentation for each function’s responsibility, inputs, and outputs

**Critical Thinking and Verification:**
- Mandate step-by-step, “chain of thought” explanations for non-trivial logic, code generation, or configuration tasks
- Summary reasoning blocks must precede outputs, especially for major architectural or refactor suggestions
- Always perform a verification pass over generated output, noting any uncertainty or points for human review

---

## 7. Implementation: Unified Copilot Instructions Prompt

Finally, as per the user requirements, we deliver a four-section, ready-to-use prompt that encapsulates this research, compatible with VS Code Copilot (Claude Sonnet 4), and targeting `.github/copilot-instructions.md` integration.

---

# Persona

You are an AI expert specializing in prompt engineering for VS Code Copilot (Claude Sonnet 4), with a focus on anti-hallucination, anti-abstraction, truthfulness, and clean, modular code for WordPress child theme projects. Your approach systematically eliminates ambiguous abstractions and unreliable code patterns, ensuring that every AI output is evidence-based, modular, and auditable. You are committed to rigorous critical thinking, code clarity, and the consistent application of WordPress-native admin UI/UX standards across all admin-side integrations. Your primary goal is to maximize reliability, maintainability, and coherence, while minimizing hallucinations, spaghetti code, and speculative responses.

---

# Task

1. Refactor and optimize the current WordPress child theme repository and its admin integrations according to the latest anti-hallucination, anti-abstraction, and anti-spaghetti code best practices.
2. Recreate the `.github/copilot-instructions.md` file, merging in the synthesized guidelines below without overwriting existing relevant content.  
   - Instruct Copilot to ensure all responses are truthful, evidence-based, and clearly labeled (e.g., ✅ Verified, ⚠️ Unverified, 🤔 Speculation).
   - Explicitly direct Copilot to avoid hallucinations, flag any uncertainty, and not invent facts or features absent from the provided context.
   - Forbid vague placeholders or generic abstractions. Mandate clear, domain-specific function/module naming, and precise boundaries in all output.
   - Demand modular, DRY, and reusable code structures; prompt for iterative refactoring whenever code length or complexity grows.
   - Require stepwise chain-of-thought reasoning, verification, and documentation for all significant outputs. Each claim, design choice, and code section must be critically examined, with uncertainty surfaced and flagged for human review.
3. Remove prompt files unless they serve a contextual, setup-specific, or configuration-driven need. Before creating a new prompt file, enforce an audit:  
   - Only create prompt files for scenarios that cannot be addressed by main instructions, for per-module onboarding, or for one-off setup/configuration steps.
   - For each prompt file, clearly state its scope, intended context, and expected removal date once setup is complete.
4. Implement and enforce a single, WordPress-native admin UI style/layout for **all** admin-side integrations:  
   - Require that all custom admin pages, settings, and tools adopt only core WordPress UI patterns, classes, and design conventions.
   - Forbid introduction of nonstandard UI components or divergent interaction models.

---

# Context

This project is a WordPress child theme with multiple custom settings pages, admin tools, and UI integrations. The codebase has accumulated legacy, inconsistent UI/code structures, prompt files of unclear value, and documentation that sometimes blurs the line between fact and speculation. The aim is to ensure that the refactored repository is:  

- Externally auditable: All claims, code, and configurations are labeled and grounded in source context.
- Hallucination-resistant: Copilot does not output or propagate unsubstantiated or invented logic.
- Modular and maintainable: Each major feature or tool exists in an isolated file/module, with clear documentation and documented extension points.
- UI-consistent: Every admin feature leverages **only** the default WordPress admin UI classes/components for a uniform experience, with no custom UI frameworks.
- Easy to onboard: Developers can quickly grasp project conventions from the `.github/copilot-instructions.md` and do not have to rely on ad hoc, duplicated prompt files.
- Critically rigorous: Claims, code, and design choices are always subject to chain-of-thought verification and explicit uncertainty annotation.

---

# Format

Output the upgraded Copilot instructions prompt as a single document using the following sections, merging additions with existing content where appropriate and marking the new anti-hallucination, anti-abstraction, truthfulness, and clean-code rules clearly:

1. **Persona:**  
   - Who Copilot should act as (AI critical-thinker, anti-hallucination, modular code enforcer, WordPress admin UI/UX validator).

2. **Task:**  
   - What refactoring and config goals to achieve, summarizing evidence rules, hallucination guards, modularity, critical thinking, and prompt file audit/creation standards.

3. **Context:**  
   - The nature of the codebase and its history of legacy code, abstractions, speculation, and inconsistent UI/code structures.

4. **Format:**  
   - Specific rules for filename placement, labeling of claims (✅, ⚠️, 🤔), prohibition of abstraction, encouragement of modular/DRY architecture, requirements for reasoning, chain-of-thought verification, and instructions for prompt file lifecycle management.
   - Direction to preserve and clearly separate newly added rules within the file using visible Markdown headings.

---

## Conclusion

By applying the synthesized, research-based prompt engineering and code structuring best practices above, your Copilot-assisted WordPress child theme development will achieve high standards of truthfulness, maintainability, and UI/UX coherence. The four-section prompt format below is ready to be pasted into `.github/copilot-instructions.md`—marking a new era of reliable, anti-hallucination, audit-friendly automation for your WordPress projects.

---

## Unified Prompt for `.github/copilot-instructions.md`

---

**Persona**

You are an AI coding assistant for this repository, specializing in advanced prompt engineering for Copilot (Claude Sonnet 4). Your responsibilities include:
- Systematically eliminating hallucinations, vagueness, and over-abstraction in code, documentation, and project structure.
- Enforcing evidence-based output, explicit labeling of claim certainty, and comprehensive modularity and DRY (Don’t Repeat Yourself) principles.
- Applying rigorous, stepwise critical thinking, verification, and chain-of-thought reasoning to all outputs.
- Guaranteeing all WordPress admin interface enhancements maintain a single, canonical UI/UX style based on native WordPress admin patterns and components only.

---

**Task**

- Refactor and unify the repository codebase according to the following best practices:
    - Classify all claims, code, and comments as ✅ Verified (evidence-backed), ⚠️ Unverified (no source or only plausible), or 🤔 Speculation (speculative/invented or outside core context).
    - For any content not strictly supported by codebase context or official documentation, flag/label it and refrain from fabricating plausible details.
    - Apply robust anti-hallucination prompting: do not invent WordPress hooks, options, UI conventions, or features unless explicitly referenced.
    - Prohibit vague or generic abstractions; every function, variable, and module must leverage explicit, domain-relevant naming and doc comments.
    - Apply modular design: all new/refactored logic should be split into single-responsibility modules/files (e.g., settings, widgets, utils, customizer, admin UI).
    - Demand chain-of-thought explanations for all complex logic, code reviews, or architectural decisions; always document reasoning and verification steps.
    - Enforce iterative code review and refactoring: break large, complex code into smaller reusable parts.
    - Remove obsolete or redundant prompt files. Only create prompt files for localized, non-instructional, module onboarding or one-off configuration use—clearly documenting their scope and removal timeline.
    - Require that all admin pages, tools, and settings leverage **only** WordPress core UI classes/components for fully unified admin-side appearance and behavior.

---

**Context**

This repository is a complex WordPress child theme with legacy abstractions, a mix of generic and specific prompt files, and UI/UX inconsistencies across admin-side features. Past AI-generated contributions have suffered from hallucinations, ambiguous abstractions, spaghetti code, and speculative outputs not clearly labeled for downstream review or correction. The goal is to transition to a maintainable, auditable, and UI/UX-consistent architecture, where every code and documentation artifact is clear, modular, evidence-based, and trustworthy.

---

**Format**

- Place this file at `.github/copilot-instructions.md`.
- Clearly mark rules, guidelines, and instructions below using # or ## Markdown headings.
- At the top of every generated file/comment, indicate the evidence status for each claim:  
   - ✅ Verified  
   - ⚠️ Unverified  
   - 🤔 Speculation
- Flag all outputs lacking direct evidence or documentation with appropriate labels.
- DO NOT use generic function/variable placeholders. All abstractions must be explained, named meaningfully, and justified for this domain.
- Every function/module must:
    - Live in a semantically relevant, single-responsibility file.
    - Be documented for intent, contracts, dependencies, and context.
    - Reference or link evidence (e.g., [See WP Codex: register_setting]).
- Modularize all code; break large functions into DRY, reusable components.
- Chain-of-thought reasoning is mandatory for new features, logic reviews, and cross-module integrations—either in comments or as Markdown planning/output blocks.
- Before adding a prompt file, consider whether its purpose exceeds the scope of this instructions file. If needed, state its purpose, scope, removal date, and audit it for obsolescence post-setup.
- For all admin integrations:
    - Use only official WordPress admin UI patterns, styles, and markup; never invent new admin UI paradigms.
    - Remove any legacy, divergent, or custom admin UI frameworks from the theme.

Clearly delimit new “anti-hallucination”, “anti-abstraction”, and “critical-thinking” directives within this file—making their presence and rationale explicit for all AI and human code contributors.

---

**End of Instructions**