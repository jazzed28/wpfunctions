<?php

/*
 * Name: Peter Son
 * Date: Aug 14 2017
 * These code used for several wordpress sites
 */


/* Redirect url to main my account page when logout */ 
add_action('wp_logout',create_function('','wp_redirect(home_url("/my-account/"));exit();'));


/**
 * Redirect users to custom URL based on their role after login
 *
 * @param string $redirect
 * @param object $user
 * @return string
 */
function wc_custom_user_redirect( $redirect, $user ) {
	// Get the first of all the roles assigned to the user
	$role = $user->roles[0];

	$dashboard = admin_url();
	$myaccount = home_url( '/my-account/' . $redirect );

	if( $role == 'administrator' ) {
		//Redirect administrators to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'shop-manager' ) {
		//Redirect shop managers to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'editor' ) {
		//Redirect editors to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'author' ) {
		//Redirect authors to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'customer' || $role == 'subscriber' ) {
		//Redirect customers and subscribers to the "home" page
		$redirect = $myaccount;
	} else {
		//Redirect any other role to the previous visited page or, if not available, to the home
		$redirect = $myaccount;
	}

	return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );


/**
 * woocommerce_package_rates is a 2.1+ hook
 */
add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );
 
/**
 * Hide shipping rates when free shipping is available
 *
 * @param array $rates Array of rates found for the package
 * @param array $package The package array/object being shipped
 * @return array of modified rates
 */
function hide_shipping_when_free_is_available( $rates, $package ) {
 	
 	// Only modify rates if free_shipping is present
  	if ( isset( $rates['free_shipping'] ) ) {
  	
  		// To unset a single rate/method, do the following. This example unsets flat_rate shipping
  		unset( $rates['flat_rate'] );
  		
  		// To unset all methods except for free_shipping, do the following
  		$free_shipping          = $rates['free_shipping'];
  		$rates                  = array();
  		$rates['free_shipping'] = $free_shipping;
	}
	
	return $rates;
}


/* Add user role description before order on my account page */
add_action( 'woocommerce_before_my_account', 'add_content_before_myaccount_order' );
function add_content_before_myaccount_order() {

	global $current_user;

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	$role_output = "";

	if ($user_role == 'administrator') {
		$role_output = 'Administrator';
	} elseif ($user_role == 'editor') {
		$role_output = 'Editor';
	} elseif ($user_role == 'author') {
		$role_output = 'Author';
	} elseif ($user_role == 'contributor') {
		$role_output = 'Contributor';
	} elseif ($user_role == 'subscriber') {
		$role_output = 'Subscriber';
	} elseif ($user_role == 'customer') {
		$role_output = 'Customer';
	} elseif ($user_role == 'wholesale_buyer') {
		$role_output = 'Wholesale Buyer';
	} elseif ($user_role == 'staff') {
		$role_output = 'Staff';
	} else {
		$role_output = '<strong>' . $user_role . '</strong>';
	}

	echo '<div class="display-userrole">
		<p class="display-role">Your current member type: <strong>'. $role_output . '</strong></p>
		<p class="display-waiting">Note: If you have registered as a wholesale buyer, you may need to wait for an approval from us.</p>
		</div>';
}


/* This code below will dequeue the plugin’s stylesheet */
add_action( 'wp_enqueue_scripts', 'wcs_dequeue_quantity' );
function wcs_dequeue_quantity() {
    wp_dequeue_style( 'wcqi-css' );
}


/* Checkbox terms and condition before register in the my account page */
function tnc_add_field_to_registration(){
    wc_get_template( 'checkout/terms.php' );
}
add_action( 'woocommerce_register_form', 'tnc_add_field_to_registration' );


/* Validate terms and condition selected checkbox */
function tnc_validation_registration( $errors, $username, $password, $email ){
    if ( empty( $_POST['terms'] ) ) {
        throw new Exception( __( 'You must accept the terms and conditions in order to register.', 'text-domain' ) );
    }
    return $errors;
}
add_action( 'woocommerce_process_registration_errors', 'tnc_validation_registration', 10, 4 );


/* Remove query version strings for faster web performance */
function _remove_script_version( $src ){
	$parts = explode( '?ver', $src );
	return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

