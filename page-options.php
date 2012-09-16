<?php
/**
 * page-options.php
 * 
 * Displays options for the beeminder ping tool. Used to set a username/token,
 * as well as choose which post-publish hooks to use.
 * 
 */

// Update options
if (isset($_POST['submit'])) {

    // TODO: Delegate this to a BeeminderAPI class

    // Check username/key is valid
    $result = json_decode(file_get_contents('https://www.beeminder.com/api/v1/users/me.json?auth_token=' . $_POST['beeminder_key']));
    
    if (isset($result->errors)) {
        $errors = array('Invalid key');
    } else {
        $updateMessage = 'Settings saved';
        update_option('beeminder_ping_username', $_POST['beeminder_username']);
        update_option('beeminder_ping_key', $_POST['beeminder_key']);
    }
}

$beeminder_username = get_option('beeminder_ping_username', $_POST['beeminder_username']);
$beeminder_key      = get_option('beeminder_ping_key', $_POST['beeminder_key']);


?>

<div class="wrap">
  <div id="icon-options-general" class="icon32"><br /></div>
  
  <h2>Beeminder Ping Settings</h2>
  
  <?php if ( isset($errors) && count($errors) > 0) { ?>
    <div id="message" class="error fade"><p><strong>Error during update operation:</strong></p><ol><?php foreach($errors as $error) { echo "<li>$error</li>"; } ?></ol></div>
  <?php } ?>
  
  <?php if ($updateMessage) { ?>
    <div id="message" class="updated fade"><p><strong><?php _e($updateMessage); ?></strong></p></div>
  <?php } ?>	
  
  <h3>API Settings</h3>
  <p>
    Once you have saved your details you will be able to setup post
    pings. You can get your API token by logging in to Beeminder and
    visiting the following url:<br /> <code><a
    href="https://www.beeminder.com/api/v1/auth_token.json">https://www.beeminder.com/api/v1/auth_token.json</a></code>
  </p>
  
  <form method="POST" action="">
    
    <table class="form-table">
      <tbody>
        
        <tr valign="top">
          <th scope="row"><label for="beeminder_username">Beeminder Username</label></th>
          <td>
            <input name="beeminder_username" type="text" id="beeminder_username" value="<?php echo $beeminder_username; ?>" class="regular-text" />
          </td>
        </tr>
        
        <tr valign="top">
          <th scope="row"><label for="beeminder_key">Beeminder API Key</label></th>
          <td>
            <input name="beeminder_key" type="text" id="beeminder_key" value="<?php echo $beeminder_key; ?>" class="regular-text" />
          </td>
        </tr>
        
      </tbody>
    </table>
    
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes" />
      &nbsp;
      <input type="submit" name="refresh" id="refresh-goals" class="button" value="Refresh Goals" />
      &nbsp;
      <input type="submit" name="clear" id="clear-settings" class="button" value="Clear Settings" />
    </p>
    
  </form>
  
</div><!-- /div#wrap -->
