<?php
/**
 * Plugin Name: Retain Admin Color
 * Plugin URI: https://github.com/laxmariappan/retain-admin-color
 * Description: Retains the admin color scheme of the logged-in user on both admin and frontend areas, preventing it from being overridden.
 * Version: 1.0.1
 * Author: Lax Mariappan
 * Author URI: https://laxmariappan.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: retain-admin-color
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 *
 * @package RetainAdminColor
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'RETAIN_ADMIN_COLOR_VERSION', '1.0.1' );
define( 'RETAIN_ADMIN_COLOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RETAIN_ADMIN_COLOR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'RETAIN_ADMIN_COLOR_PLUGIN_FILE', __FILE__ );

/**
 * Main plugin class
 */
class Retain_Admin_Color {

	/**
	 * The single instance of the class.
	 *
	 * @var Retain_Admin_Color
	 */
	private static $instance = null;

	/**
	 * Get the single instance of the class.
	 *
	 * @return Retain_Admin_Color
	 */
	public static function get_instance(): Retain_Admin_Color {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks(): void {
 		add_action( 'init', array( $this, 'load_textdomain' ) );



        add_action('wp_enqueue_scripts', array($this, 'add_admin_bar_color_style')); 		
		// Activation and deactivation hooks
		register_activation_hook( RETAIN_ADMIN_COLOR_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( RETAIN_ADMIN_COLOR_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Load plugin textdomain for internationalization.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'retain-admin-color',
			false,
			dirname( plugin_basename( RETAIN_ADMIN_COLOR_PLUGIN_FILE ) ) . '/languages'
		);
	}

	/**
	 * Add admin color to frontend.
	 */
	public function add_admin_bar_color_style(): void {
		if ( is_admin() ) {
			return; // Only for frontend
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return; // Only for logged-in users
		}

		$admin_color = get_user_meta( $user_id, 'admin_color', true );
        $primary_color = '#23282d'; // Default fallback color
            
		if ( ! empty( $admin_color ) ) {
			// Hardcoded admin color values (primary color for each scheme)
			$admin_colors = array(
				'fresh'     => '#0073aa', // Default
				'light'     => '#e5e5e5',
				'blue'      => '#52accc',
				'coffee'    => '#59524c',
				'ectoplasm' => '#523f6d',
				'midnight'  => '#e14d43',
				'ocean'     => '#738e96',
				'sunrise'   => '#dd823b',
			);
			if ( isset( $admin_colors[ $admin_color ] ) ) {
				$primary_color = $admin_colors[ $admin_color ];
			}
            wp_register_style('admin-bar-color', false);
            wp_enqueue_style('admin-bar-color');
            // Add inline style using WordPress API
            $custom_css = '#wpadminbar { background: ' . esc_attr($primary_color) . ' !important; }';
			wp_add_inline_style( 'admin-bar-color', $custom_css );

         }
     }

	/**
	 * Plugin activation hook.
	 */
	public function activate(): void {
		// Nothing to do on activation for this simple plugin
		do_action( 'retain_admin_color_activated' );
	}

	/**
	 * Plugin deactivation hook.
	 */
	public function deactivate(): void {
		// Nothing to do on deactivation for this simple plugin
		do_action( 'retain_admin_color_deactivated' );
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return RETAIN_ADMIN_COLOR_VERSION;
	}
}

/**
 * Initialize the plugin.
 */
function retain_admin_color_init(): Retain_Admin_Color {
 	return Retain_Admin_Color::get_instance();
}

// Initialize the plugin
retain_admin_color_init();
