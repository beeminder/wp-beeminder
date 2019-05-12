<?php
/**
 * Plugin Name: Beeminder Ping
 * Plugin URI:  https://www.philnewton.net/code/beeminder-ping/
 * Description: Simple plugin to ping a Beeminder goal whenever you post, either a simple counter or a wordcount.
 * Version:     1.0
 * Author:      Phil Newton
 *
 * @package BeeminderPing
 */

/**
 * Main Beeminder Ping plugin class.
 */
class BeeminderPingPlugin {

	/**
	 * Singleton plugin instance.
	 *
	 * @var BeeminderPingPlugin
	 */
	private static $instance;

	// ------------------------------------------------------------
	// -- Construction and Initialization
	// ------------------------------------------------------------

	/**
	 * Create a new plugin instance. Sets up hooks and runs activation functions
	 * plugin is being activated for the first time.
	 */
	public function __construct() {

		// Beeminder API autoloads.
		add_action( 'init', array( $this, 'load_beeminder_api_library' ) );

		// Register the options Page.
		add_action( 'admin_menu', array( $this, 'register_admin_menu_items' ) );

		// Post publish hook.
		add_action( 'publish_post', array( &$this, 'send_pings_when_post_published' ), 10, 2 );

	}

	/**
	 * Create a new plugin instance.
	 *
	 * @return BeeminderPingPlugin Plugin instance.
	 */
	public static function init() {
		return self::instance();
	}

	/**
	 * Get the singleton instance of the plugin.
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new BeeminderPingPlugin();
		}
		return self::$instance;
	}

	// ------------------------------------------------------------
	// -- Beeminder API autoloads
	// ------------------------------------------------------------

	/**
	 * Load and register the `beeminder-php-api` autoloader.
	 */
	public function load_beeminder_api_library() {
		require_once dirname( __FILE__ ) . '/vendor/beeminder-api/lib/Beeminder/Autoloader.php';
		Beeminder_Autoloader::register();
	}


	// ------------------------------------------------------------
	// -- Admin Area
	// ------------------------------------------------------------

	/**
	 * Add an options page to the WordPress admin menu.
	 */
	public function register_admin_menu_items() {
		add_options_page(
			'Beeminder Ping',
			'Beeminder Ping',
			'manage_options',
			'beeminder-ping',
			array( $this, 'display_admin_page' )
		);
	}

	/**
	 * Display the admin options page.
	 */
	public function display_admin_page() {
		require_once dirname( __FILE__ ) . '/page-options.php';
	}

	// ------------------------------------------------------------
	// -- Post Hooks
	// ------------------------------------------------------------

	/**
	 * Checks for Beeminder options, checks the post is new and sends data to
	 * the appropriate goals.
	 *
	 * @param int     $post_id ID of the post that was published.
	 * @param WP_Post $post Post object that was published.
	 */
	public function send_pings_when_post_published( $post_id, $post ) {

		// Exit if plugin not setup or has pings disabled.
		if ( false === $this->is_beeminder_ping_enabled() ) {
			return;
		}

		// Exit if post is already published (i.e. this was an edit).
		if ( get_post_meta( $post_id, '_beeminder_ping_sent', true ) ) {
			return;
		}

		// Create an API interface.
		$client = new Beeminder_Client();
		$client->login( get_option( 'beeminder_ping_username' ), get_option( 'beeminder_ping_key' ) );

		// Send a single ping.
		if ( $this->is_ping_post_enabled() ) {

			$data = $client->getDatapointApi()->createDatapoint(
				get_option( 'beeminder_ping_post_goal' ),
				1,
				"Post published: {$post->post_title}"
			);

		}

		if ( $this->is_wordcount_enabled() ) {

			// Count words.
			$words = array();
			preg_match_all( '/\w+/', $post->post_content, $words );
			$word_count = count( $words[0] );

			// Send data.
			$client->getDatapointApi()->createDatapoint(
				get_option( 'beeminder_ping_wordcount_goal' ),
				$word_count,
				"Post published: {$post->post_title}"
			);

		}

		// Mark ping as sent.
		update_post_meta( $post_id, '_beeminder_ping_sent', true );

	}


	// ------------------------------------------------------------
	// -- Plugin Option Helpers
	// ------------------------------------------------------------

	/**
	 * Check if beeminder-ping functionality is enabled and the plugin is
	 * configured.
	 *
	 * @return bool True if plugin is configured and enabled, false if not.
	 */
	public function is_beeminder_ping_enabled() {
		return ( get_option( 'beeminder_ping_key' ) && ( $this->is_ping_post_enabled() || $this->is_wordcount_enabled() ) );
	}

	/**
	 * Check if sending a single value when a post is published is enabled.
	 *
	 * @return bool True if enabled, false if not.
	 */
	public function is_ping_post_enabled() {
		return ( true == get_option( 'beeminder_ping_post_enabled' ) );
	}

	/**
	 * Check if sending a wordcount value when a post is published is enabled.
	 *
	 * @return bool True if enabled, false if not.
	 */
	public function is_wordcount_enabled() {
		return ( true == get_option( 'beeminder_ping_wordcount_enabled' ) );
	}

}

// Create plugin.
BeeminderPingPlugin::init();
