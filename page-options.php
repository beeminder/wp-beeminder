<?php
/**
 * page-options.php
 *
 * Displays options for the beeminder ping tool. Used to set a username/token,
 * as well as choose which post-publish hooks to use.
 *
 */

$errors         = null;
$update_message = '';

// Update options
if ( isset( $_POST['submit'] ) ) {

	// Fetch goals from Beeminder.com
	$api = new Beeminder_Client();
	$api->login( $_POST['beeminder_username'], $_POST['beeminder_key'] );

	// Try fetching goals
	try {

		$goals = $api->getGoalApi()->getGoals();

		// If goals valid, save them (and auth info)
		update_option( 'beeminder_ping_goals', $goals );

		update_option( 'beeminder_ping_username', $_POST['beeminder_username'] );
		update_option( 'beeminder_ping_key', $_POST['beeminder_key'] );

		$update_message = 'Settings saved';
	} catch ( Exception $e ) {
		$errors = array( 'Invalid key or username' );
	}
}

if ( isset( $_POST['refresh'] ) ) {

	// Fetch goals from Beeminder.com
	$api = new Beeminder_Client();
	$api->login( get_option( 'beeminder_ping_username' ), get_option( 'beeminder_ping_key' ) );

	// Update option
	update_option( 'beeminder_ping_goals', $api->getGoalApi()->getGoals() );
	$update_message = 'Goals Updated';

}
// Clear settings
if ( isset( $_POST['clear'] ) ) {

	delete_option( 'beeminder_ping_username' );
	delete_option( 'beeminder_ping_key' );
	delete_option( 'beeminder_ping_goals' );
	delete_option( 'beeminder_ping_post_enabled' );
	delete_option( 'beeminder_ping_post_goal' );
	delete_option( 'beeminder_ping_wordcount_enabled' );
	delete_option( 'beeminder_ping_wordcount_goal' );

	// Clear post values
	unset( $_POST );

	$update_message = 'All settings cleared';

}


if ( isset( $_POST['update-hooks'] ) ) {

	// TODO: Check goals are valid

	// Save options
	update_option( 'beeminder_ping_post_enabled', $_POST['beeminder_single_enabled'] );
	update_option( 'beeminder_ping_wordcount_enabled', $_POST['beeminder_wordcount_enabled'] );

	update_option( 'beeminder_ping_post_goal', $_POST['beeminder_single_post'] );
	update_option( 'beeminder_ping_wordcount_goal', $_POST['beeminder_wordcount_post'] );

	$update_message = 'Hooks Updated';
}



$beeminder_username = get_option( 'beeminder_ping_username', $_POST['beeminder_username'] );
$beeminder_key      = get_option( 'beeminder_ping_key', $_POST['beeminder_key'] );
$beeminder_goals    = get_option( 'beeminder_ping_goals', null );

// Options
$beeminder_post_enabled      = get_option( 'beeminder_ping_post_enabled', null );
$beeminder_wordcount_enabled = get_option( 'beeminder_ping_wordcount_enabled', null );

$beeminder_post_goal      = get_option( 'beeminder_ping_post_goal', null );
$beeminder_wordcount_goal = get_option( 'beeminder_ping_wordcount_goal', null );
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>

	<h2>Beeminder Ping Settings</h2>

	<?php if ( isset( $errors ) && count( $errors ) > 0 ) : ?>
		<div id="message" class="error fade">
			<p><strong>Error during update operation:</strong></p>

			<ol>
				<?php foreach ( $errors as $error ) : ?>
					<li><?php echo esc_html( $error ); ?></li>
				<?php endforeach; ?>
			</ol>
		</div>
	<?php endif; ?>

	<?php if ( $update_message ) : ?>
		<div id="message" class="updated fade"><p><strong><?php _e( $update_message ); ?></strong></p></div>
	<?php endif; ?>

	<div id="beeminder-ping-settings">

		<h3>API Settings</h3>
		<p>
			Once you have saved your details you will be able to setup post
			pings. You can get your API token by logging in to Beeminder and
			visiting the following url:<br />
			<code>
				<a href="https://www.beeminder.com/api/v1/auth_token.json">https://www.beeminder.com/api/v1/auth_token.json</a>
			</code>
		</p>

		<form method="POST" action="">

			<table class="form-table">
				<tbody>

					<tr valign="top">
						<th scope="row"><label for="beeminder_username">Beeminder Username</label></th>
						<td>
							<input name="beeminder_username" type="text" id="beeminder_username" value="<?php echo esc_attr( $beeminder_username ); ?>" class="regular-text" />
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="beeminder_key">Beeminder API Key</label></th>
						<td>
							<input name="beeminder_key" type="text" id="beeminder_key" value="<?php echo esc_attr( $beeminder_key ); ?>" class="regular-text" />
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

	</div>

	<?php if ( $beeminder_key ) : ?>
		<div id="beeminder-ping-actions">
			<h3>Actions</h3>
			<p>
				Choose the actions that will be executed when a post is published. Either:
			</p>

			<ul style="margin-left: 48px; list-style: disc;">
				<li>Send a single value (1) to a goal -- Use this if you have a goal such as "post 5 times a week"</li>
				<li>Send the wordcount to a goal -- Use this for goals like "write 1,000 words a week.</li>
			</ul>

			<form method="POST" action="">

				<table class="form-table">
					<tbody>

						<tr valign="top">
							<th scope="row"><strong>Post Count Goal</strong></th>
							<td>
								<label for="beeminder_single_enabled">
									<input name="beeminder_single_enabled" #
												 type="checkbox"
												 id="beeminder_single_enabled"
												 value="1"
													<?php checked( $beeminder_post_enabled ); ?> />
									Send a ping when a post is published to the following goal:
								</label>

								<br />

								<select name="beeminder_single_post">
									<?php foreach ( $beeminder_goals as $goal ) : ?>
										<option value="<?php echo esc_attr( $goal->slug ); ?>"
														<?php selected( $goal->slug === $beeminder_post_goal ); ?>>
											<?php echo esc_html( $goal->title ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><strong>Word Count Goal</strong></th>
							<td>
								<label for="beeminder_wordcount_enabled">
									<input name="beeminder_wordcount_enabled"
												 type="checkbox"
												 id="beeminder_wordcount_enabled"
												 value="1"
												 <?php checked( $beeminder_wordcount_enabled ); ?> />
									Send the wordcount of a post when published to the following goal:
								</label>

								<br />

								<select name="beeminder_wordcount_post">
									<?php foreach ( $beeminder_goals as $goal ) : ?>
										<option value="<?php echo esc_attr( $goal->slug ); ?>"
																		<?php selected( $goal->slug === $beeminder_wordcount_goal ); ?>>
											<?php echo esc_html( $goal->title ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="update-hooks" id="update-hooks-submit" class="button-primary" value="Update Hooks" />
				</p>

			</form>

		</div>
	<?php endif; ?>

</div><!-- /div#wrap -->
