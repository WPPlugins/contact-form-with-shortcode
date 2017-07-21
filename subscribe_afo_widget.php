<?php

class subscribe_form_wid extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
	 		'subscribe_form_wid',
			'Subscribe Form Widget',
			array( 'description' => __( 'Subscribe form widget', 'contact-form-with-shortcode' ), )
		);
		add_action( 'init', array( $this, 'subscribe_form_process' ) );
		add_action( 'wp_head', array( $this, 'subscribeAjaxSubmit' ) );
	 }

	public function widget( $args, $instance ) {
		extract( $args );
		
		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );
		
		echo $args['before_widget'];
		if ( ! empty( $wid_title ) )
			echo $args['before_title'] . $wid_title . $args['after_title'];
			$this->subscribeWidBody($instance);
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = sanitize_text_field( $new_instance['wid_title'] );
		$instance['wid_subscribe_form'] = sanitize_text_field( $new_instance['wid_subscribe_form'] );
		$instance['wid_subscribe_form_text'] = sanitize_text_field( $new_instance['wid_subscribe_form_text'] );
		$instance['wid_subscribe_ajax'] = sanitize_text_field( $new_instance['wid_subscribe_ajax'] );
		return $instance;
	}


	public function form( $instance ) {
		$wid_title = $instance[ 'wid_title' ];
		$wid_subscribe_form = $instance[ 'wid_subscribe_form' ];
		$wid_subscribe_form_text = $instance[ 'wid_subscribe_form_text' ];
		$wid_subscribe_ajax = $instance[ 'wid_subscribe_ajax' ];
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title:'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo $wid_title; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('wid_subscribe_form'); ?>"><?php _e('Form:'); ?> </label>
		<select id="<?php echo $this->get_field_id('wid_subscribe_form'); ?>" name="<?php echo $this->get_field_name('wid_subscribe_form'); ?>" class="widefat">
			<option value="">-</option>
			<?php $this->subscribeFormSelected($wid_subscribe_form);?>
		</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('wid_subscribe_form_text'); ?>"><?php _e('Text:'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_subscribe_form_text'); ?>" name="<?php echo $this->get_field_name('wid_subscribe_form_text'); ?>" type="text" value="<?php echo $wid_subscribe_form_text; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('wid_subscribe_ajax'); ?>"><?php _e('Enable AJAX Method:'); ?> </label>
		<input class="widefat" type="checkbox" name="<?php echo $this->get_field_name('wid_subscribe_ajax'); ?>" id="<?php echo $this->get_field_id('wid_subscribe_ajax'); ?>" value="Yes" <?php echo $wid_subscribe_ajax == 'Yes'?'checked="checked"':'';?> />
		</p>
		<?php 
	}
	
	public function start_session(){
		if(!session_id()){
			@session_start();
		}
	}
	
	public function current_page_url() {
		$pageURL = 'http';
		if( isset($_SERVER["HTTPS"]) ) {
			if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	public function subscribeWidBody($instance){
	if($instance['wid_subscribe_form'] == ''){
	 _e('Newsletter subscription form not selected.', 'contact-form-with-shortcode');
	 return;
	}
	$ajax_submit = '';
	$sub_form_process = 'do_process';
	if($instance['wid_subscribe_ajax'] == 'Yes'){
		$ajax_submit = 'onsubmit="return subscribe_afo_submit(\''.$instance['wid_subscribe_form'].'\');"';
		$sub_form_process = 'do_process_ajax';
	}
	$this->start_session();
	$this->error_message($instance['wid_subscribe_form']);
	?>
	<div class="cont_forms">
		<form name="sub" id="sub-<?php echo $instance['wid_subscribe_form'];?>" <?php echo $ajax_submit;?> action="" method="post">
		<div class="contact_afo id-<?php echo $instance['wid_subscribe_form'];?>">
			<?php $this->subscribeFormFields($instance['wid_subscribe_form']); ?>
			<input type="hidden" name="sub_form_id" value="<?php echo $instance['wid_subscribe_form'];?>" />
			<input type="hidden" name="sub_form_process" value="<?php echo $sub_form_process;?>" />
			<div class="form-group"><input type="submit" name="submit" value="<?php _e('Submit','contact-form-with-shortcode');?>" /></div>
		</div>
		</form>
		<p><?php echo $instance['wid_subscribe_form_text'];?></p>
	</div>
	<?php
	}
	
	public function subscribeAjaxSubmit(){?>
	<script type="text/javascript">
		function subscribe_afo_submit(sub_id){
			var data = jQuery( "#sub-"+sub_id ).serialize();
			jQuery.ajax({
			data: data,  
			beforeSend: function( renponse ) {}
			})
			.done(function( renponse ) {
				jQuery('#sub-err-msg-'+sub_id).html(renponse);
				jQuery( "#sub-"+sub_id ).find("input[type=text], textarea, select").val("");
				jQuery( "#sub-"+sub_id ).find("input[type=checkbox]").attr('checked', false);
				jQuery( "#sub-"+sub_id ).find("input[type=radio]").attr('checked', false);
			});
			return false;
		}
	</script>
	<?php 
	}
	
	public function subscribeFormFields($id){
		global $cfc;
		$include_name_in_subscription = get_post_meta( $id, '_include_name_in_subscription', true );
		$name_in_subscription_required = get_post_meta( $id, '_name_in_subscription_required', true );
		$name_required = ($name_in_subscription_required == 'Yes'?'required="required"':'');
		?>
			<div class="form-group">
				<label for="sub_email"><?php echo _e('Email','contact-form-with-shortcode');?></label>
				<?php $cfc->genField('email','sub_email','sub_email', '', '', '','required="required"');?>
			</div>
			<?php if($include_name_in_subscription == 'Yes'){ ?>
			<div class="form-group">
				<label for="sub_name"><?php echo _e('Name','contact-form-with-shortcode');?></label>
				<?php $cfc->genField('text','sub_name','sub_name', '', '', '',$name_required);?>
			</div>
			<?php } ?>
		<?php
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
	
	public function error_message( $sub_id = '' ){
		$this->start_session();
		$e_msg = '<div id="sub-err-msg-'.$sub_id.'">';
		if(isset($_SESSION['subscribe_msg']) and $_SESSION['subscribe_msg']){
			$e_msg .=  '<div class="'.$_SESSION['subscribe_msg_class'].'">'.$_SESSION['subscribe_msg'].'</div>';
			unset($_SESSION['subscribe_msg']);
			unset($_SESSION['subscribe_msg_class']);
		}
		$e_msg .= '</div>';
		echo $e_msg;
	}
	
	public function is_user_already_subscribed( $email = '' ){
		if(empty($email)){
			return false;
		}
		
		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."contact_subscribers where sub_email = %s and sub_status<>'Deleted'", $email);
		$result = $wpdb->get_row( $query, ARRAY_A );
		if($result){
			return $result['sub_id'];
		} else {
			return false;
		}
	}
	

	public function subscribe_form_process(){
		$this->start_session();
		if(isset($_REQUEST['sub_form_process']) and sanitize_text_field($_REQUEST['sub_form_process']) == 'do_process'){
			global $wpdb;
			$form_id = sanitize_text_field($_REQUEST['sub_form_id']);
			// add subscriber //
			$sub_id_if_exist = $this->is_user_already_subscribed(sanitize_text_field($_REQUEST['sub_email']));
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
					'sub_name' => sanitize_text_field($_REQUEST['sub_name']),
					'sub_email' => sanitize_text_field($_REQUEST['sub_email']),
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
			
			$cmc = new contact_mail_class;
			$bol = $cmc->subscribe_mail_body($form_id, array('sub_name' => sanitize_text_field($_REQUEST['sub_name']), 'sub_email' => sanitize_text_field($_REQUEST['sub_email']) ) );
			if($bol){
				$_SESSION['subscribe_msg'] = __('Thankyou for your subscription. Subscription mail sent successfully.','contact-form-with-shortcode');
				$_SESSION['subscribe_msg_class'] = 'cont_success';
			} else {
				$_SESSION['subscribe_msg'] = __('Thankyou for your subscription. Subscription mail not sent. Please try again later.','contact-form-with-shortcode');
				$_SESSION['subscribe_msg_class'] = 'cont_error';
			}
			wp_redirect( $this->current_page_url() );
			exit;
		}
		
		if(isset($_REQUEST['sub_form_process']) and sanitize_text_field($_REQUEST['sub_form_process']) == 'do_process_ajax'){
			global $wpdb;
			$form_id = sanitize_text_field($_REQUEST['sub_form_id']);
			// add subscriber //
			$sub_id_if_exist = $this->is_user_already_subscribed(sanitize_text_field($_REQUEST['sub_email']));
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
					'sub_name' => sanitize_text_field($_REQUEST['sub_name']),
					'sub_email' => sanitize_text_field($_REQUEST['sub_email']),
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
				$new_sub_id = $wpdb->insert( $wpdb->prefix."contact_subscribers", $sdata, $data_type );
			}
			// add subscriber //
			
			$cmc = new contact_mail_class;
			$bol = $cmc->subscribe_mail_body($form_id, array('sub_name' => sanitize_text_field($_REQUEST['sub_name']), 'sub_email' => sanitize_text_field($_REQUEST['sub_email']) ));
			if($bol){
				echo '<div class="cont_success">'.__('Thankyou for your subscription. Subscription mail sent successfully.','contact-form-with-shortcode').'</div>';
			} else {
				echo '<div class="cont_error">'.__('Thankyou for your subscription. Subscription mail not sent. Please try again later.','contact-form-with-shortcode').'</div>';
			}
			exit;
		}
	}
		
} 

