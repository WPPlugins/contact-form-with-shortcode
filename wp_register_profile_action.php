<?php

function cfws_subscription( $user_id = '', $userdata = array() ){
	
	if( empty($user_id) ){
		return;
	}
	
	if( is_array($userdata) && empty($userdata) ){
		return;
	}
	if( empty($userdata['userdata']['cf_subscribe_newsletter']) && $userdata['userdata']['cf_subscribe_newsletter'] != 'Yes' ){
		return;
	}
	
	global $wpdb;
	$form_id = '';
	
	if(get_default_subscription_form()){
		$form_id = get_default_subscription_form();
	} else {
		return;
	}

	$sfw = new subscribe_form_wid;
	
	$cmc = new contact_mail_class;

	// add subscriber //
	$sub_id_if_exist = $sfw->is_user_already_subscribed(sanitize_text_field($userdata['userdata']['user_email']));
	if($sub_id_if_exist){
		$sdata = array(
			'sub_ip' => $_SERVER['REMOTE_ADDR'],
			'sub_added' => date('Y-m-d H:i:s'),
			'sub_status' => 'Active'
		);
		$data_type = array(
			'%s',
			'%s',
			'%s',
		);
		$where = array('sub_id' => $sub_id_if_exist);
		$data_type1 = array(
			'%d',
		);
		$wpdb->update( $wpdb->prefix."contact_subscribers", $sdata, $where, $data_type, $data_type1 );
	} else {
		$sdata = array(
			'form_id' => $form_id,
			'sub_name' => sanitize_text_field($userdata['userdata']['first_name']),
			'sub_email' => sanitize_text_field($userdata['userdata']['user_email']),
			'sub_ip' => $_SERVER['REMOTE_ADDR'],
			'sub_added' => date('Y-m-d H:i:s'),
			'sub_status' => 'Active'
		);
		$data_type = array(
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$new_sub_id = $wpdb->insert( $wpdb->prefix."contact_subscribers", $sdata, $data_type);
	}
	// add subscriber //
	$bol = $cmc->subscribe_mail_body($form_id, array('sub_name' => sanitize_text_field($userdata['userdata']['first_name']), 'sub_email' => sanitize_text_field($userdata['userdata']['user_email']) ));

}

function get_default_subscription_form(){
	$contact_default_subscribe_form = (int)get_option('contact_default_subscribe_form');
	
	if( $contact_default_subscribe_form != 0 ){
		return $contact_default_subscribe_form;
	}
	// if not set find the latest subscription form 
	$sel = '';
	$args = array( 'post_type' => 'subscribe_form', 'posts_per_page' => 1 );
	$c_forms = get_posts( $args );
	
	if(is_array($c_forms)){
		foreach ( $c_forms as $c_form ) : setup_postdata( $c_form );
			$sel = $c_form->ID;
		endforeach; 
	}
	wp_reset_postdata();
	
	if( $sel ){
		return $sel;
	} else {
		return false;
	}
}
