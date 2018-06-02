<?php
/**
 * Plugin Name: Beeminder Ping
 * Plugin URI:   https://www.philnewton.net/code/beeminder-ping/
 * Description: Simple plugin to ping a Beeminder goal whenever you post, either a simple counter or a wordcount.
 * Version:     1.0
 * Author:      Phil Newton
 */


class BeeminderPingPlugin {


	/**
	 * Create a new plugin instance. Sets up hooks and runs activation functions
	 * plugin is being activated for the first time.
	 */
	public function __construct() {

		// Beeminder API autoloads
		add_action( 'init', array( $this, 'load_beeminder_api' ) );

		// Options Page
		add_action( 'admin_menu', array( &$this, 'Handle_createAdminMenu' ) );

		// Post publish hook
		add_action( 'publish_post', array( &$this, 'Handle_onPublishPost' ), 10, 2 );

	}


	// ------------------------------------------------------------
	// -- Beeminder API autoloads
	// ------------------------------------------------------------

	/**
	 * Creates a Beeminder API autoloader.
	 */
	public function load_beeminder_api() {
		require_once dirname( __FILE__ ) . '/vendor/beeminder-api/lib/Beeminder/Autoloader.php';
		Beeminder_Autoloader::register();
	}


	// ------------------------------------------------------------
	// -- Admin Area
	// ------------------------------------------------------------

	/**
	 * Creates the admin sidebar menu. Adds a page to the "settings" menu,
	 */
	public function create_admin_nenu() {
		add_options_page(
			'Beeminder Ping',
			'Beeminder Ping',
			'manage_options',
			'beeminder-ping',
			array( $this, 'show_admin_page' )
		);
	}

	public function show_admin_page() {
		require_once dirname( __FILE__ ) . '/page-options.php';
	}

	// ------------------------------------------------------------
	// -- Post Hooks
	// ------------------------------------------------------------

	/**
	 * Handles the "publish_post" hook. Checks for Beeminder options, checks
	 * the post is new and sends data to the appropriate goals.
	 */
	public function on_publish_post( $post_id, $post ) {

		// Exit if plugin not setup or has pings disabled.
		if ( ! get_option( 'beeminder_ping_key' ) && ! get_option( 'beeminder_ping_post_enabled' ) && ! get_option( 'beeminder_ping_wordcount_enabled' ) ) {
			return;
		}

		// Exit if post is already published (i.e. this was an edit).
		if ( get_post_meta( $post_id, '_beeminder_ping_sent', true ) ) {
			return;
		}

		// Create an API interface
		$client = new Beeminder_Client();
		$client->login( get_option( 'beeminder_ping_username' ), get_option( 'beeminder_ping_key' ) );

		// Send a single ping
		if ( get_option( 'beeminder_ping_post_enabled' ) ) {

			$data = $client->getDatapointApi()->createDatapoint(
				get_option( 'beeminder_ping_post_goal' ),
				1,
				"Post published: {$post->post_title}"
			);

		}

		if ( get_option( 'beeminder_ping_wordcount_enabled' ) ) {

			// Count words
			$words = array();
			preg_match_all( '/\w+/', $post->post_content, $words );
			$word_count = count( $words[0] );

			// Send data
			$client->getDatapointApi()->createDatapoint(
				get_option( 'beeminder_ping_wordcount_goal' ),
				$word_count,
				"Post published: {$post->post_title}"
			);

		}

		// Mark ping as sent
		update_post_meta( $post_id, '_beeminder_ping_sent', true );

	}

}

// Create plugin
$beeminderPingPluginInstance = new BeeminderPingPlugin();
