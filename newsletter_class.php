<?php

class newsletter_class {
	
	public function __construct() {
		add_action( 'init', array($this,'newsletter_form_post') );
		add_filter( 'manage_edit-newsletter_form_columns', array($this,'show_newsletter_sc') );
		add_action( 'manage_newsletter_form_posts_custom_column' , array($this,'display_newsletter_sc'), 10, 2 );
		add_filter( 'gettext', array($this,'button_text'), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'codex_newsletter_updated_messages') );
	}
	
	public function newsletter_form_post() {
		$labels = array(
			'name'               => _x( 'Newsletter', 'post type general name', 'contact-form-with-shortcode' ),
			'singular_name'      => _x( 'Newsletter', 'post type singular name', 'contact-form-with-shortcode' ),
			'menu_name'          => _x( 'Newsletters', 'admin menu', 'contact-form-with-shortcode' ),
			'name_admin_bar'     => _x( 'Newsletter', 'add new on admin bar', 'contact-form-with-shortcode' ),
			'add_new'            => _x( 'Add New', 'newsletter', 'contact-form-with-shortcode' ),
			'add_new_item'       => __( 'Add New Newsletter', 'contact-form-with-shortcode' ),
			'new_item'           => __( 'New Newsletter', 'contact-form-with-shortcode' ),
			'edit_item'          => __( 'Edit Newsletter', 'contact-form-with-shortcode' ),
			'view_item'          => __( 'View Newsletter', 'contact-form-with-shortcode' ),
			'all_items'          => __( 'All Newsletters', 'contact-form-with-shortcode' ),
			'search_items'       => __( 'Search Newsletters', 'contact-form-with-shortcode' ),
			'not_found'          => __( 'No newsletter forms found.', 'contact-form-with-shortcode' ),
			'not_found_in_trash' => __( 'No newsletter forms found in Trash.', 'contact-form-with-shortcode' )
		);
	
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=newsletter_form',
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => NULL,
			'supports'           => array( 'title' )
		);
	
		register_post_type( 'newsletter_form', $args );
	}
	
	
	public function show_newsletter_sc($columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = __('Title', 'contact-form-with-shortcode');
		$new_columns['last_update'] = __('Last mail sent on');
		return $new_columns;
	}
	
	public function display_newsletter_sc( $column, $post_id ){
		 switch ( $column ) {
			case 'last_update' :
				echo get_the_modified_date();
				break;
		}
	}
	
	public function button_text( $translation, $text ) {
		if ( 'newsletter_form' == get_post_type())
		if ( $text == 'Publish' or $text == 'Update' )
		return 'Send';
	
		return $translation;
	}
	
	public function codex_newsletter_updated_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
	
		$messages['newsletter_form'] = array(
			0  => '',
			1  => __( 'Newsletter updated. Newsletter mail sent to subscribers.', 'contact-form-with-shortcode' ),
		);
	
		return $messages;
	}

}

class newsletter_meta_class {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'newsletter_form_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'newsletter_form_mail_body_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'newsletter_form_other_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'newsletter_template' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}
	
	public function newsletter_form_fields( $post_type ) {
            $post_types = array('newsletter_form');  
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'newsletter_select_subscribers'
					,__( 'Newsletter Subscribers', 'contact-form-with-shortcode' )
					,array( $this, 'render_newsletter_select_subscribers' )
					,$post_type
					,'advanced'
					,'high'
				);
            }
	}

	public function newsletter_form_mail_body_fields( $post_type ) {
			$post_types = array('newsletter_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'newsletter_form_mail_body_fields'
					,__( 'Mail Body', 'contact-form-with-shortcode' )
					,array( $this, 'render_newsletter_form_body' )
					,$post_type
					,'advanced'
					,'high'
				);
			}
	}
	
	public function newsletter_form_other_fields( $post_type ) {
			$post_types = array('newsletter_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'newsletter_form_other_fields'
					,__( 'From Settings', 'contact-form-with-shortcode' )
					,array( $this, 'render_newsletter_other_fields' )
					,$post_type
					,'side'
					,'high'
				);
			}
	}
	
	public function newsletter_template( $post_type ) {
			$post_types = array('newsletter_form');  
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'newsletter_template'
					,__( 'Newsletter Template', 'contact-form-with-shortcode' )
					,array( $this, 'render_newsletter_template_fields' )
					,$post_type
					,'side'
					,'high'
				);
			}
	}
	
	public function help_js(){?>
	<script>
	jQuery(document).ready(function(jQuery) { jQuery( '.tool' ).tooltip(); });
	jQuery( document ).ready(function() { jQuery('#select_all').click(function() { jQuery('#subscribers option').prop('selected', true); });
	jQuery('#unselect_all').click(function() { jQuery('#subscribers option').prop('selected', false); }); });
	function filterSubscribers(f_id,post_id){ jQuery.ajax({ method: "POST", data: { option: "filterSubscribers", post_id: post_id, form_id: f_id }, beforeSend: function( bc ){ jQuery("#loader").html('Loading..'); } }).done(function( data ) { jQuery("#subscribers").html(data); jQuery("#loader").html(''); }); }
	</script>
	<?php }
	
	public function save( $post_id ) {
	
		if ( ! isset( $_POST['cfws_inner_custom_box_newsletter_nonce'] ) )
			return $post_id;

		$nonce = $_POST['cfws_inner_custom_box_newsletter_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'cfws_inner_custom_box_newsletter' ) )
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
		
		$test_news_mail = sanitize_text_field($_POST['test_news_mail']);
		
		$subscribers =  $_POST['subscribers'];
		update_post_meta( $post_id, '_newsletter_from_subscribers', $subscribers );
		
		$subscription_form =  sanitize_text_field($_POST['subscription_form']);
		update_post_meta( $post_id, '_newsletter_subscription_form', $subscription_form );
		
		$from_name = sanitize_text_field( $_POST['from_name'] );
		update_post_meta( $post_id, '_newsletter_from_name', $from_name );
		
		$from_mail = sanitize_text_field( $_POST['from_mail'] );
		update_post_meta( $post_id, '_newsletter_from_mail', $from_mail );
		
		$newsletter_subject = sanitize_text_field( $_POST['newsletter_subject'] );
		update_post_meta( $post_id, '_newsletter_subject', $newsletter_subject );
		
		$newsletter_mail_body =  esc_html($_POST['newsletter_mail_body']);
		update_post_meta( $post_id, '_newsletter_mail_body', $newsletter_mail_body );
		
		$templete_file =  sanitize_text_field($_POST['templete_file']);
		update_post_meta( $post_id, '_templete_file', $templete_file );
		
		$args = array(
		'newsletter_id' => $post_id,
 		'subscribers' => $subscribers,
		'from_name' => $from_name,
		'from_mail' => $from_mail,
		'newsletter_subject' => $newsletter_subject,
		'newsletter_mail_body' => $newsletter_mail_body,
		'test_news_mail' => $test_news_mail,
		);
		
		$cmc = new contact_mail_class;
		$bol = $cmc->newsletter_mail_body($args);
	}
	
	public function subscribeFormSelected( $sel = '' ){
		$args = array( 'post_type' => 'subscribe_form', 'posts_per_page' => -1 );
		$c_forms = get_posts( $args );
		foreach ( $c_forms as $c_form ) : setup_postdata( $c_form );
			if($sel == $c_form->ID){
				echo '<option value="'.$c_form->ID.'"  selected="selected">'.$c_form->post_title.'</option>';
			} else {
				echo '<option value="'.$c_form->ID.'">'.$c_form->post_title.'</option>';
			}
		endforeach; 
		wp_reset_postdata();
	}
	
	public function render_newsletter_select_subscribers( $post ) {
		wp_nonce_field( 'cfws_inner_custom_box_newsletter', 'cfws_inner_custom_box_newsletter_nonce' );
		$subscription_form = get_post_meta( $post->ID, '_newsletter_subscription_form', true ) == '' ? 'all' : get_post_meta( $post->ID, '_newsletter_subscription_form', true );
		?>
		<table width="100%" border="0" style="border:1px dotted #999999; background-color:#FFFFFF;">
		   <tr>
			<td><strong>Filter by Subscription Form</strong>
			<select name="subscription_form" onChange="filterSubscribers(this.value,'<?php echo $post->ID;?>');">
            	<option value="all" style="border-bottom:1px solid #BFBFBF;">All Subscribers</option>
				<?php $this->subscribeFormSelected( $subscription_form ); ?>
			</select> <span id="loader"></span>
			</td>
		  </tr>
           <tr>
			<td>
            <p>
            <a href="javascript:void(0)" id="select_all" class="button">Select All</a> 
            <a href="javascript:void(0)" id="unselect_all" class="button">Unselect All</a>
            </p>
			<select name="subscribers[]" id="subscribers" style="width:100%; height:200px;" multiple="multiple"></select>
			</td>
		  </tr>
		</table>
        <script>
		jQuery( document ).ready(function() { filterSubscribers('<?php echo $subscription_form;?>','<?php echo $post->ID;?>'); });
		</script>
		<?php
	}
	
	public function render_newsletter_form_body( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box_newsletter', 'cfws_inner_custom_box_newsletter_nonce' );
		$newsletter_mail_body = get_post_meta( $post->ID, '_newsletter_mail_body', true );
		$this->help_js();
		?>
		<table width="100%" border="0" style="background-color:#FFFFFF;">
		  <tr>
			<td><textarea name="newsletter_mail_body" style="width:100%; height:200px;" placeholder="Example: [newsletter]"><?php echo $newsletter_mail_body;?></textarea></td>
		  </tr>
		   <tr>
			<td>[newsletter] <a href="#" class="tool" title="Use this to send 10 latest posts in the newsletter">?</a> Click <a href="https://wordpress.org/plugins/contact-form-with-shortcode/installation/" target="_blank">here</a> to find out more shortcode options. HTML tags can be used in newsletter mail body.</td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_newsletter_other_fields( $post ) {
		global $cfc;
		wp_nonce_field( 'cfws_inner_custom_box_newsletter', 'cfws_inner_custom_box_newsletter_nonce' );
		$newsletter_subject = get_post_meta( $post->ID, '_newsletter_subject', true );
		$from_name = get_post_meta( $post->ID, '_newsletter_from_name', true );
		$from_mail = get_post_meta( $post->ID, '_newsletter_from_mail', true );
		$from_mail = get_post_meta( $post->ID, '_newsletter_from_mail', true );
		?>
		<table width="100%" border="0">
		  <tr>
			<td><?php _e('Subject','contact-form-with-shortcode');?></td>
		  </tr>
		  <tr>
			<td><input type="text" name="newsletter_subject" value="<?php echo $newsletter_subject;?>" /></td>
		  </tr>
		   <tr>
			<td><?php _e('From Name','contact-form-with-shortcode');?></td>
		  </tr>
		  <tr>
			<td><input type="text" name="from_name" value="<?php echo $from_name;?>" /> <a href="#" class="tool" title="Enter from name in email">?</a></td>
		  </tr>
		  <tr>
			<td><?php _e('From Mail','contact-form-with-shortcode');?></td>
		  </tr>
		  <tr>
			<td><input type="text" name="from_mail" value="<?php echo $from_mail;?>" /> <a href="#" class="tool" title="Enter from email address">?</a></td>
		  </tr>
		  <tr>
			<td><?php _e('Test Mail','contact-form-with-shortcode');?> </td>
		  </tr>
		  <tr>
			<td><input type="text" name="test_news_mail" value="" /> <a href="#" class="tool" title="Enter email here to send a test newsletter. If Test Mail is entered then newsletter will not be mailed to subscribers.">?</a></td>
		  </tr>
		</table>
		<?php
	}
	
	public function render_newsletter_template_fields( $post ) {
	global $cfc;
	wp_nonce_field( 'cfws_inner_custom_box_newsletter', 'cfws_inner_custom_box_newsletter_nonce' );
	$templete_file = get_post_meta( $post->ID, '_templete_file', true );
	?>
	<table width="100%" border="0">
	  <tr>
		<td>
		<select name="templete_file">
			<?php echo templates_selected($templete_file);?>
		</select>
        <a href="#" class="tool" title="Use PRO version for more Newsletter Themes. FREE themes are available to download from aviplugins.com">?</a>
        <p>Get <a href="http://www.aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">PRO</a> version for <strong>FREE</strong> newsletter templates.</p>
		</td>
	  </tr>
	</table>
	<?php
	}

}
