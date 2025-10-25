<?php
/**
 * Main Theme Class
 *
 * Core orchestrator that loads dependencies, sets up internationalization,
 * and defines admin/public hooks.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main LoveTravelChild Class
 *
 * Coordinates theme initialization, dependency loading, and hook registration.
 */
class LoveTravelChild {

	/**
	 * The loader that maintains and registers all hooks.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    LoveTravelChildLoader $loader Hook loader instance.
	 */
	protected $loader;

	/**
	 * Theme identifier.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string $themeName Theme slug.
	 */
	protected $themeName;

	/**
	 * Theme version.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string $version Theme version.
	 */
	protected $version;

	/**
	 * Initialize the theme.
	 *
	 * Sets version, loads dependencies, sets locale, defines admin/public hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->version   = defined( 'LOVETRAVEL_CHILD_VERSION' ) ? LOVETRAVEL_CHILD_VERSION : '2.0.0';
		$this->themeName = 'lovetravel-child';

		$this->loadDependencies();
		$this->setLocale();
		$this->defineTaxonomyHooks();
		$this->defineMetaboxHooks();
		$this->defineAdminHooks();
		$this->definePublicHooks();
		$this->defineElementorHooks();
		$this->defineTemplateImporterHooks();
	}

	/**
	 * Load required dependencies.
	 *
	 * Includes loader, i18n, admin, public, and helper files.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function loadDependencies() {
		// Core includes
		require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-loader.php';
		require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-i18n.php';
		require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-taxonomy-manager.php';
		require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-favicon.php';
		require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-admin-notices.php';
		require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-elementor-template-importer.php';
		// DEPRECATED: Legacy widget extensions (replaced with standalone widgets in Phase 2)
		// require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-elementor-search-widget-extension.php';
		// require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child-elementor-packages-widget-extension.php';
		require_once LOVETRAVEL_CHILD_PATH . '/includes/helpers.php';
		require_once LOVETRAVEL_CHILD_PATH . '/includes/favicon-helpers.php';

		// Elementor manager (loads widgets, metaboxes, dynamic tags)
		require_once LOVETRAVEL_CHILD_PATH . '/elementor/class-lovetravel-child-elementor-manager.php';

		// Admin class
		require_once LOVETRAVEL_CHILD_PATH . '/admin/class-lovetravel-child-admin.php';

		// Public class
		require_once LOVETRAVEL_CHILD_PATH . '/public/class-lovetravel-child-public.php';

		// Initialize loader
		$this->loader = new LoveTravelChildLoader();
	}

	/**
	 * Define the locale for internationalization.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function setLocale() {
		$themeI18n = new LoveTravelChildI18n();
		$this->loader->addAction( 'after_setup_theme', $themeI18n, 'loadThemeTextdomain' );
	}

	/**
	 * Register all taxonomy-related hooks.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function defineTaxonomyHooks() {
		$taxonomyManager = new LoveTravelChildTaxonomyManager();

		// Register custom taxonomies (priority 9 - before nd-travel at 10)
		$this->loader->addAction( 'init', $taxonomyManager, 'registerTaxonomies', 9 );

		// Modify existing taxonomy labels (priority 20 - override plugin defaults)
		$this->loader->addFilter( 'register_taxonomy_args', $taxonomyManager, 'modifyTaxonomyLabels', 20, 2 );

		// Flush rewrite rules on theme activation
		$this->loader->addAction( 'after_switch_theme', $taxonomyManager, 'flushRewriteRules' );
	}

	/**
	 * Register typology metabox hooks.
	 *
	 * DEPRECATED: Metabox now managed by Elementor Manager.
	 * Kept for backwards compatibility during migration.
	 *
	 * @since  2.0.0
	 * @access private
	 * @deprecated 2.1.0 Use Elementor Manager instead
	 */
	private function defineMetaboxHooks() {
		// Metabox registration moved to Elementor Manager
		// This method intentionally left empty during migration
	}

	/**
	 * Register all admin-specific hooks.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function defineAdminHooks() {
		$themeAdmin = new LoveTravelChildAdmin( $this->getThemeName(), $this->getVersion() );

		// Enqueue admin assets (priority 20 to override parent/plugins)
		$this->loader->addAction( 'admin_enqueue_scripts', $themeAdmin, 'enqueueStyles', 20 );
		$this->loader->addAction( 'admin_enqueue_scripts', $themeAdmin, 'enqueueScripts', 20 );

		// Register admin menu
		$this->loader->addAction( 'admin_menu', $themeAdmin, 'addAdminMenu' );

		// Register settings
		$this->loader->addAction( 'admin_init', $themeAdmin, 'registerSettings' );

		// AJAX handler for manual template import
		$this->loader->addAction( 'wp_ajax_lovetravel_child_import_templates', $themeAdmin, 'ajaxImportTemplates' );
	}

	/**
	 * Register all public-facing hooks.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function definePublicHooks() {
		$themePublic = new LoveTravelChildPublic( $this->getThemeName(), $this->getVersion() );
		$favicon     = new LoveTravelChildFavicon();

		// Enqueue public assets (priority 20 to override parent/plugins)
		$this->loader->addAction( 'wp_enqueue_scripts', $themePublic, 'enqueueStyles', 20 );
		$this->loader->addAction( 'wp_enqueue_scripts', $themePublic, 'enqueueScripts', 20 );

		// Favicon output (priority 98 - before WordPress wp_site_icon at 99)
		$this->loader->addAction( 'wp_head', $favicon, 'outputFavicon', 98 );
		$this->loader->addAction( 'wp_head', $favicon, 'outputThemeColor', 98 );
		$this->loader->addAction( 'login_head', $favicon, 'outputFavicon', 98 );
		$this->loader->addAction( 'login_head', $favicon, 'outputThemeColor', 98 );
	}

	/**
	 * Register Elementor integration hooks.
	 *
	 * Uses centralized Elementor Manager for widgets, metaboxes, and dynamic tags.
	 * Also registers legacy widget extensions (Search, Packages) - to be migrated.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function defineElementorHooks() {
		// Only register hooks if Elementor is active
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Elementor Manager (NEW: centralized widget/metabox management)
		$elementorManager = new LoveTravelChild_Elementor_Manager( $this->getThemeName(), $this->getVersion() );

		// Register post meta fields with REST API (for Dynamic Tags)
		$this->loader->addAction(
			'init',
			$elementorManager,
			'register_post_meta'
		);

		// Register Dynamic Tags group
		$this->loader->addAction(
			'elementor/dynamic_tags/register',
			$elementorManager,
			'register_dynamic_tags_group'
		);

		// Register Dynamic Tags
		$this->loader->addAction(
			'elementor/dynamic_tags/register',
			$elementorManager,
			'register_dynamic_tags'
		);

		// Register custom widget category
		$this->loader->addAction(
			'elementor/elements/categories_registered',
			$elementorManager,
			'register_category'
		);

		// Register custom widgets
		$this->loader->addAction(
			'elementor/widgets/register',
			$elementorManager,
			'register_widgets'
		);

		// Register metaboxes
		$this->loader->addAction(
			'add_meta_boxes',
			$elementorManager,
			'register_metaboxes'
		);

		// Enqueue plugin CSS in Elementor editor (fixes preview issues)
		$this->loader->addAction(
			'elementor/editor/after_enqueue_styles',
			$elementorManager,
			'enqueue_editor_styles'
		);

		// Also enqueue in preview mode
		$this->loader->addAction(
			'elementor/preview/enqueue_styles',
			$elementorManager,
			'enqueue_editor_styles'
		);

		/*
		 * DEPRECATED: Legacy widget extensions (October 25, 2025)
		 *
		 * Replaced with standalone widgets in Phase 2:
		 * - Search Widget Extension → class-search-widget.php
		 * - Packages Widget Extension → class-packages-widget.php
		 *
		 * Code preserved below for reference but commented out.
		 * Remove completely after verifying no pages use legacy widgets.
		 */

		/*
		// Search Widget Extension (LEGACY: DEPRECATED in v2.2.0)
		$searchWidgetExtension = new LoveTravelChildElementorSearchWidgetExtension();

		// Inject Month taxonomy controls after Min Ages section
		$this->loader->addAction(
			'elementor/element/Search/content_section_minages/after_section_end',
			$searchWidgetExtension,
			'addMonthControls',
			10,
			2
		);

		// Modify Search widget render output to include Month taxonomy
		$this->loader->addFilter(
			'elementor/widget/render_content',
			$searchWidgetExtension,
			'modifySearchWidgetRender',
			10,
			2
		);

		// Packages Widget Extension (LEGACY: DEPRECATED in v2.2.0)
		if ( get_option( 'lovetravel_child_enable_custom_packages_layout', 0 ) ) {
			$packagesWidgetExtension = new LoveTravelChildElementorPackagesWidgetExtension(
				$this->getThemeName(),
				$this->getVersion()
			);

			// Run a one-time migration on admin_init
			$this->loader->addAction(
				'admin_init',
				$packagesWidgetExtension,
				'migrateSavedLayouts',
				1
			);

			// Frontend: Replace default render when custom layout enabled globally
			$this->loader->addFilter(
				'elementor/frontend/widget/should_render',
				$packagesWidgetExtension,
				'shouldRenderWidget',
				10,
				2
			);

			// Editor: Intercept render content when custom layout enabled globally
			$this->loader->addFilter(
				'elementor/widget/render_content',
				$packagesWidgetExtension,
				'interceptRender',
				10,
				2
			);
		}
		*/
	}

	/**
	 * Register template importer and admin notices hooks.
	 *
	 * @since  2.0.0
	 * @access private
	 */
	private function defineTemplateImporterHooks() {
		$adminNotices     = new LoveTravelChildAdminNotices();
		$templateImporter = new LoveTravelChildElementorTemplateImporter( $adminNotices );

		// Display admin notices
		$this->loader->addAction( 'admin_notices', $adminNotices, 'displayNotices' );
		
		// Enqueue notice scripts
		$this->loader->addAction( 'admin_enqueue_scripts', $adminNotices, 'enqueueScripts' );
		
		// Handle AJAX notice dismissal
		$this->loader->addAction( 'wp_ajax_lovetravel_child_dismiss_notice', $adminNotices, 'dismissNotice' );

		// Auto-import templates on theme activation
		$this->loader->addAction( 'after_switch_theme', $templateImporter, 'autoImport' );
	}

	/**
	 * Execute all registered hooks.
	 *
	 * @since 2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Get theme name.
	 *
	 * @since  2.0.0
	 * @return string Theme identifier.
	 */
	public function getThemeName() {
		return $this->themeName;
	}

	/**
	 * Get theme version.
	 *
	 * @since  2.0.0
	 * @return string Theme version.
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Get loader reference.
	 *
	 * @since  2.0.0
	 * @return LoveTravelChildLoader Loader instance.
	 */
	public function getLoader() {
		return $this->loader;
	}
}
