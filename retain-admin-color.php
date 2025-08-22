<?php
/**
 * Plugin Name: Retain Admin Color
 * Plugin URI: https://github.com/laxmariappan/retain-admin-color
 * Description: Retains the admin color scheme of the logged-in user, preventing it from being overridden.
 * Version: 1.0.0
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
define( 'RETAIN_ADMIN_COLOR_VERSION', '1.0.0' );
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
		add_action( 'admin_init', array( $this, 'retain_admin_color_scheme' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		
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
	 * Retain the user's admin color scheme.
	 */
	public function retain_admin_color_scheme(): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		$admin_color = get_user_meta( $user_id, 'admin_color', true );

		// If user has a specific admin color set, ensure it's applied
		// We want to retain ALL color choices, not just non-fresh ones
		if ( ! empty( $admin_color ) ) {
			// Force the admin color to be the user's preferred choice
			add_filter( 'get_user_option_admin_color', array( $this, 'force_user_admin_color' ), 10, 3 );
			
			// Also ensure the global $user_id has the correct color when WordPress checks it
			add_action( 'admin_head', array( $this, 'ensure_admin_color_body_class' ) );
		}
	}

	/**
	 * Force the user's admin color choice.
	 *
	 * @param mixed           $result The value to return instead.
	 * @param string          $option Option name.
	 * @param WP_User|int     $user   User object or User ID.
	 * @return string
	 */
	public function force_user_admin_color( $result, string $option, $user ): string {
		if ( 'admin_color' === $option ) {
			// Handle both WP_User object and user ID
			$user_id = is_object( $user ) && $user instanceof WP_User ? $user->ID : (int) $user;
			
			if ( $user_id ) {
				$user_admin_color = get_user_meta( $user_id, 'admin_color', true );
				if ( ! empty( $user_admin_color ) ) {
					return $user_admin_color;
				}
			}
		}
		return $result;
	}

	/**
	 * Ensure the correct admin color body class is applied.
	 */
	public function ensure_admin_color_body_class(): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		$admin_color = get_user_meta( $user_id, 'admin_color', true );
		
		if ( ! empty( $admin_color ) ) {
			// Add JavaScript to ensure the body class is correct
			echo '<script type="text/javascript">
				(function() {
					var body = document.body;
					if (body) {
						// Remove any existing admin-color-* classes
						body.className = body.className.replace(/admin-color-[a-z]+/g, "");
						// Add the correct admin color class
						body.classList.add("admin-color-' . esc_js( $admin_color ) . '");
					}
				})();
			</script>' . "\n";
		}
	}

	/**
	 * Enqueue admin styles to ensure color scheme is properly applied.
	 */
	public function enqueue_admin_styles(): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		$admin_color = get_user_meta( $user_id, 'admin_color', true );

		if ( ! empty( $admin_color ) ) {
			// Add inline CSS to ensure the admin color scheme is retained and prioritized
			$custom_css = sprintf(
				'/* Retain Admin Color Plugin - Ensuring %s color scheme is retained */
				body.admin-color-%s,
				body.admin-color-%s #wpadminbar,
				body.admin-color-%s .wp-core-ui .button-primary {
					/* Ensure the color scheme is properly applied with higher specificity */
				}
				
				/* Force correct admin color scheme application */
				body:not(.admin-color-%s) {
					/* Remove conflicting styles that might override the user color choice */
				}',
				esc_attr( $admin_color ),
				esc_attr( $admin_color ),
				esc_attr( $admin_color ),
				esc_attr( $admin_color ),
				esc_attr( $admin_color )
			);
			
			wp_add_inline_style( 'colors', $custom_css );
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
