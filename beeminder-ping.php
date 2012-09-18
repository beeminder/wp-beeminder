<?php
/**
 * Plugin Name: Beeminder Ping
 * Plugin URI:	 http://www.philnewton.net/tools/beeminder-ping/
 * Description: Simple plugin to ping a Beeminder goal whenever you post, either a simple counter or a wordcount.
 * Version:     1.0
 * Author:      Phil Newton
 */


class BeeminderPingPlugin
{
    
    /**
     * Create a new plugin instance. Sets up hooks and runs activation functions
     * plugin is being activated for the first time.
     */
    public function __construct()
    {
        
        // Beeminder API autoloads
        add_action('init', array(&$this, 'Handle_onInit_loadBeeminderApi'));
        
        // Install / Uninstall hooks
        register_activation_hook(__FILE__, array(&$this, 'Handle_onActivate'));
        register_deactivation_hook(__FILE__, array(&$this, 'Handle_onDeActivate'));
        
        // Options Page
        add_action('admin_menu', array(&$this, 'Handle_createAdminMenu'));
                
        // Post publish hook
        add_action('publish_post', array(&$this, 'Handle_onPublishPost'), 10, 2);
        
    }
    
    
    // ------------------------------------------------------------
    // -- Beeminder API autoloads
    // ------------------------------------------------------------
    
    /**
     * Creates a Beeminder API autoloader.
     */
    public function Handle_onInit_loadBeeminderApi()
    {
        require_once dirname(__FILE__) . '/vendor/beeminder-api/lib/Beeminder/Autoloader.php';
        Beeminder_Autoloader::register();
    }
    
    
    // ------------------------------------------------------------
    // -- Admin Area
    // ------------------------------------------------------------
    
    /**
     * Creates the admin sidebar menu. Adds a page to the "settings" menu,
     */
    function Handle_createAdminMenu() 
    {
        add_options_page('Beeminder Ping', 'Beeminder Ping', 'manage_options', 'beeminder-ping', array(&$this, 'Handle_showAdminPage'));
    }
    
    function Handle_showAdminPage()
    {
        require_once dirname(__FILE__) . '/page-options.php';
    }
    
    
    // ------------------------------------------------------------
    // -- Activation / Deactivation
    // ------------------------------------------------------------
    
    /**
     * Called when the plugin is first activated.
     */
    function Handle_onActivate()
    {
        
    }
    
    /**
     * Called when the plugin is deactivated.
     */
    function Handle_onDeActivate()
    {
        
    }
    
    
    // ------------------------------------------------------------
    // -- Post Hooks
    // ------------------------------------------------------------
    
    /**
     * Handles the "publish_post" hook. Checks for Beeminder options, checks the
     * post is new and sends data to the appropriate goals.
     */
    public function Handle_onPublishPost($postId, $post)
    {
        
        // Exit if plugin not setup or has pings disabled
        if (!get_option('beeminder_ping_key') && !get_option('beeminder_ping_post_enabled') && !get_option('beeminder_ping_wordcount_enabled')) {
            return;
        }
        
        // Exit if post is already published (i.e. this was an edit)
        if (get_post_meta($postId, '_beeminder_ping_sent', true)) {
            return;
        }
        
        // Create an API interface
        $client = new Beeminder_Client();
        $client->login(get_option('beeminder_ping_username'), get_option('beeminder_ping_key'));
        
        // Send a single ping
        if (get_option('beeminder_ping_post_enabled')) {
            
            $data = $client->getDatapointApi()->createDatapoint(
                get_option('beeminder_ping_post_goal'),
                1,
                "Post published: {$post->post_title}"
            );
            
        }
        
        if (get_option('beeminder_ping_wordcount_enabled')) {
            
            // Count words
            $words = array();
            preg_match_all("/\w+/", $post->post_content, $words);
            $wordCount = count($words[0]);
            
            // Send data
            $client->getDatapointApi()->createDatapoint(
                get_option('beeminder_ping_wordcount_goal'),
                $wordCount,
                "Post published: {$post->post_title}"
            );
            
        }
        
        // Mark ping as sent
        update_post_meta($postId, '_beeminder_ping_sent', true);
        
    }
    
}

// Create plugin 
$beeminderPingPluginInstance = new BeeminderPingPlugin();
