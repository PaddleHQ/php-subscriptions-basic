<?php
	/* File: hook.php
	 *
	 * This file is used for handling webhook/alert notifications from Paddle
	 * and updating your system accordingly.
	 *
	 * You can find a full reference of all alerts/events and their properties
	 * in our documentation:
	 * https://www.paddle.com/docs/subscriptions/events
	 *
	 *
	 * Note: In this example we use mysqli to connect to our database, and have
	 * escaped all of the POST parameters with the mysqli->real_escape_string
	 * function.
	 */

	// Connect to your database, etc...
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);

	// Which alert/event is this request for?
	$alert_name = $db->real_escape_string($_POST['alert_name']);

	// In our checkout, we used the 'passthrough' field to pass in
	// the user's ID. We retrieve this with each alert/notification
	// using the 'passthrough' variable. In production, you should
	// confirm this user exists.
	$user_id = $db->real_escape_string($_POST['passthrough']);

	// The unique ID of this user's subscription.
	$subscription_id = $db->real_escape_string($_POST['subscription_id']);

	// The plan/product this user is subscribed to.
	$plan_id = $db->real_escape_string($_POST['subscription_plan_id']);

	// The status of this user's subscription
	$status = $db->real_escape_string($_POST['status']);

	// Respond appropriately to this request.
	switch($alert_name) {
		case 'subscription_created':
			// The next billing date of this user's subscription.
			$next_bill_date = $db->real_escape_string($_POST['next_bill_date']);

			// We pass two URLs with this event, an update_url, which you can direct
			// the user to in order to update their payment information, and a 'cancel_url'
			// where you can direct the user to cancel their subscription.
			$update_url = $db->real_escape_string($_POST['update_url']);
			$cancel_url = $db->real_escape_string($_POST['cancel_url']);

			$db->query("INSERT INTO subscriptions (subscription_id, plan_id, user_id, status, next_bill_date, update_url, cancel_url, created_at) VALUES ('$subscription_id', '$plan_id', '$user_id', '$status', '$next_bill_date', '$update_url', '$cancel_url', NOW())");

			break;
		case 'subscription_updated':
			// The next billing date of this user's subscription.
			$next_bill_date = $db->real_escape_string($_POST['next_bill_date']);

			$db->query("UPDATE subscriptions SET next_bill_date = '$next_bill_date', plan_id = '$plan_id', status = '$status' WHERE subscription_id = '$subscription_id'");

			break;
		case 'subscription_cancelled':
			$db->query("UPDATE subscriptions SET next_bill_date = NULL, status = 'cancelled', cancelled_at = NOW() WHERE subscription_id = '$subscription_id'");

			break;
		case 'subscription_payment_succeeded':
			// The next billing date of this user's subscription.
			$next_bill_date = $db->real_escape_string($_POST['next_bill_date']);

			/*
				Here, you might wish to log the payment in a 'payments' table, if you
				want to keep your own records of each transaction. This isn't entirely
				necessary, as you can access this information from the Paddle Dashboard.

				You can get a full list of parameters we sent about each payment here:
				https://www.paddle.com/docs/subscriptions/events
			*/

			$db->query("UPDATE subscriptions SET next_bill_date = '$next_bill_date' WHERE subscription_id = '$subscription_id'");

			break;
		case 'subscription_payment_failed':
			/*
				Here, you might wish to email the customer telling them that you attempted
				to charge them for their subscription but were unable to.

				Including a link to the 'update_url' and asking them if they wish to re-enter
				their billing information is also a good idea.
			*/

			$db->query("UPDATE subscriptions SET next_bill_date = '', status = 'past_due' WHERE subscription_id = '$subscription_id'");

			break;
	}

	// Close the database connection.
	$db->close();
?>
