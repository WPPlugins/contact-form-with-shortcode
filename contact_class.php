<?php
class contact_class {
	public function __construct() {
		add_action( 'init', array($this,'contact_form_post') );
		add_filter( 'manage_edit-contact_form_columns', array($this,'show_contact_sc') );
		add_action( 'manage_contact_form_posts_custom_column' , array($this,'display_contact_sc'), 10, 2 );
		add_filter( 'gettext', array($this,'button_text'), 10, 2 );
	}
	
	public function contact_form_post() {
		$labels = array(
			'name'               => _x( 'Contact', 'post type general name', 'contact-form-with-shortcode' ),
			'singular_name'      => _x( 'Contact', 'post type singular name', 'contact-form-with-shortcode' ),
			'menu_name'          => _x( 'Contacts', 'admin menu', 'contact-form-with-shortcode' ),
			'name_admin_bar'     => _x( 'Contact', 'add new on admin bar', 'contact-form-with-shortcode' ),
			'add_new'            => _x( 'Add New', 'contact', 'contact-form-with-shortcode' ),
			'add_new_item'       => __( 'Add New Contact', 'contact-form-with-shortcode' ),
			'new_item'           => __( 'New Contact', 'contact-form-with-shortcode' ),
			'edit_item'          => __( 'Edit Contact', 'contact-form-with-shortcode' ),
			'view_item'          => __( 'View Contact', 'contact-form-with-shortcode' ),
			'all_items'          => __( 'All Contacts', 'contact-form-with-shortcode' ),
			'search_items'       => __( 'Search Contacts', 'contact-form-with-shortcode' ),
			'not_found'          => __( 'No contact forms found.', 'contact-form-with-shortcode' ),
			'not_found_in_trash' => __( 'No contact forms found in Trash.', 'contact-form-with-shortcode' )
		);
	
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=contact_form',
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => NULL,
			'supports'           => array( 'title' )
		);
	
		register_post_type( 'contact_form', $args );
	}
	
	
	public function show_contact_sc($columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = __('Title', 'contact-form-with-shortcode');
		$new_columns['sc'] = __('Shortcode', 'contact-form-with-shortcode');
		return $new_columns;
	}
	
	public function display_contact_sc( $column, $post_id ){
		 switch ( $column ) {
			case 'sc' :
				echo '[contactwid id="'.$post_id.'" title="'.get_the_title($post_id).'" ajax="No"]';
				break;
		}
	}
	
	public function button_text( $translation, $text ) {
		if ( 'contact_form' == get_post_type())
		if ( $text == 'Publish' )
		return 'Save';
	
		return $translation;
	}

}

class contact_meta_class {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'contact_form_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'contact_form_mail_body_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'contact_form_other_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'contact_form_security_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'contact_form_messages_fields' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}
	
	public function contact_form_fields( $post_type ) {
            $post_types = array('contact_form');  
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'contact_form_fields'
					,__( 'Contact Form Fields', 'contact-form-with-shortcode' )
					,array( $this, 'render_contact_form_fields' )
					,$post_type
					,'advanced'
					,'high'
				);
            }
	}

	public function contact_form_mail_body_fields( $post_type ) {
			$post_types = array('contact_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'contact_form_mail_body_fields'
					,__( 'Mail Body', 'contact-form-with-shortcode' )
					,array( $this, 'render_contact_form_body' )
					,$post_type
					,'advanced'
					,'high'
				);
			}
	}
	
	public function contact_form_other_fields( $post_type ) {
			$post_types = array('contact_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'contact_form_other_fields'
					,__( 'From Settings', 'contact-form-with-shortcode' )
					,array( $this, 'render_contact_other_fields' )
					,$post_type
					,'side'
					,'high'
				);
			}
	}
	
	public function contact_form_security_fields( $post_type ) {
			$post_types = array('contact_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'contact_form_security_fields'
					,__( 'Security Settings', 'contact-form-with-shortcode' )
					,array( $this, 'render_contact_security_fields' )
					,$post_type
					,'side'
					,'high'
				);
			}
	}
	
	public function contact_form_messages_fields( $post_type ) {
			$post_types = array('contact_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'contact_form_messages_fields'
					,__( 'Messages', 'contact-form-with-shortcode' )
					,array( $this, 'render_contact_form_messages_fields' )
					,$post_type
					,'side'
					,'high'
				);
			}
	}

	public function save( $post_id ) {
	
		if ( ! isset( $_POST['cfws_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['cfws_inner_custom_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'cfws_inner_custom_box' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		
		$field_names 			= $_REQUEST['field_names'];
		$field_labels 			= $_REQUEST['field_labels'];
		$field_types 			= $_REQUEST['field_types'];
		$field_descs 			= $_REQUEST['field_descs'];
		$field_requireds 		= $_REQUEST['field_requireds'];
		$field_values_array 	= $_REQUEST['field_values_array'];
		$extra_fields = array();
		
		if(is_array($field_names)){
			foreach($field_names as $key => $value){
				if($value){
					$extra_fields[] = array('field_name' => str_replace(" ","_",strtolower(trim(sanitize_text_field($value)))), 'field_label' => sanitize_text_field($field_labels[$key]), 'field_type' => sanitize_text_field($field_types[$key]), 'field_desc' => sanitize_text_field($field_descs[$key]), 'field_required' => sanitize_text_field($field_requireds[$key]), 'field_values' => sanitize_text_field($field_values_array[$key]) );
				}
			}
		}
		update_post_meta( $post_id, '_contact_extra_fields', $extra_fields );
		
		$contact_enable_captcha = sanitize_text_field( $_POST['contact_enable_captcha'] );
		update_post_meta( $post_id, '_contact_enable_security', $contact_enable_captcha );
		
		$from_name = sanitize_text_field( $_POST['from_name'] );
		update_post_meta( $post_id, '_contact_from_name', $from_name );
		
		$from_mail = sanitize_text_field( $_POST['from_mail'] );
		update_post_meta( $post_id, '_contact_from_mail', $from_mail );
		
		$contact_subject = sanitize_text_field( $_POST['contact_subject'] );
		update_post_meta( $post_id, '_contact_subject', $contact_subject );
		
		$contact_to = sanitize_text_field( $_POST['contact_to'] );
		update_post_meta( $post_id, '_contact_to_mail', $contact_to );
		
		$contact_mail_body =  esc_html( $_POST['contact_mail_body'] );
		update_post_meta( $post_id, '_contact_mail_body', $contact_mail_body );
		
		$contact_mail_body_user =  esc_html( $_POST['contact_mail_body_user'] );
		update_post_meta( $post_id, '_contact_mail_body_user', $contact_mail_body_user );
		
		$reply_to_field =  sanitize_text_field( $_POST['reply_to_field'] );
		update_post_meta( $post_id, '_reply_to_field', $reply_to_field );
		
		$contact_success_message =  sanitize_text_field( $_POST['contact_success_message'] );
		update_post_meta( $post_id, '_contact_success_message', $contact_success_message );
		
	}
	
	public function help_js(){ ?>
	<script>
	jQuery(document).ready(function(jQuery) {
		jQuery( '.tool' ).tooltip();
	});
	</script>
	<?php }
	
	public function render_contact_form_fields( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box', 'cfws_inner_custom_box_nonce' );
		$extra_fields = get_post_meta( $post->ID, '_contact_extra_fields', true );
		$cfc->LoadFieldJs();
		?>
		<table width="100%" border="0" style="border:1px dotted #999999; background-color:#FFFFFF;">
		   <tr>
			<td><?php echo $cfc->fieldList();?></td>
		  </tr>
		  <tr>
			<td>
			<div id="newFields"><?php $cfc->savedExtraFields($extra_fields);?></div>
			<div id="newFieldForm"></div>
			</td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_contact_form_messages_fields( $post ) {
		wp_nonce_field( 'cfws_inner_custom_box', 'cfws_inner_custom_box_nonce' );
		$contact_success_message = get_post_meta( $post->ID, '_contact_success_message', true );
		$cmc = new contact_mail_class;
		?>
		<table width="100%" border="0">
		  <tr>
			<td><input type="text" name="contact_success_message" value="<?php echo $contact_success_message;?>" /> <i><?php _e('Default : ','contact-form-with-shortcode');?> <?php echo $cmc->default_success_message; ?></i></td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_contact_form_body( $post ) {
		global $cfc;
		$this->help_js();
		wp_nonce_field( 'cfws_inner_custom_box', 'cfws_inner_custom_box_nonce' );
		$contact_mail_body = get_post_meta( $post->ID, '_contact_mail_body', true );
		$contact_mail_body_user = get_post_meta( $post->ID, '_contact_mail_body_user', true );
		?>
		<table width="100%" border="0" style="background-color:#FFFFFF;">
		  <tr>
			<td><strong><?php _e('Mail body to Admin','contact-form-with-shortcode');?></strong> (<i><?php _e('Attachments will be automatically attached.','contact-form-with-shortcode');?></i>)</td>
		  </tr>
          <tr>
			<td>
			<textarea name="contact_mail_body" style="width:100%; height:200px;" placeholder="Name: #user_name#, Email: #user_email#, Phone: #user_phone#"><?php echo $contact_mail_body;?></textarea></td>
		  </tr>
           <tr>
			<td><strong><?php _e('Mail body to User','contact-form-with-shortcode');?></strong></td>
		  </tr>
          <tr>
			<td>
			<textarea name="contact_mail_body_user" style="width:100%; height:200px;" placeholder="Thank you for your interest. We will contact you as soon as possible."><?php echo $contact_mail_body_user;?></textarea></td>
		  </tr>
		  <tr>
			<td>
             <i>If you want to send a <strong>Thank You</strong> email to the user then make sure you have an email field in your contact form. And dont forget to enter the email field <strong>Code</strong> to <strong>Reply To Field</strong> section.</i>
             <br>
             <br>
            HTML tags can be used in the mail body.</td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_contact_other_fields( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box', 'cfws_inner_custom_box_nonce' );
		$contact_subject = get_post_meta( $post->ID, '_contact_subject', true );
		$contact_to = get_post_meta( $post->ID, '_contact_to_mail', true );
		$from_name = get_post_meta( $post->ID, '_contact_from_name', true );
		$from_mail = get_post_meta( $post->ID, '_contact_from_mail', true );
		$reply_to_field = get_post_meta( $post->ID, '_reply_to_field', true );
		?>
		<table width="100%" border="0">
		  <tr>
			<td><strong><?php _e('Subject','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="contact_subject" value="<?php echo $contact_subject;?>" /> <a href="#" class="tool" title="Enter Email Subject">?</a></td>
		  </tr>
		  <tr>
			<td><strong><?php _e('To','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="contact_to" value="<?php echo $contact_to;?>" /> <a href="#" class="tool" title="Enter email address, where the mail will be send. You must not leave it blank.">?</a></td>
		  </tr>
		   <tr>
			<td><strong><?php _e('From Name','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="from_name" value="<?php echo $from_name;?>" /> <a href="#" class="tool" title="Enter from name in email">?</a></td>
		  </tr>
		  <tr>
			<td><strong><?php _e('From Mail','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="from_mail" value="<?php echo $from_mail;?>" /> <a href="#" class="tool" title="Enter from email address">?</a></td>
		  </tr>
          <tr>
			<td><strong><?php _e('Reply To Field','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="reply_to_field" value="<?php echo $reply_to_field;?>" placeholder="#email_field#" /><p><i>If you have an <strong>Email</strong> field in your contact from then put the field <strong>Code</strong> here. So that you can reply directly in the email chain. For example if your Email field <strong>Code</strong> is <strong>#email_field#</strong> then put it here.</i></p></td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_contact_security_fields( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box', 'cfws_inner_custom_box_nonce' );
		$contact_enable_captcha = get_post_meta( $post->ID, '_contact_enable_security', true );
		?>
		<table width="100%" border="0">
		  <tr>
			<td><input type="checkbox" name="contact_enable_captcha" value="Yes" <?php echo $contact_enable_captcha == 'Yes'?'checked="checked"':'';?> /> <?php _e('Enable Captcha Security Code','contact-form-with-shortcode');?></td>
		  </tr>
		</table>
		<?php
	}
	
}


function get_contact_form_name($id = ''){
	if($id == ''){
		return;
	}
	$contact = get_post($id);
	return $contact->post_title; 
}

function get_contact_stored_data_for_list($data){
	$data = unserialize($data);
	$ret = '';
	if(is_array($data['data'])){
		foreach($data['data'] as $key => $value){
			$ret .= $key.': '.stripslashes($value);
			$ret .= '<br>';
		}
	}
	return $ret;
}