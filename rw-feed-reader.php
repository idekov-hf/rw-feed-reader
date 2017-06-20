<?php
/**
 * Plugin Name: RayWenderlich Feed Reader
 * Version: 0.0.2
 * Author: Iavor Dekov
 */

function get_latest_article_url() {
	$xml = simplexml_load_file( 'https://www.raywenderlich.com/feed' );
	$latest_article_url = $xml->channel->item->link->__toString();;
	return $latest_article_url;
}

function update() {
	$latest_article_url = get_latest_article_url();
	$saved_article_url = get_option( 'latest_article_url' );

	if ( false == $saved_article_url  ) {
		add_option( 'latest_article_url', $latest_article_url );
		echo 'Option does not exist, save newest article URL.<br>';
		return;
	}

	if ( $latest_article_url != $saved_article_url ) {
		update_option( 'latest_article_url', $latest_article_url );
		echo 'Newer article exists, option updated.<br>';
		notify();
		return;
	}

	echo 'Latest URL is the same as the one in the WP options table.<br>';
}

function notify() {
    $request = new WP_Http;
    $result = $request->request( 'https://us-central1-rwnotifier-f77f0.cloudfunctions.net/sendPush' );
		echo 'Firebase cloud function, sendPush, called.<br>';
}

if ( isset( $_GET['loadXML'] ) ) {
	update();
}
