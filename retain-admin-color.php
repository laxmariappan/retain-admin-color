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
		error_log( 'Retain Admin Color: Plugin constructor called' );
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks(): void {
		error_log( 'Retain Admin Color: Initializing hooks' );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		
		// FOR FRONTEND - Apply admin color scheme to frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_admin_styles' ) );
		add_action( 'wp_head', array( $this, 'add_frontend_admin_color_body_class' ) );
		
		// FOR ADMIN - Keep the admin area working too
		add_filter( 'get_user_option_admin_color', array( $this, 'force_user_admin_color' ), 10, 3 );
		add_filter( 'pre_get_user_option_admin_color', array( $this, 'pre_force_user_admin_color' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		
		// Debug both frontend and admin
		add_action( 'init', array( $this, 'debug_init' ) );
		add_action( 'wp_head', array( $this, 'debug_frontend' ) );
		add_action( 'admin_init', array( $this, 'debug_admin_init' ) );
		
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
	 * Debug function for init hook.
	 */
	public function debug_init(): void {
		$user_id = get_current_user_id();
		$admin_color = '';
		if ( $user_id ) {
			$admin_color = get_user_meta( $user_id, 'admin_color', true );
		}
		$context = is_admin() ? 'ADMIN' : 'FRONTEND';
		error_log( 'Retain Admin Color: init fired in ' . $context . ' - User ID: ' . $user_id . ' Admin Color: ' . $admin_color );
	}

	/**
	 * Debug function for frontend.
	 */
	public function debug_frontend(): void {
		$user_id = get_current_user_id();
		$admin_color = '';
		if ( $user_id ) {
			$admin_color = get_user_meta( $user_id, 'admin_color', true );
		}
		error_log( 'Retain Admin Color: FRONTEND wp_head fired - User ID: ' . $user_id . ' Admin Color: ' . $admin_color );
	}

	/**
	 * Debug function to check if admin_init fires.
	 */
	public function debug_admin_init(): void {
		$user_id = get_current_user_id();
		$admin_color = get_user_meta( $user_id, 'admin_color', true );
		error_log( 'Retain Admin Color: admin_init fired - User ID: ' . $user_id . ' Admin Color: ' . $admin_color );
		
		// Test what get_user_option returns
		$current_color = get_user_option( 'admin_color' );
		error_log( 'Retain Admin Color: get_user_option admin_color returns: ' . $current_color );
		
		// Also test the body class that WordPress would generate
		$body_class = 'admin-color-' . get_user_option( 'admin_color', $user_id );
		error_log( 'Retain Admin Color: Expected body class: ' . $body_class );
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
		// Only process admin_color option
		if ( 'admin_color' !== $option ) {
			return $result;
		}

		// Only process in admin area
		if ( ! is_admin() ) {
			return $result;
		}

		// Handle both WP_User object and user ID
		$user_id = is_object( $user ) && $user instanceof WP_User ? $user->ID : (int) $user;
		
		if ( ! $user_id ) {
			return $result;
		}

		// Get the user's stored admin color preference
		$user_admin_color = get_user_meta( $user_id, 'admin_color', true );
		
		if ( ! empty( $user_admin_color ) ) {
			error_log( 'Retain Admin Color: Forcing admin color ' . $user_admin_color . ' for user ' . $user_id . ' (was: ' . $result . ')' );
			return $user_admin_color;
		}

		return $result;
	}

	/**
	 * Pre-filter the user's admin color choice (earlier hook).
	 *
	 * @param mixed           $result The value to return instead.
	 * @param string          $option Option name.
	 * @param WP_User|int     $user   User object or User ID.
	 * @return mixed
	 */
	public function pre_force_user_admin_color( $result, string $option, $user ) {
		if ( 'admin_color' !== $option ) {
			return $result;
		}

		if ( ! is_admin() ) {
			return $result;
		}

		$user_id = is_object( $user ) && $user instanceof WP_User ? $user->ID : (int) $user;
		
		if ( ! $user_id ) {
			return $result;
		}

		$user_admin_color = get_user_meta( $user_id, 'admin_color', true );
		
		if ( ! empty( $user_admin_color ) ) {
			error_log( 'Retain Admin Color: PRE-forcing admin color ' . $user_admin_color . ' for user ' . $user_id );
			return $user_admin_color;
		}

		return $result;
	}

	/**
	 * Enqueue admin color styles on the frontend.
	 */
	public function enqueue_frontend_admin_styles(): void {
		if ( is_admin() ) {
			return; // Only for frontend
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return; // Only for logged-in users
		}

		$admin_color = get_user_meta( $user_id, 'admin_color', true );

		if ( ! empty( $admin_color ) && 'fresh' !== $admin_color ) {
			// First, register the admin color scheme using wp_admin_css_color
			$this->register_admin_color_scheme( $admin_color );
			
			// Then load the CSS file
			$css_url = includes_url( "css/colors/$admin_color/colors.min.css" );
			$css_path = ABSPATH . WPINC . "/css/colors/$admin_color/colors.min.css";
			
			if ( file_exists( $css_path ) ) {
				wp_enqueue_style( 
					'frontend-admin-colors-' . $admin_color, 
					$css_url, 
					array(), 
					get_bloginfo( 'version' ) 
				);
				
				// Also enqueue the base admin styles that the color scheme depends on
				wp_enqueue_style( 
					'frontend-admin-colors-base',
					includes_url( 'css/colors/fresh/colors.min.css' ),
					array(),
					get_bloginfo( 'version' )
				);
				
				error_log( 'Retain Admin Color: FRONTEND - Registered and enqueued ' . $admin_color . ' CSS' );
			}
		}
	}

	/**
	 * Register the admin color scheme for frontend use.
	 *
	 * @param string $color_scheme The color scheme name.
	 */
	private function register_admin_color_scheme( string $color_scheme ): void {
		// Get the color scheme data
		$color_schemes = array(
			'light' => array(
				'name' => __( 'Light' ),
				'colors' => array( '#e5e5e5', '#999', '#d64e07', '#04a4cc' ),
			),
			'blue' => array(
				'name' => __( 'Blue' ),
				'colors' => array( '#52accc', '#096484', '#e14d43', '#096484' ),
			),
			'coffee' => array(
				'name' => __( 'Coffee' ),
				'colors' => array( '#46403c', '#59524c', '#c7a589', '#9ea476' ),
			),
			'ectoplasm' => array(
				'name' => __( 'Ectoplasm' ),
				'colors' => array( '#413256', '#523f6d', '#a3b745', '#d46f15' ),
			),
			'midnight' => array(
				'name' => __( 'Midnight' ),
				'colors' => array( '#25282b', '#363b3f', '#69a8bb', '#e14d43' ),
			),
			'ocean' => array(
				'name' => __( 'Ocean' ),
				'colors' => array( '#627c83', '#738e96', '#9ebaa0', '#87a6bc' ),
			),
			'sunrise' => array(
				'name' => __( 'Sunrise' ),
				'colors' => array( '#b43c38', '#cf4944', '#dd823b', '#ccaf0b' ),
			),
			'modern' => array(
				'name' => __( 'Modern' ),
				'colors' => array( '#1e1e1e', '#3858e9', '#833378', '#d22d2d' ),
			),
		);

		if ( isset( $color_schemes[ $color_scheme ] ) ) {
			$scheme = $color_schemes[ $color_scheme ];
			wp_admin_css_color( 
				$color_scheme, 
				$scheme['name'], 
				includes_url( "css/colors/$color_scheme/colors.min.css" ),
				$scheme['colors']
			);
			
			error_log( 'Retain Admin Color: FRONTEND - Registered color scheme ' . $color_scheme );
		}
	}

	/**
	 * Add admin color body class to frontend.
	 */
	public function add_frontend_admin_color_body_class(): void {
		if ( is_admin() ) {
			return; // Only for frontend
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return; // Only for logged-in users
		}

		$admin_color = get_user_meta( $user_id, 'admin_color', true );

		if ( ! empty( $admin_color ) ) {
			// Get color scheme colors
			$color_data = $this->get_color_scheme_data( $admin_color );
			
			// Add comprehensive styling and body class
			echo '<style type="text/css">
				/* Retain Admin Color - ' . esc_attr( $admin_color ) . ' scheme */
				body.admin-color-' . esc_attr( $admin_color ) . ' {
					--wp-admin-theme-color: ' . esc_attr( $color_data['primary'] ) . ';
					--wp-admin-theme-color-darker-10: ' . esc_attr( $color_data['secondary'] ) . ';
					--wp-admin-border-color-darker-20: ' . esc_attr( $color_data['tertiary'] ) . ';
					--wp-admin-scheme-color: ' . esc_attr( $color_data['highlight'] ) . ';
				}
				
				/* Apply admin colors to common elements */
				body.admin-color-' . esc_attr( $admin_color ) . ' .admin-color-element {
					background-color: var(--wp-admin-theme-color);
					color: white;
				}
				
				body.admin-color-' . esc_attr( $admin_color ) . ' .admin-color-border {
					border-color: var(--wp-admin-theme-color);
				}
			</style>' . "\n";
			
			// Add the admin color class to the body via JavaScript
			echo '<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", function() {
					document.body.classList.add("admin-color-' . esc_js( $admin_color ) . '");
					console.log("Retain Admin Color: Applied ' . esc_js( $admin_color ) . ' color scheme to frontend");
				});
			</script>' . "\n";
			
			error_log( 'Retain Admin Color: FRONTEND - Added comprehensive styling for ' . $admin_color );
		}
	}

	/**
	 * Get color scheme data for a given admin color.
	 *
	 * @param string $color_scheme The color scheme name.
	 * @return array Array with primary, secondary, tertiary, and highlight colors.
	 */
	private function get_color_scheme_data( string $color_scheme ): array {
		$color_schemes = array(
			'fresh' => array(
				'primary' => '#0073aa',
				'secondary' => '#005177', 
				'tertiary' => '#0085ba',
				'highlight' => '#00a0d2'
			),
			'light' => array(
				'primary' => '#999999',
				'secondary' => '#666666',
				'tertiary' => '#d54e21',
				'highlight' => '#04a4cc'
			),
			'blue' => array(
				'primary' => '#52accc',
				'secondary' => '#096484',
				'tertiary' => '#e14d43', 
				'highlight' => '#096484'
			),
			'coffee' => array(
				'primary' => '#46403c',
				'secondary' => '#59524c',
				'tertiary' => '#c7a589',
				'highlight' => '#9ea476'
			),
			'ectoplasm' => array(
				'primary' => '#523f6d',
				'secondary' => '#413256',
				'tertiary' => '#a3b745',
				'highlight' => '#d46f15'
			),
			'midnight' => array(
				'primary' => '#363b3f',
				'secondary' => '#25282b',
				'tertiary' => '#69a8bb',
				'highlight' => '#e14d43'
			),
			'ocean' => array(
				'primary' => '#738e96',
				'secondary' => '#627c83',
				'tertiary' => '#9ebaa0',
				'highlight' => '#87a6bc'
			),
			'sunrise' => array(
				'primary' => '#cf4944',
				'secondary' => '#b43c38',
				'tertiary' => '#dd823b',
				'highlight' => '#ccaf0b'
			),
			'modern' => array(
				'primary' => '#3858e9',
				'secondary' => '#1e1e1e',
				'tertiary' => '#833378',
				'highlight' => '#d22d2d'
			),
		);

		return $color_schemes[ $color_scheme ] ?? $color_schemes['fresh'];
	}

	/**
	 * Enqueue admin styles to ensure color scheme is properly applied.
	 */
	public function enqueue_admin_styles(): void {
		if ( ! is_admin() || ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$admin_color = get_user_meta( $user_id, 'admin_color', true );

		if ( ! empty( $admin_color ) ) {
			// Force the correct color scheme CSS to load
			$css_url = includes_url( "css/colors/$admin_color/colors.min.css" );
			
			// Check if the CSS file exists before trying to enqueue it
			$css_path = ABSPATH . WPINC . "/css/colors/$admin_color/colors.min.css";
			if ( file_exists( $css_path ) ) {
				wp_enqueue_style( 
					'colors-' . $admin_color, 
					$css_url, 
					array(), 
					get_bloginfo( 'version' ) 
				);
				
				error_log( 'Retain Admin Color: Force enqueued ' . $admin_color . ' CSS' );
			}
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
	error_log( 'Retain Admin Color: retain_admin_color_init() called' );
	return Retain_Admin_Color::get_instance();
}

// Initialize the plugin
error_log( 'Retain Admin Color: Plugin file loaded, calling init function' );
retain_admin_color_init();
