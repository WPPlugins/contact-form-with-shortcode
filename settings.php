<?php

class contact_settings {
	
	public $plugin_folder_name = 'contact-form-with-shortcode';
	
	public function __construct() {
		$this->load_settings();
		add_action( 'admin_init',  array( $this, 'wp_contact_save_settings' ) );
		add_action( 'wp_register_profile_form', array( $this, 'wp_register_profile_newsletter_subscription' ) );
	}
	
	public function wp_register_profile_newsletter_subscription(){
		$enable_cfws_newsletter_subscription = get_option( 'enable_cfws_newsletter_subscription' );
		$contact_newsletter_subscribe_checkbox_text = get_option('contact_newsletter_subscribe_checkbox_text');
		$text = 'Subscribe to our newsletter';
		
		if( $contact_newsletter_subscribe_checkbox_text ){
			$text = $contact_newsletter_subscribe_checkbox_text;
		}
		
		if($enable_cfws_newsletter_subscription == 'Yes'){
			echo '<div class="reg-form-group">
		<input type="checkbox" name="cf_subscribe_newsletter" value="Yes" > <span style="cf-subscribe-newsletter">'.$text.'</span></div>';
		}
	}
	
	public function  contact_widget_afo_options () {
	echo '<div class="wrap">';
	global $wpdb;
	
	$this->view_message();
	$this->contact_wid_pro_add();
	$this->help_support();
	
	$contact_enable_smtp = get_option('contact_enable_smtp');
	$contact_smtp_host = get_option('contact_smtp_host');
	$contact_smtp_port = get_option('contact_smtp_port');
	$contact_smtp_username = get_option('contact_smtp_username');
	$contact_smtp_password = get_option('contact_smtp_password');
	$contact_smtp_secure = get_option('contact_smtp_secure');
	
	$contact_default_subscribe_form = get_option('contact_default_subscribe_form');
	$contact_newsletter_subscribe_checkbox_text = get_option('contact_newsletter_subscribe_checkbox_text');
	?>
    <form name="f" method="post" action="">
	<input type="hidden" name="option" value="wp_contact_save_settings" />
    <?php wp_nonce_field( 'wp_contact_save_action', 'wp_contact_save_action_field' ); ?>
	<table width="98%" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
	  <tr>
		<td style="padding:0px 10px 0px 10px;"><h1><?php _e('Contact Form Settings','contact-form-with-shortcode');?></h1></td>
	  </tr>
      
      
      <tr>
		<td>
        
        <div class="ap-tabs">
        	<div class="ap-tab"><?php _e('Usage','contact-form-with-shortcode');?></div>
            <div class="ap-tab"><?php _e('SMTP','contact-form-with-shortcode');?></div>
            <div class="ap-tab"><?php _e('Subscription','contact-form-with-shortcode');?></div>
        </div>

        <div class="ap-tabs-content">
        	<div class="ap-tab-content">
            	<table width="100%" border="0">
                <tr>
                    <td valign="top" colspan="2"><h2><?php _e('Usage','contact-form-with-shortcode');?></h2></td>
                </tr>
			  <tr>
				<td>
				<p>
                <strong>1.</strong> Create multiple contact forms for your site.<br><br>
				<strong>2.</strong> Contact forms can be displayed using <strong>Widgets</strong> and <strong>Shortcodes</strong> in your theme. Unlimited number of dynamic fields can be created in contact forms.<br><br>
				<strong>3.</strong> Dynamic fields can be easily included in the e-mail template.<br><br>
				<strong>4.</strong> Files can be uploaded as attachment in contact forms. Files will be mailed to respective Email address as Attachments. Supported file types are <strong>jpg, jpeg, png, gif, doc, docx, pdf</strong><br><br>
                <strong>5.</strong> Create Newsletter Subscription.<br><br>
                <strong>6.</strong> Send Newsletter emails to subscribers.<br><br>
                <br>
				
				<strong>Mail Body Example</strong>
				<p> For example you have created two text fields</p>
				<p> 1. "name"</p>
				<p> 2. "phone"</p> 
				<p> then, in the email body you should use,
				<p> 
				<div style="border:1px solid #999999; padding:5px;">
					<strong>Contact us mail</strong><br /><br />
					Name: #name#<br />
					Phone No: #phone#
				</div>
				</p>
				<p> This way users Name and Phone will be included in the e-mail body.</p>
		 </td>
			  </tr>
			</table>
            </div>
        	<div class="ap-tab-content">
            	<table width="100%">
                  	<tr>
                		<td valign="top" colspan="2"><h2><?php _e('SMTP Setup','contact-form-with-shortcode');?></h2></td>
                    </tr>
                    <tr>
                      <td><?php _e('Enable','contact-form-with-shortcode');?></td>
                      <td><input type="checkbox" name="contact_enable_smtp" value="yes" <?php echo ($contact_enable_smtp == 'yes'?'checked="checked"':''); ?>></td>
                    </tr>
                    <tr>
                      <td><?php _e('Host','contact-form-with-shortcode');?></td>
                      <td><input type="text" name="contact_smtp_host" value="<?php echo $contact_smtp_host;?>" placeholder="<?php _e('SMTP host name','contact-form-with-shortcode');?>"></td>
                    </tr>
                    <tr>
                      <td><?php _e('Port','contact-form-with-shortcode');?></td>
                      <td><input type="text" name="contact_smtp_port" value="<?php echo $contact_smtp_port;?>" placeholder="25"></td>
                    </tr>
                    <tr>
                      <td><?php _e('Username','contact-form-with-shortcode');?></td>
                      <td><input type="text" name="contact_smtp_username" value="<?php echo $contact_smtp_username;?>" placeholder="<?php _e('If required','contact-form-with-shortcode');?>"></td>
                    </tr>
                    <tr>
                      <td><?php _e('Password','contact-form-with-shortcode');?></td>
                      <td><input type="text" name="contact_smtp_password" value="<?php echo $contact_smtp_password;?>" placeholder="<?php _e('If required','contact-form-with-shortcode');?>"></td>
                    </tr>
                    <tr>
                      <td><?php _e('SMTP Secure','contact-form-with-shortcode');?></td>
                      <td><input type="text" name="contact_smtp_secure" value="<?php echo $contact_smtp_secure;?>" placeholder="<?php _e('ssl / tls','contact-form-with-shortcode');?>"></td>
                    </tr>
                     <tr>
                     	<td>&nbsp;</td>
                        <td><input type="submit" name="submit" value="<?php _e('Save','contact-form-with-shortcode');?>" class="button button-primary button-large" /></td>
                      </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                    </tr>
                </table>
            </div>
            <div class="ap-tab-content">
            	<table width="100%" border="0">
                   <tr>
                        <td colspan="2"><h1><?php _e('Subscription Form Settings','contact-form-with-shortcode');?></h1></td>
                      </tr>
                  	<tr>
                      <td valign="top" width="30%"><?php _e('Default Subscription Form','contact-form-with-shortcode');?></td>
                      <td>
                      	<select name="contact_default_subscribe_form">
							<?php $this->subscribeFormSelected( $contact_default_subscribe_form );?>
					  	</select> <a href="edit.php?post_type=subscribe_form" class="button">Create Newsletter Subscription Form</a>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" width="30%"><?php _e('Text for Newsletter Subscription Checkbox','contact-form-with-shortcode');?></td>
                      <td><input type="text" name="contact_newsletter_subscribe_checkbox_text" value="<?php echo $contact_newsletter_subscribe_checkbox_text;?>" size="40" placeholder="Subscribe to our newsletter"><i>This checkbox will appear in the user registration form of <strong>WP Register Profile With Shortcode</strong> plugin.</i></td>
                    </tr>
                    <tr>
                     	<td>&nbsp;</td>
                        <td><input type="submit" name="submit" value="<?php _e('Save','contact-form-with-shortcode');?>" class="button button-primary button-large" /></td>
                      </tr>
                    <tr>
                      <td colspan="2"><p>Please select the default <strong>Newsletter</strong> subscription form if you are using <a href="https://wordpress.org/plugins/wp-register-profile-with-shortcode/" target="_blank">WP Register Profile With Shortcode</a> with this plugin. <strong>WP Register Profile With Shortcode</strong> plugin will allow users of your site to subscribe your <strong>Newsletters</strong> at the time they make registration in the site.</p></td>
                    </tr>
                </table>
            </div>
            
         </div>	
        
        </td>
	  </tr>
      </table>
      
      <table width="98%" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
      <tr>
		<td>
			<table width="100%" border="0">
			  <tr>
				<td><h2>Contact Form With Shortcode PRO</h2></td>
			  </tr> 
			  <tr>
				<td>
				<strong>PRO</strong> version costs only <strong>USD 3.00</strong> with  additional features and support. PRO version can be used for <strong>Newsletter Sunscription</strong>. It can send nicely <strong>Designed</strong> Newsletter Emails. <strong>FREE</strong> Newsletter Themes are availabe to <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">Download</a> from <a href="http://aviplugins.com/" target="_blank">aviplugins.com</a>. Check it out <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">here</a>.
  <br><br>
  
  <strong>1.</strong> Store contact form data is database. View contact log data from admin panel and Export data in excel format. Create <strong>Query Manager</strong> user type and let them handle the queries. Query Managers will be able to reply users directly from admin panel.<br><br>
  
  <strong>2.</strong> Choose Theme for your newsletter. <strong>FREE</strong> downloadable themes are available to download for Newsletter Templates. <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">Checkout available themes</a><br><br>
  <strong>3.</strong> Send bulk Emails to the subscribers.
 </p>
				
				</td>
			  </tr>
			</table>
		</td>
	  </tr>
      
	</table>
    </form>
	<?php 
	$this->donate();
	echo '</div>';
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
	
	public function view_message(){
		if(isset($GLOBALS['msg'])){
			echo '<div class="cont_success">'.$GLOBALS['msg'].'</div>';
		}
	}
	
	public function wp_contact_save_settings(){
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "wp_contact_save_settings"){
			
			if ( ! isset( $_POST['wp_contact_save_action_field'] ) || ! wp_verify_nonce( $_POST['wp_contact_save_action_field'], 'wp_contact_save_action' ) ) {
			   wp_die( 'Sorry, your nonce did not verify.');
			} 
			
			update_option( 'contact_enable_smtp', sanitize_text_field($_POST['contact_enable_smtp']) );
			update_option( 'contact_smtp_host', sanitize_text_field($_POST['contact_smtp_host']) );
			update_option( 'contact_smtp_port', sanitize_text_field($_POST['contact_smtp_port']) );
			update_option( 'contact_smtp_username', sanitize_text_field($_POST['contact_smtp_username']) );
			update_option( 'contact_smtp_password', sanitize_text_field($_POST['contact_smtp_password']) );
			update_option( 'contact_smtp_secure', sanitize_text_field($_POST['contact_smtp_secure']) );
			update_option( 'contact_default_subscribe_form', sanitize_text_field($_POST['contact_default_subscribe_form']) );
			update_option( 'contact_newsletter_subscribe_checkbox_text', sanitize_text_field($_POST['contact_newsletter_subscribe_checkbox_text']) );
			
			$GLOBALS['msg'] = __('Data saved successfully','contact-form-with-shortcode');
		}
		
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "filterSubscribers"){
			global $wpdb;
			$form_id = sanitize_text_field($_POST['form_id']);	
			$post_id = sanitize_text_field($_POST['post_id']);	
			
			$sub_users = get_post_meta( $post_id, '_newsletter_from_subscribers', true );
			if( $form_id == 'all' ){
				$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."contact_subscribers WHERE sub_status = 'Active' ORDER BY sub_added DESC", $form_id );
			} else {
				$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."contact_subscribers WHERE form_id = %d AND sub_status = 'Active' ORDER BY sub_added DESC", $form_id );
			}
			$data = $wpdb->get_results($query,ARRAY_A);
			$ret = '';
			
			if(is_array($data)){
				foreach($data as $k => $v){
					if(is_array($sub_users) and in_array($v['sub_id'],$sub_users)){
						$ret .= '<option value="'.$v['sub_id'].'" selected="selected">'.$v['sub_email'].' ('.$v['sub_status'].')'.'</option>';
					} else {
						$ret .= '<option value="'.$v['sub_id'].'">'.$v['sub_email'].' ('.$v['sub_status'].')'.'</option>';
					}
				}
			}
			echo $ret;
			exit;
		}
	}
	
	public function help_support(){ ?>
	<table width="98%" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
	  <tr>
		<td align="right"><a href="http://www.aviplugins.com/support.php" target="_blank">Help and Support</a> <a href="http://www.aviplugins.com/rss/news.xml" target="_blank"><img src="<?php echo  plugin_dir_url( __FILE__ ) . '/images/rss.png';?>" style="vertical-align: middle;" alt="RSS"></a></td>
	  </tr>
	</table>
	<?php
	}
	
	public function contact_wid_pro_add(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55; padding:0px 0px 0px 10px; margin:2px;">
  <tr>
    <td><p>The <strong>PRO</strong> version of this plugin supports additional contact form settings like <strong>Drag & Drop</strong> in contact form fields for easy sorting, option to choose different newsletter <strong>Themes</strong>. <strong>FREE</strong> themes are available from <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">aviplugins.com</a>. <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">Get PRO version</a> with <strong>USD 3.00</strong></p></td>
  </tr>
</table>
	<?php 
	}
	
	public function donate(){	?>
	<table width="98%" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
	 <tr>
	 <td align="right"><a href="http://www.aviplugins.com/donate/" target="_blank">Donate</a> <img src="<?php echo  plugin_dir_url( __FILE__ ) . '/images/paypal.png';?>" style="vertical-align: middle;" alt="PayPal"></td>
	  </tr>
	</table>
	<?php
	}
	
	public function contact_form_load_text_domain(){
		load_plugin_textdomain('contact-form-with-shortcode', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}
	
	public function contact_widget_afo_menu () {
		add_menu_page( 'Contact Form Usage', 'Contact Form Usage', 'activate_plugins', 'contact_form_settings', array( $this,'contact_widget_afo_options' ) );	
		add_submenu_page('contact_form_settings', 'Contact Forms', 'Contact Forms', 'activate_plugins', 'edit.php?post_type=contact_form');
		
		add_submenu_page('contact_form_settings', 'Subscription Forms', 'Subscription Forms', 'activate_plugins' , 'edit.php?post_type=subscribe_form');
		add_submenu_page('contact_form_settings', 'Subscribers', 'Subscribers', 'activate_plugins' , 'contact_widget_afo_subscribers', array( $this,'contact_widget_afo_subscribers_list' ));
		add_submenu_page('contact_form_settings', 'Newsletter', 'Newsletter', 'activate_plugins' , 'edit.php?post_type=newsletter_form');
		add_submenu_page( 'contact_form_settings', 'Contact Form DB', 'Stored Data', 'activate_plugins', 'contact_form_afo_db_data', array( $this,'contact_form_afo_db_stored_data' ) );
	}
	
	public function  contact_form_afo_db_stored_data () {
		global $wpdb;
		$slc = new contact_db_list_class();
		$slc->display_list();
	}
	
	public function contact_widget_afo_subscribers_list(){
		$slc = new subscribers_list_class();
		$slc->display_list();
	}
	
	public function load_settings(){
		add_action( 'admin_menu' , array( $this, 'contact_widget_afo_menu' ) );
		add_action( 'plugins_loaded',  array( $this, 'contact_form_load_text_domain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'contact_plugin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'contact_plugin_styles' ) );
	}
	
	public function contact_plugin_styles() {
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'jquery-ui', plugins_url( $this->plugin_folder_name . '/css/jquery-ui.css' ) );
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('jquery.ptTimeSelect', plugins_url( $this->plugin_folder_name . '/css/jquery.ptTimeSelect.js' ));
		wp_enqueue_style( 'jquery.ptTimeSelect', plugins_url( $this->plugin_folder_name . '/css/jquery.ptTimeSelect.css' ) );
		wp_enqueue_style( 'style_contact_widget', plugins_url( $this->plugin_folder_name . '/style_contact_widget.css' ) );
		wp_enqueue_script( 'jquery.cookie', plugin_dir_url( __FILE__ ) . 'js/jquery.cookie.js' );
		wp_enqueue_script( 'ap-tabs', plugin_dir_url( __FILE__ ) . 'js/ap-tabs.js' );
	}
}

