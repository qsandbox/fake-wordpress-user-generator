<?php

// This script creates 100 fake users on your WordPress site for testing purposes.
// You need to upload it where WordPress config is and access it from the browser example.com/fake-user-gen.php.
// Everytime you run this it will create another set of users. Make sure you delete this after the testing is done.
// Requirements:
// - the hosting to support php exec() function
// - wp-cli to be installed and available as 'wp'
// Copyright: Svetoslav Marinov | https://qSandbox.com
// Blog post: https://qSandbox.com/1083
$role = 'subscriber';
$email_tpl = 'mygmailemail@gmail.com';
$max_users = 100;

// This script will also be loaded by wp-cli
// https://make.wordpress.org/cli/handbook/guides/force-output-specific-locale/
if (class_exists('WP_CLI')) {
	WP_CLI::add_wp_hook( 'pre_option_WPLANG', function () {
		return 'en_US';
	} );

	// Override to prevent any accidental user notifications
	if (!function_exists('wp_new_user_notification')) {
		function wp_new_user_notification(int $user_id, $deprecated = null, string $notify = '') {
			// nada
		}
	}

	if (!function_exists('wp_mail')) {
		function wp_mail($to, $subject, $message, $headers = [], $attachments = array()) {
			echo "--------------------------------------------------\n";
			echo __METHOD__ . " email: [$to], from: [$subject], [$message]\n";
			var_dump($headers);
			echo "--------------------------------------------------\n";
		}
	}

	return; // don't process anything else if loaded by WP-CLI
}

header("Content-Type: text/plain");
echo "Creating users\n";

for ($i = 1; $i <= $max_users; $i++) {
	$cmd_exit_code = 0;
	$cmd_params_pairs_arr = [];
	$output_lines_arr = [];

	$rnd = str_replace('.', '', microtime(true)) . mt_rand(999, 99999);
	$user = 'rnduser' . $rnd;
	$email = str_replace('@', '+' . $rnd . '@', $email_tpl);
	$pass = sha1($rnd);

	$cmd = "wp user create ";
	$cmd_params_pairs_arr[] = escapeshellarg($user);
	$cmd_params_pairs_arr[] = escapeshellarg($email);
	$cmd_params_pairs_arr[] = '--role=' . escapeshellarg($role);
	$cmd_params_pairs_arr[] = '--user_pass=' . escapeshellarg($pass);
	$cmd_params_pairs_arr[] = '--first_name=' . 'first' . escapeshellarg($rnd);
	$cmd_params_pairs_arr[] = '--last_name=' . 'last' . escapeshellarg($rnd);
	$cmd_params_pairs_arr[] = '--require=' . escapeshellarg( __FILE__ ); // load itself to tweak WP-CLI features
	$cmd_params_pairs_arr[] = '--porcelain'; // return user id only

	$cmd .= join(' ', $cmd_params_pairs_arr);

	$cmd_last_line = exec($cmd, $output_lines_arr, $cmd_exit_code);
	$cmd_last_line = trim($cmd_last_line);
	$cmd .= ' 2>&1';
	$user_id = intval($cmd_last_line);

	echo "[$i/$max_users] user: [$user] " . (empty($cmd_exit_code) ? "Created user id: #$user_id" : 'Error creation: ' . $cmd_last_line) . "\n";
	echo str_repeat(" ", 1024); // otherwise apache/browsers would cache the response. We want to see the info right away
	@flush();
	@ob_flush();
}

echo "Done";
