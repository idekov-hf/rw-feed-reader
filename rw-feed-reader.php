<?php
/**
 * Plugin Name: RayWenderlich Feed Reader
 * Version: 0.0.1
 * Author: Iavor Dekov
 */

function get_latest_timestamp() {
	$xml = simplexml_load_file( 'https://www.raywenderlich.com/feed' );
	$latest_build_date = $xml->channel->lastBuildDate->__toString();
	$timestamp = strtotime( $latest_build_date );
	return $timestamp;
}

function update() {
	$latest_timestamp = get_latest_timestamp();
	update_option( 'last_build_timestamp', '1' );
	$last_timestamp = get_option( 'last_build_timestamp' );

	if ( false == $last_timestamp  ) {
		add_option( 'last_build_timestamp', $latest_timestamp );
		print_r('Option does not exist, save last_timestamp as WP option.');
		return;
	}

	if ( $latest_timestamp > $last_timestamp ) {
		update_option( 'last_build_timestamp', $latest_timestamp );
		notify();
		return;
	}

	echo 'Latest timestamp is the same as the one in the WP options table.';
}

function notify() {
    $request = new WP_Http;
    $result = $request->request( 'https://us-central1-rwnotifier-f77f0.cloudfunctions.net/sendPush' );
		print_r( 'Firebase cloud function, sendPush, called.' );
}

if ( isset( $_GET['loadXML'] ) ) {
	update();
}
