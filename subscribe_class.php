<?php
class subscribe_class {
	public function __construct() {
		add_action( 'init', array($this,'subscribe_form_post') );
		add_filter( 'manage_edit-subscribe_form_columns', array($this,'show_subscribe_sc') );
		add_action( 'manage_subscribe_form_posts_custom_column' , array($this,'display_subscribe_sc'), 10, 2 );
		add_filter( 'gettext', array($this,'button_text'), 10, 2 );
	}
	
	public function subscribe_form_post() {
		$labels = array(
			'name'               => _x( 'Subscription', 'post type general name', 'contact-form-with-shortcode' ),
			'singular_name'      => _x( 'Subscription', 'post type singular name', 'contact-form-with-shortcode' ),
			'menu_name'          => _x( 'Subscription', 'admin menu', 'contact-form-with-shortcode' ),
			'name_admin_bar'     => _x( 'Subscription', 'add new on admin bar', 'contact-form-with-shortcode' ),
			'add_new'            => _x( 'Add New', 'Subscription', 'contact-form-with-shortcode' ),
			'add_new_item'       => __( 'Add New Subscription', 'contact-form-with-shortcode' ),
			'new_item'           => __( 'New Subscription', 'contact-form-with-shortcode' ),
			'edit_item'          => __( 'Edit Subscription', 'contact-form-with-shortcode' ),
			'view_item'          => __( 'View Subscription', 'contact-form-with-shortcode' ),
			'all_items'          => __( 'All subscription forms', 'contact-form-with-shortcode' ),
			'search_items'       => __( 'Search subscription forms', 'contact-form-with-shortcode' ),
			'not_found'          => __( 'No subscription forms found.', 'contact-form-with-shortcode' ),
			'not_found_in_trash' => __( 'No subscription forms found in Trash.', 'contact-form-with-shortcode' )
		);
	
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=subscribe_form',
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => NULL,
			'supports'           => array( 'title' )
		);
	
		register_post_type( 'subscribe_form', $args );
	}
	
	
	public function show_subscribe_sc($columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = __('Title', 'contact-form-with-shortcode');
		$new_columns['sc'] = __('Shortcode');
		return $new_columns;
	}
	
	public function display_subscribe_sc( $column, $post_id ){
		 switch ( $column ) {
			case 'sc' :
				echo '[subscribewid id="'.$post_id.'" title="'.get_the_title($post_id).'" ajax="No"]';
				break;
		}
	}
	
	public function button_text( $translation, $text ) {
		if ( 'subscribe_form' == get_post_type())
		if ( $text == 'Publish' )
		return 'Save';
	
		return $translation;
	}

}

class subscribe_meta_class {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'subscribe_form_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'subscribe_form_mail_body_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'subscribe_form_mail_body_admin_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'subscribe_form_other_fields' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}
	
	public function subscribe_form_fields( $post_type ) {
            $post_types = array('subscribe_form');  
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'subscribe_form_fields'
					,__( 'Subscribe Form Fields', 'contact-form-with-shortcode' )
					,array( $this, 'render_subscribe_form_fields' )
					,$post_type
					,'advanced'
					,'high'
				);
            }
	}

	public function subscribe_form_mail_body_fields( $post_type ) {
			$post_types = array('subscribe_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'subscribe_form_mail_body_fields'
					,__( 'Mail Body User', 'contact-form-with-shortcode' )
					,array( $this, 'render_subscribe_form_body' )
					,$post_type
					,'advanced'
					,'high'
				);
			}
	}
	
	public function subscribe_form_mail_body_admin_fields( $post_type ) {
			$post_types = array('subscribe_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'subscribe_form_mail_body_admin_fields'
					,__( 'Mail Body Admin', 'contact-form-with-shortcode' )
					,array( $this, 'render_subscribe_form_body_admin' )
					,$post_type
					,'advanced'
					,'high'
				);
			}
	}
	
	public function subscribe_form_other_fields( $post_type ) {
			$post_types = array('subscribe_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'subscribe_form_other_fields'
					,__( 'From Settings', 'contact-form-with-shortcode' )
					,array( $this, 'render_subscribe_other_fields' )
					,$post_type
					,'side'
					,'high'
				);
			}
	}
	
	public function help_js(){?>
	<script>
	jQuery(document).ready(function(jQuery) {
		jQuery( '.tool' ).tooltip();
	});
	</script>
	<?php }

	public function save( $post_id ) {
	
		if ( ! isset( $_POST['cfws_inner_custom_box_subscribe_nonce'] ) )
			return $post_id;

		$nonce = $_POST['cfws_inner_custom_box_subscribe_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'cfws_inner_custom_box_subscribe' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		if ( 'page' == sanitize_text_field($_POST['post_type']) ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		
		$include_name_in_subscription = sanitize_text_field( $_POST['include_name_in_subscription'] );
		update_post_meta( $post_id, '_include_name_in_subscription', $include_name_in_subscription );
		
		$name_in_subscription_required = sanitize_text_field( $_POST['name_in_subscription_required'] );
		update_post_meta( $post_id, '_name_in_subscription_required', $name_in_subscription_required );
		
		$from_name = sanitize_text_field( $_POST['from_name'] );
		update_post_meta( $post_id, '_subscribe_from_name', $from_name );
		
		$from_mail = sanitize_text_field( $_POST['from_mail'] );
		update_post_meta( $post_id, '_subscribe_from_mail', $from_mail );
		
		$subscribe_to_admin = sanitize_text_field( $_POST['subscribe_to_admin'] );
		update_post_meta( $post_id, '_subscribe_to_admin_mail', $subscribe_to_admin );
		
		$subscribe_subject = sanitize_text_field( $_POST['subscribe_subject'] );
		update_post_meta( $post_id, '_subscribe_subject', $subscribe_subject );
		
		$subscribe_mail_body =  esc_html($_POST['subscribe_mail_body']);
		update_post_meta( $post_id, '_subscribe_mail_body', $subscribe_mail_body );
		
		$subscribe_mail_body_admin =  esc_html($_POST['subscribe_mail_body_admin']);
		update_post_meta( $post_id, '_subscribe_mail_body_admin', $subscribe_mail_body_admin );
		
		
	}
	
	public function render_subscribe_form_fields( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box_subscribe', 'cfws_inner_custom_box_subscribe_nonce' );
		$include_name_in_subscription = get_post_meta( $post->ID, '_include_name_in_subscription', true );
		$name_in_subscription_required = get_post_meta( $post->ID, '_name_in_subscription_required', true );
		?>
		<table width="100%" border="0" style="border:1px dotted #999999; background-color:#FFFFFF;">
		   <tr>
			<td><input type="checkbox" checked="checked" disabled="disabled"/><?php _e('Include Email Field','contact-form-with-shortcode');?></td>
		  </tr>
		    <tr>
			<td><input type="checkbox" checked="checked" disabled="disabled"/><?php _e('Email Required','contact-form-with-shortcode');?></td>
		  </tr>
		   <tr>
			<td><input type="checkbox" name="include_name_in_subscription" value="Yes" <?php echo $include_name_in_subscription=="Yes"?'checked="checked"':'';?> /><?php _e('Include Name Field','contact-form-with-shortcode');?></td>
		  </tr>
		  <tr>
			<td><input type="checkbox" name="name_in_subscription_required" value="Yes" <?php echo $name_in_subscription_required=="Yes"?'checked="checked"':'';?> /><?php _e('Name Required','contact-form-with-shortcode');?></td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_subscribe_form_body( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box_subscribe', 'cfws_inner_custom_box_subscribe_nonce' );
		$subscribe_mail_body = get_post_meta( $post->ID, '_subscribe_mail_body', true );
		?>
		<table width="100%" border="0" style="background-color:#FFFFFF;">
		  <tr>
			<td><textarea name="subscribe_mail_body" style="width:100%; height:200px;"><?php echo $subscribe_mail_body;?></textarea></td>
		  </tr>
		  <tr>
			<td>HTML tags can be used in mail body.</td>
		  </tr>
		   <tr>
			<td>Name field code: #name# <a href="#" class="tool" title="Use this to include Name in the mail body.">?</a><br />
			Email field code: #email# <a href="#" class="tool" title="Use this to include Email in the mail body.">?</a><br />
			</td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_subscribe_form_body_admin( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box_subscribe', 'cfws_inner_custom_box_subscribe_nonce' );
		$subscribe_mail_body_admin = get_post_meta( $post->ID, '_subscribe_mail_body_admin', true );
		?>
		<table width="100%" border="0" style="background-color:#FFFFFF;">
		  <tr>
			<td><textarea name="subscribe_mail_body_admin" style="width:100%; height:200px;"><?php echo $subscribe_mail_body_admin;?></textarea></td>
		  </tr>
		 <tr>
			<td>HTML tags can be used in mail body.</td>
		  </tr>
		   <tr>
			<td>Name field code: #name# <a href="#" class="tool" title="Use this to include Name in the mail body.">?</a><br />
			Email field code: #email# <a href="#" class="tool" title="Use this to include Email in the mail body.">?</a><br />
			</td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_subscribe_other_fields( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box_subscribe', 'cfws_inner_custom_box_subscribe_nonce' );
		$subscribe_subject = get_post_meta( $post->ID, '_subscribe_subject', true );
		$subscribe_to_admin = get_post_meta( $post->ID, '_subscribe_to_admin_mail', true );
		$from_name = get_post_meta( $post->ID, '_subscribe_from_name', true );
		$from_mail = get_post_meta( $post->ID, '_subscribe_from_mail', true );
		$this->help_js();
		?>
		<table width="100%" border="0">
		  <tr>
			<td><strong><?php _e('Subject','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="subscribe_subject" value="<?php echo $subscribe_subject;?>" /></td>
		  </tr>
		  <tr>
			<td><strong><?php _e('To (Admin)','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="subscribe_to_admin" value="<?php echo $subscribe_to_admin;?>" />
			<a href="#" class="tool" title="Left this blank if you don't want the admin to get a mail when a user subscribe for this newsletter.">?</a></td>
		  </tr>
		   <tr>
			<td><strong><?php _e('From Name (User)','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="from_name" value="<?php echo $from_name;?>" />
			<a href="#" class="tool" title="From name for the email, that user receives.">?</a>
			</td>
		  </tr>
		  <tr>
			<td><strong><?php _e('From Mail (User)','contact-form-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td><input type="text" name="from_mail" value="<?php echo $from_mail;?>" />
			<a href="#" class="tool" title="From mail for the email, that user receives.">?</a>
			</td>
		  </tr>
		</table>
		<?php
	}
	
}

function get_subscribe_form_name($id = ''){
	if($id == ''){
		return;
	}
	$contact = get_post($id);
	return $contact->post_title; 
}