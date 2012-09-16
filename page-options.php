<?php
/**
 * page-options.php
 * 
 * Displays options for the beeminder ping tool. Used to set a username/token,
 * as well as choose which post-publish hooks to use.
 * 
 */
?>

<div class="wrap">
    <div id="icon-Settings" class="icon32"><br /></div>
    
    <h2>Beeminder Ping Settings</h2>
	
    <?php if ( isset($errors) && count($errors) > 0) { ?>
        <div id="message" class="error fade"><p><strong>Error during update operation:</strong></p><ol><?php foreach($errors as $error) { echo "<li>$error</li>"; } ?></ol></div>
    <?php } ?>
    
    <?php if ($updateMessage) { ?>
        <div id="message" class="updated fade"><p><strong><?php _e($updateMessage); ?></strong></p></div>
    <?php } ?>	
    		
    
						
</div><!-- /div#wrap -->
