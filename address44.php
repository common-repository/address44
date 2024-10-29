<?php
/**
 * Plugin Name: Address44
 * Plugin URI: https://www.address44.com/
 * Description: Add Address Auto-Complete and Postcode Lookup to your WooCommerce Checkout.
 * Version: 1.0.2
 * Author: Address44
 * Author URI: https://www.address44.com
 * License: ADDRESS44_LICENSE
 */

/*  Copyright 2015-2021  Address44  (email : support@address44.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

// admin menu, page
add_action('admin_menu', 'ADDRESS44_admin');
function ADDRESS44_admin() {
	add_options_page('Address44', 'Address44', 'manage_options', 'address44_settings', 'ADDRESS44_adminpage');
}

function ADDRESS44_adminpage() {
	if (!current_user_can('manage_options'))
	{
	wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	$ADDRESS44_optName = 'ADDRESS44_accesskey';
	$ADDRESS44_hiddenField = 'ADDRESS44_submitHidden';
	$ADDRESS44_fieldName = 'ADDRESS44_accesskey';
	$ADDRESS44_nonce = 'ADDRESS44_nonce';

	$ADDRESS44_optVal = get_option( $ADDRESS44_optName );

	if( isset($_POST[ $ADDRESS44_hiddenField ]) && $_POST[ $ADDRESS44_hiddenField ] == 'Y'&&wp_verify_nonce( $_POST['ADDRESS44_nonce'], 'address44' ) ) {
		//Sanitize input as key and return to upper case;
		$ADDRESS44_optVal = strtoupper(sanitize_key($_POST[ $ADDRESS44_fieldName ]));
		update_option( $ADDRESS44_optName, $ADDRESS44_optVal);
?>
		<div class="updated"><p><strong><?php _e('settings saved.', 'ADDRESS44_SettingsMenu' ); ?></strong></p></div>
<?php
	}
	echo '<div class="wrap">';
	echo "<h2>" . __( 'Address44 Settings', 'ADDRESS44_SettingsMenu' ) . "</h2>";
?>
<p><b>Enter your Access Key from Address44 in the box below and click on 'Save Changes'</b></p>
<br>
<br>
	<form name="ADDRESS44_SettingsForm" method="post" action="">
	<input type="hidden" name="<?php echo $ADDRESS44_hiddenField; ?>" value="Y">
	<input type="hidden" name="<?php echo $ADDRESS44_nonce; ?>" value="<?php echo wp_create_nonce( 'address44')?>">

	<p><?php _e("Access Key:", 'ADDRESS44_SettingsMenu' ); ?> 
		<input type="text" name="<?php echo $ADDRESS44_fieldName; ?>" value="<?php echo $ADDRESS44_optVal; ?>" size="80">
	</p><hr />
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
	</form></div>
<?php
}

add_action( 'woocommerce_after_checkout_form', 'address44_add_jscript_checkout');
 
function address44_add_jscript_checkout() {
$ADDRESS44_accesskey = get_option( 'ADDRESS44_accesskey' );
$ROC_jsUrl="https://remote.address44.com/v2/go/?access-key=".$ADDRESS44_accesskey;
wp_enqueue_script('address44',$ROC_jsUrl,array(),'',true);
}
?>