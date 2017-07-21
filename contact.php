<?php
/*
Plugin Name: Contact Form With Shortcode
Plugin URI: https://wordpress.org/plugins/contact-form-with-shortcode/
Description: This is a contact form plugin. You can use widgets and shortcodes to display contact form in your theme. Unlimited number of dynamic fields can me created for contact froms.
Version: 4.0.1
Text Domain: contact-form-with-shortcode
Domain Path: /languages
Author: aviplugins.com
Author URI: http://www.aviplugins.com/
*/

/**
	  |||||   
	<(`0_0`)> 	
	()(afo)()
	  ()-()
**/

include_once dirname( __FILE__ ) . '/settings.php';
include_once dirname( __FILE__ ) . '/fields_class.php';
include_once dirname( __FILE__ ) . '/contact_class.php';
include_once dirname( __FILE__ ) . '/contact_afo_widget.php';
include_once dirname( __FILE__ ) . '/contact_afo_widget_shortcode.php';
include_once dirname( __FILE__ ) . '/contact_mail_class.php';
include_once dirname( __FILE__ ) . '/contact_mail_smtp_class.php';
include_once dirname( __FILE__ ) . '/paginate_class.php';
include_once dirname( __FILE__ ) . '/subscribe_afo_widget.php';
include_once dirname( __FILE__ ) . '/subscribe_class.php';
include_once dirname( __FILE__ ) . '/subscribers_list_class.php';
include_once dirname( __FILE__ ) . '/newsletter_class.php';
include_once dirname( __FILE__ ) . '/newsletter_template_functions.php';
include_once dirname( __FILE__ ) . '/wp_register_profile_action.php';
include_once dirname( __FILE__ ) . '/unsubscribe_newsletter.php';
include_once dirname( __FILE__ ) . '/contact_db_list_class.php';
include_once dirname( __FILE__ ) . '/hook.php';

$sup_attachment_files_array = array( 
'image/jpeg',  
'image/png', 
'image/gif', 
'application/msword', 
'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
'application/pdf', 
);

class ContactFormSC {

	static function cfws_install() {
	 global $wpdb;
	 $create_table = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."contact_subscribers` (
	  `sub_id` int(11) NOT NULL AUTO_INCREMENT,
	  `form_id` int(11) NOT NULL,
	  `sub_name` varchar(255) NOT NULL,
	  `sub_email` varchar(255) NOT NULL,
	  `sub_ip` varchar(50) NOT NULL,
	  `sub_added` datetime NOT NULL,
	  `sub_status` enum('Active','Inactive','Deleted') NOT NULL,
	  PRIMARY KEY (`sub_id`)
	)";
	$wpdb->query($create_table);
	
	// update on 4.0.0 //
	$create_table1 = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."contact_stored_data` (
		`sd_id` int(11) NOT NULL AUTO_INCREMENT,
		`con_id` int(11) NOT NULL,
		`sd_data` text NOT NULL,
		`sd_added` datetime NOT NULL,
		`sd_ip` VARCHAR(50) NOT NULL,
		PRIMARY KEY (`sd_id`)
	)";
	$wpdb->query($create_table1);
	
	$check_field_query = "SHOW COLUMNS FROM `".$wpdb->prefix."contact_stored_data` WHERE field = 'sd_status'";
	$field_data = $wpdb->get_row($check_field_query);
	if(empty($field_data)){
		$alter_table = "ALTER TABLE `".$wpdb->prefix."contact_stored_data` ADD `sd_status` ENUM('processing','attending','unresolved','resolved') NOT NULL DEFAULT 'processing'";
		$wpdb->query($alter_table);
	}
	
	$check_field_query1 = "SHOW COLUMNS FROM `".$wpdb->prefix."contact_stored_data` WHERE field = 'sd_email'";
	$field_data1 = $wpdb->get_row($check_field_query1);
	if(empty($field_data1)){
		$alter_table1 = "ALTER TABLE `".$wpdb->prefix."contact_stored_data` ADD `sd_email` VARCHAR(255) NULL DEFAULT NULL AFTER `sd_data`";
		$wpdb->query($alter_table1);
	}
	// update on 4.0.0 //

	}
	
	static function cfws_uninstall() {}
}

register_activation_hook( __FILE__, array( 'ContactFormSC', 'cfws_install' ) );

add_action( 'widgets_init', create_function( '', 'register_widget( "contact_form_wid" );' ) );
add_action( 'widgets_init', create_function( '', 'register_widget( "subscribe_form_wid" );' ) );

add_shortcode( 'contactwid', 'contact_widget_shortcode' );
add_shortcode( 'subscribewid', 'subscribe_widget_shortcode' );
add_shortcode( 'newsletter', 'newsletter_shortcode_function' );

function call_contact_meta_class() {
    new contact_meta_class();
}

function call_newsletter_meta_class() {
    new newsletter_meta_class();
}

function call_subscribe_meta_class() {
    new subscribe_meta_class();
}

if ( is_admin() ) {
	
    add_action( 'load-post.php', 'call_contact_meta_class' );
    add_action( 'load-post-new.php', 'call_contact_meta_class' );
	new contact_class;
	
	add_action( 'load-post.php', 'call_newsletter_meta_class' );
    add_action( 'load-post-new.php', 'call_newsletter_meta_class' );
	new newsletter_class;
	
	add_action( 'load-post.php', 'call_subscribe_meta_class' );
    add_action( 'load-post-new.php', 'call_subscribe_meta_class' );
	new subscribe_class;
	
}

add_action( 'admin_init', 'process_contact_stored_data' );
add_action( 'admin_init', 'process_sub_data' );
add_action( 'contact_store_db', 'contact_store_db_process', 10, 3 );

add_action( 'init', 'process_delete_subscription_data' );

add_action( 'cfws_subscription', 'cfws_subscription', 1, 2 );

new contact_mail_smtp_class;

$cfc = new fields_class;

new contact_settings;
