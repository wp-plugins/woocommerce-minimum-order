<?php
/**
 * Plugin Name: Woocommerce Minimum Order
 * Plugin URI: http://jem-products.com
 * Description: This plugin creates a simple minimum order value for Woocommerce
 * Version: 1.0.0
 * Author: Simon Emmett - JEM Marketing LLC
 * Author URI: http://jem-products.com
 */



//Our base class
class JEM_Controller{


	public function __construct(){
		define('JEMMIN_VERSION', '1.0');
		define('JEMMIN_PLUGIN_SLUG', 'woocommerce-minimum-order');


		add_action('admin_menu', array($this, 'my_plugin_menu') );
		add_action('admin_init', array($this, 'jemmin_admin_init') );
		//add_action('woocommerce_after_calculate_totals', array($this, 'jemmin_check_cart'), 99 );
		//add_action('woocommerce_update_cart_action_cart_updated', array($this, 'jemmin_check_cart'), 99 );
		add_action('woocommerce_after_calculate_totals', array($this, 'jemmin_check_cart'), 99 );

	}


	//Sets up the Admin screen
	function my_plugin_menu() {
		add_menu_page('Woocommerce Minimum Order', 'Woo Min. Order', 'administrator', 'woo-min-order-settings', array($this, 'jemmin_admin_page'), 'dashicons-admin-generic');
	}


	function jemmin_admin_init() {
	  // 
		register_setting( JEMMIN_PLUGIN_SLUG . '-settings-group', 'min_amount' );
		register_setting( JEMMIN_PLUGIN_SLUG . '-settings-group', 'error_message' );
	}



	//This is where we do the actual check!
	function jemmin_check_cart( $cart ){
		global $woocommerce;

		//So this function gets called twice
		//echo 'the answer is ' . $woocommerce->cart->total . 'id:' . get_the_ID ();


		//get the min purchase
		$amt = get_option('min_amount');

		//echo 'Got some' . intval($amt);


		//We only want apply this on specific pages
        $woocommerce_keys   =   array ( "woocommerce_cart_page_id" ,
                            "woocommerce_checkout_page_id" ,
                            "woocommerce_pay_page_id" );

        foreach ( $woocommerce_keys as $wc_page_id ) {
	        if ( get_the_ID () == get_option ( $wc_page_id , 0 ) ) {
	        	//so we are on one of our pages
	        		//echo $wc_page_id;
	        	//lets check the amount
				if($woocommerce->cart->total < intval($amt)) {

					//Get the error meesage
					$msg = get_option('error_message');
					wc_print_notice( $msg, $notice_type = 'error'); 
				}
	        }
        }

	}

	//This is the actual admin page
	//TODO - in future put this in a seperate class? Don't like it in the code
	function jemmin_admin_page(){
		?>
<div class="wrap">
<h2>Woocommerce Minimum Order Setup</h2>
 
 <p> Simply enter the minimum amount of the order along with the error message you would like to display </p>
 <p> It's that simple! </p>
<form method="post" action="options.php">
    <?php settings_fields( JEMMIN_PLUGIN_SLUG . '-settings-group' ); ?>
    <?php do_settings_sections( JEMMIN_PLUGIN_SLUG . '-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Minimum Order Amount</th>
        <td><input type="text" name="min_amount" value="<?php echo esc_attr( get_option('min_amount') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Error Message</th>
        <td><input type="text" name="error_message" size='80' value="<?php echo esc_attr( get_option('error_message') ); ?>" /></td>
        </tr>
        
    </table>
    
    <?php submit_button(); ?>
 
</form>
</div>
<?php
	}


}


//Only load the plugin if woocommerce is present
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  
	$jem_controller = new JEM_Controller();

}

