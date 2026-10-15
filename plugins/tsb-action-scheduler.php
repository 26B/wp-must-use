<?php
/**
 * @wordpress-plugin
 * Plugin Name: Action Scheduler failure notifications
 * Description: Email the site administrators when an Action Scheduler job fails.
 * Version:     Unreleased
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

// Hook into Action Scheduler events to notify administrators of failures.
add_action(
	'action_scheduler_init',
	function () {
		add_action(
			'action_scheduler_failed_execution',
			function ( $action_id, $exception ) {
				tsb_action_scheduler_notify_failure( (int) $action_id, $exception->getMessage() );
			},
			10,
			2
		);

		add_action(
			'action_scheduler_failed_validation',
			function ( $action_id, $exception ) {
				tsb_action_scheduler_notify_failure( (int) $action_id, $exception->getMessage() );
			},
			10,
			2
		);

		add_action(
			'action_scheduler_failed_action',
			function ( $action_id, $timeout ) {
				tsb_action_scheduler_notify_failure(
					(int) $action_id,
					sprintf( 'Action timed out after %d seconds.', (int) $timeout )
				);
			},
			10,
			2
		);

		add_action(
			'action_scheduler_unexpected_shutdown',
			function ( $action_id, $error ) {
				tsb_action_scheduler_notify_failure(
					(int) $action_id,
					sprintf( 'Unexpected shutdown: %s', $error['message'] ?? '' )
				);
			},
			10,
			2
		);
	}
);

/**
 * Email the current site's administrators about a failed action.
 *
 * @param int    $action_id Failed action ID.
 * @param string $reason    Failure reason.
 * @return void
 */
function tsb_action_scheduler_notify_failure( int $action_id, string $reason ) : void {
	$action = \ActionScheduler::store()->fetch_action( $action_id );

	$admins = get_users(
		[
			'role'   => 'administrator',
			'fields' => [ 'ID', 'user_login', 'user_email' ],
		]
	);

	/**
	 * Filter the administrators notified about a failed Action Scheduler job.
	 *
	 * @param array                  $admins    Users with ID, user_login and user_email.
	 * @param int                    $action_id Failed action ID.
	 * @param ActionScheduler_Action $action    Failed action.
	 * @param string                 $reason    Failure reason.
	 */
	$admins = apply_filters( 'tsb_action_scheduler_failure_recipients', $admins, $action_id, $action, $reason );

	$emails = array_unique( array_filter( wp_list_pluck( (array) $admins, 'user_email' ), 'is_email' ) );
	if ( empty( $emails ) ) {
		return;
	}

	$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	$message = implode(
		"\n",
		[
			sprintf( 'An Action Scheduler job failed on %s (%s).', $site_name, home_url() ),
			'',
			sprintf( 'Action ID: %d', $action_id ),
			sprintf( 'Hook: %s', $action->get_hook() ),
			sprintf( 'Group: %s', $action->get_group() ),
			sprintf( 'Arguments: %s', wp_json_encode( $action->get_args() ) ),
			sprintf( 'Reason: %s', $reason ),
			'',
			admin_url( 'tools.php?page=action-scheduler&status=failed' ),
		]
	);

	wp_mail(
		$emails,
		sprintf( '[%s] Action Scheduler job failed: %s', $site_name, $action->get_hook() ),
		$message
	);
}
