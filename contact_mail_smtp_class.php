<?php
if(!class_exists('contact_mail_smtp_class')){
	class contact_mail_smtp_class {
		
		public function __construct() {
			$contact_enable_smtp = get_option('contact_enable_smtp');
			if($contact_enable_smtp == 'yes'){
				add_action( 'phpmailer_init', array( $this, 'cfwsp_phpmailer_init') );
			}
		}
		
		public function cfwsp_phpmailer_init( PHPMailer $phpmailer ) {
			
			$contact_enable_smtp = get_option('contact_enable_smtp');
			$contact_smtp_host = get_option('contact_smtp_host');
			$contact_smtp_port = get_option('contact_smtp_port');
			$contact_smtp_secure = get_option('contact_smtp_secure');
			
			if($contact_smtp_port == ''){
				$contact_smtp_port = 25;
			}
			$contact_smtp_username = get_option('contact_smtp_username');
			$contact_smtp_password = get_option('contact_smtp_password');
			
			if($contact_enable_smtp == 'yes'){
				$phpmailer->IsSMTP();
				$phpmailer->Host = $contact_smtp_host;
				$phpmailer->Port = (int)$contact_smtp_port; 
				if( $contact_smtp_username and $contact_smtp_password ){
					$phpmailer->Username = $contact_smtp_username;
					$phpmailer->Password = $contact_smtp_password;
					$phpmailer->SMTPAuth = true;
				}
				$phpmailer->SMTPSecure = $contact_smtp_secure;
			}
	
		}
	}
}
