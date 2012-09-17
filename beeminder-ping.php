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
    
}

// Create plugin 
$beeminderPingPluginInstance = new BeeminderPingPlugin();
