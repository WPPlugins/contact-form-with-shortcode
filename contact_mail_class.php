<?php
class contact_mail_class {
	
	public $default_success_message;
	
	public function __construct() {
		$this->default_success_message = 'Mail sent successfully';	
	}
	
	public function mailable_submitter_data( $data = '' ){
		if( is_array( $data ) ){
			$data = array_filter( $data, 'sanitize_text_field' );
			return implode( " , ", $data );
		} else {
			return sanitize_text_field( $data );
		}
	}
	
	public function contact_mail_body($id = ''){
		if(!$id){
			return;
		}
		
		$contact_enable_captcha = get_post_meta( $id, '_contact_enable_security', true );
		if($contact_enable_captcha == 'Yes'){ 
			$cont_captcha = sanitize_text_field($_REQUEST['cont_captcha']); 
			if ( $cont_captcha != $_SESSION['captcha_code'] ){
				return array('msg' => __('Security code do not match!','contact-form-with-shortcode'), 'error' => 1);
			}
		}
		
		$contact 				 = get_post($id); 
		$contact_subject 		 = get_post_meta( $contact->ID, '_contact_subject', true );
		$form_name 				 = get_post_meta( $contact->ID, '_contact_from_name', true );
		$from_mail 				 = get_post_meta( $contact->ID, '_contact_from_mail', true );
		$to_mail 				 = get_post_meta( $contact->ID, '_contact_to_mail', true );
		$form_fields 			 = get_post_meta( $contact->ID, '_contact_extra_fields', true );
		$body 					 = get_post_meta( $contact->ID, '_contact_mail_body', true );
		$body_user 				 = get_post_meta( $contact->ID, '_contact_mail_body_user', true );
		$reply_to_field 		 = get_post_meta( $contact->ID, '_reply_to_field', true );
		$contact_success_message = get_post_meta( $contact->ID, '_contact_success_message', true );
		
		$attachments = array();
		$att_msg = '';
		
		if(is_array($form_fields)){
			foreach($form_fields as $k => $v){
				if($v['field_type'] == 'file'){
					$a_file = $this->get_file_attachments($v['field_name']);
					if(is_array($a_file) and $a_file['file']){
						$attachments[] = $a_file['file'];
						$attachments_db[] = $a_file['url'];
					} else {
						$att_msg = __('File not uploaded.','contact-form-with-shortcode');
					}
					
				} else {
					$body = str_replace('#'.$v['field_name'].'#', $this->mailable_submitter_data($_REQUEST[$v['field_name']]), $body);
				}
			}
		}
		
		$body = html_entity_decode($body);
		$body_user = html_entity_decode($body_user);
		
		$reply_to_field = trim( $reply_to_field, '#' );
		$reply_to_email = sanitize_text_field($_REQUEST[$reply_to_field]);
		
		do_action( 'contact_store_db', $id, $attachments_db, $_REQUEST );
		
		$multiple_to_recipients = array(
			$to_mail
		);
		
		$headers[] = 'From: ' . $form_name . ' <' . $from_mail . '>' . "\r\n";
		if($reply_to_email){
			$headers[] = 'Reply-To: '.$reply_to_email. "\r\n";
		}
		$headers_user[] = 'From: ' . $form_name . ' <' . $from_mail . '>' . "\r\n";
		
		add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		
		$bol = wp_mail( $multiple_to_recipients, $contact_subject ,$body, $headers, $attachments );
		
		// mail to user
		if( $reply_to_email and $body_user ){
			wp_mail( $reply_to_email, $contact_subject, $body_user, $headers_user );
		}
		
		remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		
		if($bol){
			if( $contact_success_message != '' ){
				$s_msg = $contact_success_message . $att_msg;
			} else {
				$s_msg = __( $this->default_success_message, 'contact-form-with-shortcode' ) . $att_msg;
			}
			return array('msg' => $s_msg, 'error' => 0);
		} else {
			return array('msg' => __('Mail not sent. Please try again later.','contact-form-with-shortcode') . $att_msg, 'error' => 1);
		}
	}
	
	public function get_file_attachments($name){
		global $sup_attachment_files_array;
		if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$uploadedfile = $_FILES[$name];
		$upload_overrides = array( 'test_form' => false );
		$arr_file_type = wp_check_filetype(basename($_FILES[$name]['name']));
		$uploaded_type = $arr_file_type['type'];
		if(in_array($uploaded_type, $sup_attachment_files_array)) {
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			if ( $movefile ) {
				return array('file' => $movefile['file'], 'url' => $movefile['url'] );
			}
		} else {
			return false;
		}
	}
	
	public function subscribe_mail_body($id = '', $data = array() ){
		if(!$id){
			return;
		}
		$subscribe 			= get_post($id); 
		$subscribe_subject 	= get_post_meta( $subscribe->ID, '_subscribe_subject', true );
		$form_name 			= get_post_meta( $subscribe->ID, '_subscribe_from_name', true );
		$from_mail 			= get_post_meta( $subscribe->ID, '_subscribe_from_mail', true );
		$subscribe_to_admin	= get_post_meta( $subscribe->ID, '_subscribe_to_admin_mail', true );
		$mail_body_user 	= get_post_meta( $subscribe->ID, '_subscribe_mail_body', true );
		$mail_body_admin 	= get_post_meta( $subscribe->ID, '_subscribe_mail_body_admin', true );
		$name 				= $data['sub_name'];
		$email 				= $data['sub_email'];
			
		$mail_body_user = str_replace(array('#name#','#email#'), array($name,$email), $mail_body_user);
		$mail_body_admin = str_replace(array('#name#','#email#'), array($name,$email), $mail_body_admin);
		
		$mail_body_user = html_entity_decode($mail_body_user);
		$mail_body_admin = html_entity_decode($mail_body_admin);
		
		$multiple_to_recipients = array(
			$email
		);
		
		$headers[] = 'From: ' . $form_name . ' <' . $from_mail . '>';
		
		add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		$bol = wp_mail( $multiple_to_recipients, $subscribe_subject ,$mail_body_user, $headers );
		
		if($subscribe_to_admin){
			$admin_recipients = array(
				$subscribe_to_admin
			);
			$bol = wp_mail( $admin_recipients, $subscribe_subject ,$mail_body_admin, $headers );
		}
		
		remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		return $bol;
	}
	
	public function newsletter_mail_body($args){
		if(!count($args)){
			return;
		}
		global $wpdb;
		
		$test_news_mail = $args['test_news_mail'];
		$subscribers = $args['subscribers'];
		$subscribers = implode(",",$subscribers);
		$from_name = $args['from_name'];
		$from_mail = $args['from_mail'];
		$newsletter_subject = $args['newsletter_subject'];
		$newsletter_mail_body = $args['newsletter_mail_body'];
		
		$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."contact_subscribers WHERE sub_id IN (%s)", $subscribers );
		$sub_users = $wpdb->get_results($query,ARRAY_A);
		
		$all_data = do_shortcode(nl2br(stripslashes(html_entity_decode($newsletter_mail_body))));
		preg_match("/(?<=#).*?(?=#)/", $all_data, $match);
		
		$all_data = str_replace($match[0], 'loop', $all_data);
		
		$all_args = unserialize($match[0]);	
		$featuredimage = $all_args['extra']['featuredimage'];
		$readmore = $all_args['extra']['readmore'];
		
		ob_start();
		$news_query = new WP_Query( $all_args['query_args'] );
		$loop_path = select_template_loop($args['newsletter_id']);
		include($loop_path);
		wp_reset_query();
		$loop = ob_get_contents();	
		ob_end_clean();
		
		$all_data = str_replace('#loop#', $loop, $all_data);
		
		ob_start();
		$template_path = select_template($args['newsletter_id']);
		include($template_path);
		$body = ob_get_contents();	
		ob_end_clean();
		
		$headers[] = 'From: ' . $from_name . ' <' . $from_mail . '>';
		
		add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		
		if($test_news_mail){
			$bol = wp_mail( $sub_to_mail, $newsletter_subject ,$body, $headers );
		} else {
			foreach($sub_users as $key => $value){
				if($value['sub_status'] == 'Active'){
					$sub_to_mail = $value['sub_email'];
					$body = str_replace( array( '#content#', '#unsubscribe#' ), array( $all_data, $this->unsubsctibe_url($value['sub_id']) ), $body );
					$body = html_entity_decode($body);
					$bol = wp_mail( $sub_to_mail, $newsletter_subject ,$body, $headers );
				}
			}
		}
				
		remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		return $bol;
	}
	
	public function set_html_content_type() {
		return 'text/html';
	}
	
	public function unsubsctibe_url($id){
		return '<a href="'.site_url().'?action=delete_subscription&sub='.base64_encode($id).'">'.__('Unsubscribe','contact-form-with-shortcode').'</a>';
	}
		
}