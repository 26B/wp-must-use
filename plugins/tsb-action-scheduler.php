<?php
/**
 * @wordpress-plugin
 * Plugin Name: Action Scheduler failure notifications
 * Description: Email the site administrators when an Action Scheduler job fails.
 * Version:     1.0.0
 * Author:      26B
 * Author URI:  https://github.com/26B/
 * License:     GPL-3.0+
 */

namespace TSB\WP\MUPlugin\ActionScheduler;

if ( ! class_exists( '\ActionScheduler' ) || ! is_plugin_active( 'action-scheduler/action-scheduler.php' ) ) {
	return;
}

// Hook into Action Scheduler events to notify administrators of failures.
add_action(
	'action_scheduler_init',
	function () {
		add_action(
			'action_scheduler_failed_execution',
			fn ( $action_id, $exception ) => notify_failure( (int) $action_id, $exception->getMessage() ),
			10,
			2
		);

		add_action(
			'action_scheduler_failed_validation',
			fn ( $action_id, $exception ) => notify_failure( (int) $action_id, $exception->getMessage() ),
			10,
			2
		);

		add_action(
			'action_scheduler_failed_action',
			fn ( $action_id, $timeout ) => notify_failure( (int) $action_id, sprintf( 'Action timed out after %d seconds.', (int) $timeout ) ),
			10,
			2
		);

		add_action(
			'action_scheduler_unexpected_shutdown',
			fn ( $action_id, $error ) => notify_failure( (int) $action_id, sprintf( 'Unexpected shutdown: %s', $error['message'] ?? '' ) ),
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
function notify_failure( int $action_id, string $reason ) : void {
	$action = \ActionScheduler::store()->fetch_action( $action_id );

	$admins = get_users(
		[
			'role'   => 'administrator',
			'fields' => [ 'ID', 'user_login', 'user_email' ],
		]
	);
	if ( is_multisite() ) {
		$super_admins = get_super_admins();
		if ( ! empty( $super_admins ) ) {
			$admins = array_merge(
				$admins,
				get_users(
					[
						'login__in' => $super_admins,
						'fields'    => [ 'ID', 'user_login', 'user_email' ],
					]
				)
			);
			$admins = array_values( array_combine( wp_list_pluck( $admins, 'ID' ), $admins ) );
		}
	}

	// Get emails of all administrators and super administrators.
	$emails = array_unique( array_filter( wp_list_pluck( (array) $admins, 'user_email' ), 'is_email' ) );
	if ( empty( $emails ) ) {
		return;
	}

	/**
	 * Filter the emails notified about a failed Action Scheduler job.
	 *
	 * @param array                  $emails    Email addresses of the administrators and super administrators.
	 * @param array                  $admins    Users with ID, user_login and user_email.
	 * @param int                    $action_id Failed action ID.
	 * @param ActionScheduler_Action $action    Failed action.
	 * @param string                 $reason    Failure reason.
	 */
	$emails = apply_filters( 'tsb_action_scheduler_failure_recipients', $emails, $admins, $action_id, $action, $reason );

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
