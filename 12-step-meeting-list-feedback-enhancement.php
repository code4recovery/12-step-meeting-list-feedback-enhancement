<?php
/**
 * Plugin Name: 12 Step Meeting List Feedback Enhancement
 * Plugin URI: https://wordpress.org/plugins/12-step-meeting-list-feedback-enhancement/
 * Description: This '12 Step Meeting List' plugin add-on enhances the feedback feature found on the meetings detail page. It provides a formatted solution to guide user feedback, giving a consistent, auditable, and accurate view of what the feedback submitter is wanting added, changed, or removed in the 12 Step Meeting List. 
 * Version: 1.0.1
 * Requires PHP: 5.6
 * Requires 12 Step Meeting List Version: 3.12.
 * Tested up to: 5.8.2.
 * Author: Code for Recovery
 * Author URI: https://github.com/code4recovery/12-step-meeting-list-feedback-enhancement
 * Text Domain: 12-step-meeting-list-feedback-enhancement
 * Updated: December 7, 2021
 */

 //define constants
if (!defined('TSMLFE_CONTACT_EMAIL')) {
    define('TSMLFE_CONTACT_EMAIL', 'tsml@code4recovery.org');
}

if (!defined('TSMLFE_VERSION')) {
    define('TSMLFE_VERSION', '1.0.1');
}

if (!defined('TSMLFE_PLUGIN_DIR')) {
    define('TSMLFE_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('TSMLFE_PLUGIN_TEMPLATE_DIR')) {
    define( 'TSMLFE_PLUGIN_TEMPLATE_DIR', TSMLFE_PLUGIN_DIR . '/templates/' );
}

// force use of template from plugin folder
function TSMLFE_include_from_plugin( $template ) {

	  $our_post_types = array( 'tsml_meeting', 'tsml_location', 'tsml_group' );
	  
	  if( ! is_singular( $our_post_types) ){
	    return $template;
	  }

 	$meetings_template = '';
    $meetings_template = TSMLFE_PLUGIN_TEMPLATE_DIR . 'single-meetings.php';
	if( file_exists( $meetings_template ) ) {
	    $template = $meetings_template;
 	}
     return $template;
}
add_filter( 'template_include', 'TSMLFE_include_from_plugin', 99 );

include TSMLFE_PLUGIN_DIR . '/includes/ajax-override.php';

// ***************** Start of Version Check code *************************
/**
  * Plugin Activation hook function to check for Minimum 12 Step Meeting List version
  */
 function activate( ) {

    $tsml_min_version = '3.12'; //Minimum version of 12 Step Meeting List required for this plugin
    $tsml_cur_version = get_option('tsml_version', '');
    if (!validatedVersion( $tsml_cur_version, $tsml_min_version )) {
        $flag = '12 Step Meeting List'; 
         _e("<div class='bg-warning text-dark'>An error occurred. Correct TSML_VERSION not available!</div>", '12-step-meeting-list-feedback-enhancement');
   }
    else {  // All is good
        return;
    }

    deactivate_plugins( basename( __FILE__ ) );
    wp_die('<p>The <strong>12 Step Meeting List Feedback Enhancement</strong> plugin requires the '.$flag.' plugin <strong>version '.$tsml_min_version.'</strong> or greater be installed.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
}

function validatedVersion($tsml_cur_version, $tsml_min_version)
{
    if (version_compare($tsml_cur_version, $tsml_min_version, '<')) {
         echo '12 Step Meeting List plugin version: ' . $tsml_cur_version . "\n\n";
         return false;
    }
  
    return true;
}
// ****************** End of Version Check code *************************

//tell wp what to do when plugin is activated and uninstalled
if (function_exists('register_activation_hook'))
	register_activation_hook(__FILE__, 'activate');
if (function_exists('register_deactivation_hook'))
	register_deactivation_hook(__FILE__, 'deactivate');
if (function_exists('register_uninstall_hook'))
	register_uninstall_hook(__FILE__, 'uninstall');	


