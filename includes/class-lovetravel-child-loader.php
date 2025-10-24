<?php
/**
 * Hook Loader
 *
 * Registers all actions and filters for the theme.
 * Maintains hooks with explicit priority control for overriding parent theme and plugins.
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
 * Hook Loader Class
 *
 * Manages the registration of all theme hooks (actions and filters) with WordPress.
 * Provides explicit priority control crucial for child theme overrides.
 */
class LoveTravelChildLoader {

	/**
	 * Array of actions registered with WordPress.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    array $actions Actions to register.
	 */
	protected $actions;

	/**
	 * Array of filters registered with WordPress.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    array $filters Filters to register.
	 */
	protected $filters;

	/**
	 * Initialize the collections.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection.
	 *
	 * @since 2.0.0
	 * @param string $hook          Hook name.
	 * @param object $component     Object instance.
	 * @param string $callback      Method name.
	 * @param int    $priority      Priority (default 10, child theme uses 20 for overrides).
	 * @param int    $accepted_args Number of arguments (default 1).
	 */
	public function addAction( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection.
	 *
	 * @since 2.0.0
	 * @param string $hook          Hook name.
	 * @param object $component     Object instance.
	 * @param string $callback      Method name.
	 * @param int    $priority      Priority (default 10, child theme uses 20 for overrides).
	 * @param int    $accepted_args Number of arguments (default 1).
	 */
	public function addFilter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Utility to register hooks into a single collection.
	 *
	 * @since  2.0.0
	 * @access private
	 * @param  array  $hooks         Collection of hooks.
	 * @param  string $hook          Hook name.
	 * @param  object $component     Object instance.
	 * @param  string $callback      Method name.
	 * @param  int    $priority      Priority.
	 * @param  int    $accepted_args Number of arguments.
	 * @return array  Updated collection.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register all hooks with WordPress.
	 *
	 * @since 2.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}
}
