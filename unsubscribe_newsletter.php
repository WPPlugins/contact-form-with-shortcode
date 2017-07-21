<?php

function process_delete_subscription_data(){
	
	if(isset($_REQUEST['action']) and sanitize_text_field($_REQUEST['action']) == 'delete_subscription'){
		global $wpdb;
		$sub_id = base64_decode(sanitize_text_field($_REQUEST['sub']));
		$slc = new subscribers_list_class;
		$update = array('sub_status' => 'Inactive');
		$data_format = array( '%s' );
		$where = array('sub_id' => $sub_id);
		$data_format1 = array( '%d' );
		$rr = $wpdb->update( $wpdb->prefix."contact_subscribers", $update, $where, $data_format, $data_format1 );
		wp_die(__('Your subscription is successfully removed.','contact-form-with-shortcode'));
		exit;
	}

}
