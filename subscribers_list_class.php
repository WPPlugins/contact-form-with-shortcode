<?php
class subscribers_list_class {
    
	public $plugin_page;
	
	public $plugin_page_base;
	
    public function __construct(){
      $this->plugin_page_base = 'contact_widget_afo_subscribers';
	  $this->plugin_page = admin_url('admin.php?page='.$this->plugin_page_base);
    }
	
	public function get_table_colums(){
		$colums = array(
		'sub_id' => __('ID','contact-form-with-shortcode'),
		'form_id' => __('Form / ID','contact-form-with-shortcode'),
		'sub_email' => __('Email','contact-form-with-shortcode'),
		'sub_added' => __('Added','contact-form-with-shortcode'),
		'sub_status' => __('Status','contact-form-with-shortcode'),
		'action' => __('Action','contact-form-with-shortcode')
		);
		return $colums;
	}
	
	public function add_message($msg,$class = 'error'){
		$this->start_session();
		$_SESSION['msg'] = $msg;
	}
	
	public function view_message(){
		$this->start_session();
		if(isset($_SESSION['msg']) and $_SESSION['msg']){
			echo '<div class="cont_success">'.$_SESSION['msg'].'</div>';
			$_SESSION['msg'] = '';
		}
	}
	
	public function table_start(){
		return '<table class="wp-list-table widefat">';
	} 
    
	public function table_end(){
		return '</table>';
	}
	
	public function get_table_header(){
		$ret = '';
		$header = $this->get_table_colums();
		$ret .= '<thead>';
		$ret .= '<tr>';
		foreach($header as $key => $value){
			$ret .= '<th>'.$value.'</th>';
		}
		$ret .= '</tr>';
		$ret .= '</thead>';
		return $ret;		
	}
	
	public function table_td_column($value){
		$ret = '';
		if(is_array($value)){
			foreach($value as $vk => $vv){
				$ret .= $this->row_data($vk,$vv);
			}
		}
		
		$ret .= $this->row_actions($value['sub_id']);
		return $ret;
	}
	
	public function row_actions($id){
		return '<td><a href="'.$this->plugin_page.'&action=cf_edit&id='.$id.'">'.__('Edit','contact-form-with-shortcode').'</a> | <a href="'.wp_nonce_url($this->plugin_page.'&action=cf_delete&id='.$id, 'cfwsp_nonce', 'cfwsp_nonce_field').'">'.__('Delete','contact-form-with-shortcode').'</a></td>';
	}
	
	public function row_data($key,$value){
		$v = '';
		switch ($key){
			case 'sub_id':
			$v = $value;
			break;
			case 'form_id':
			$v = get_subscribe_form_name($value) . ' / ' . $value;
			break;
			case 'sub_email':
			$v = $value;
			break;
			case 'sub_added':
			$v = $value;
			break;
			case 'sub_status':
			$v = $value;
			break;
			default:
			//$v = $value; uncomment this line on your own risk
			break;
		}
		if($v){
			return '<td>'.$v.'</td>';
		}
	}
	
	public function get_table_body($data){
		$cnt = 0;
		$ret = '';
		if(is_array($data)){
			$ret .= '<tbody id="the-list">';
			foreach($data as $k => $v){
				$ret .= '<tr class="'.($cnt%2==0?'alternate':'').'">';
				$ret .= $this->table_td_column($v);
				$ret .= '</tr>';
				$cnt++;
			}
			$ret .= '</tbody>';
		}
		return $ret;
	}
	
	public function get_single_row_data($id){
		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."contact_subscribers WHERE sub_id = %d", $id );
		$result = $wpdb->get_row( $query, ARRAY_A );
		return $result;
	}
	
	public function prepare_data(){
		global $wpdb;
		$query = "SELECT * FROM ".$wpdb->prefix."contact_subscribers WHERE sub_status<>'Deleted' ORDER BY sub_added DESC";
		//$data = $wpdb->get_results($query,ARRAY_A);
		$ap = new afo_paginate(1,$this->plugin_page);
		$data = $ap->initialize($query,0);
		return $data;
	}
	
	public function search_form(){
	?>
	<form name="sub_search" action="" method="get">
	<input type="hidden" name="page" value="<?php echo $this->plugin_page_base;?>" />
	<input type="hidden" name="search" value="sub_search" />
	<table width="100%" border="0">
	  <tr>
		<td width="17%"><input type="text" name="form_id" value="<?php echo sanitize_text_field(@$_REQUEST['form_id']);?>" placeholder="Form ID"/></td>
		<td width="83%"><input type="submit" name="submit" value="<?php _e('Filter','contact-form-with-shortcode');?>" class="button"/></td>
	  </tr>
	</table>
	</form>
	<?php
	}
	
	public function get_active_subscribers(){
		global $wpdb;
		$query = "SELECT * FROM ".$wpdb->prefix."contact_subscribers WHERE sub_status='Active' ORDER BY sub_added DESC";
		$data = $wpdb->get_results($query,ARRAY_A);
		return $data;
	}
	
	public function edit(){
	$id = intval(sanitize_text_field($_REQUEST['id']));
	$data = $this->get_single_row_data($id);
	$this->view_message();
	?>
	<form name="f" action="" method="post">
	<input type="hidden" name="sub_id" value="<?php echo $id;?>" />
	<input type="hidden" name="action" value="sub_edit" />
	<h2><?php _e('Subscriber Details','contact-form-with-shortcode');?></h2>
	<table width="100%" border="0" cellspacing="10" style="background-color:#FFFFFF; padding:5px; border:1px solid #CCCCCC;">
		<tr>
			<td><strong><?php _e('Subscriber Form','contact-form-with-shortcode');?></strong></td>
			<td><?php echo get_subscribe_form_name($data['form_id']);?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Subscriber Name','contact-form-with-shortcode');?></strong></td>
			<td><?php echo $data['sub_name'] == ''?'NA':$data['sub_name'];?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Subscriber Email','contact-form-with-shortcode');?></strong></td>
			<td><?php echo $data['sub_email'];?></td>
		</tr>
		<tr>
			<td><strong><?php _e('IP','contact-form-with-shortcode');?></strong></td>
			<td><?php echo $data['sub_ip'];?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Added','contact-form-with-shortcode');?></strong></td>
			<td><?php echo $data['sub_added'];?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Status','contact-form-with-shortcode');?></strong></td>
			<td>
			<select name="sub_status">
				<option value="Active" <?php echo $data['sub_status'] =='Active'?'selected="selected"':''; ?>>Active</option>
				<option value="Inactive" <?php echo $data['sub_status'] =='Inactive'?'selected="selected"':''; ?>>Inactive</option>
				<option value="Deleted" <?php echo $data['sub_status'] =='Deleted'?'selected="selected"':''; ?>>Deleted</option>
			</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="<?php _e('Submit','contact-form-with-shortcode');?>" class="button" /></td>
		</tr>
	</table>
	</form>
	<?php
	}	
	
	public function lists(){
	$this->view_message();
	?>
	<h2>Subscriber</h2>
	<?php
		global $wpdb;
		$srch_extra = '';
		if(isset($_REQUEST['search']) and sanitize_text_field($_REQUEST['search']) == 'sub_search'){
			if(sanitize_text_field($_REQUEST['form_id'])){
				$srch_extra .= " and form_id='".sanitize_text_field($_REQUEST['form_id'])."'";
			}
		}
		$query = "SELECT * FROM ".$wpdb->prefix."contact_subscribers WHERE sub_status<>'Deleted' ".$srch_extra." ORDER BY sub_added DESC";
		$ap = new afo_paginate(10,$this->plugin_page);
		$data = $ap->initialize($query,sanitize_text_field(@$_REQUEST['paged']));
		
		echo $this->search_form();
		echo $this->table_start();
		echo $this->get_table_header();
		echo $this->get_table_body($data);
		echo $this->table_end();
		
		echo $ap->paginate($_REQUEST);
	}
	
    public function display_list() {
		echo '<div class="wrap">';
		if(isset($_REQUEST['action']) and sanitize_text_field($_REQUEST['action']) == 'cf_edit'){
			$this->edit();
		} else{
			$this->lists();
		}
		echo '</div>';
  }
  
  public function start_session(){
  	if(!session_id()){
		session_start();
	}
  }
}

function process_sub_data(){
	if(isset($_REQUEST['action']) and sanitize_text_field($_REQUEST['action']) == 'cf_delete'){
		
		if ( ! isset( $_REQUEST['cfwsp_nonce_field'] ) || ! wp_verify_nonce( $_REQUEST['cfwsp_nonce_field'], 'cfwsp_nonce' ) ) {
		   wp_die( 'Sorry, your nonce did not verify.');
		} 
			
		global $wpdb;
		$slc = new subscribers_list_class;
		$update = array('sub_status' => 'Deleted');
		$data_format = array( '%s' );
		$where = array('sub_id' => intval(sanitize_text_field($_REQUEST['id'])));
		$data_format1 = array( '%d' );
		$rr = $wpdb->update( $wpdb->prefix."contact_subscribers", $update, $where, $data_format, $data_format1 );
		$slc->add_message(__('Subscriber deleted successfully.','contact-form-with-shortcode'), 'success');
		wp_redirect($slc->plugin_page);
		exit;
	}
	
	if(isset($_REQUEST['action']) and sanitize_text_field($_REQUEST['action']) == 'sub_edit'){
		global $wpdb;
		$slc = new subscribers_list_class;
		$update = array('sub_status' => sanitize_text_field($_REQUEST['sub_status']));
		$data_format = array( '%s' );
		$where = array('sub_id' => intval(sanitize_text_field($_REQUEST['sub_id'])));
		$data_format1 = array( '%d' );
		$wpdb->update( $wpdb->prefix."contact_subscribers", $update, $where, $data_format, $data_format1 );
		$slc->add_message(__('Subscriber updated successfully','contact-form-with-shortcode'), 'success');
		wp_redirect($slc->plugin_page."&action=cf_edit&id=".sanitize_text_field($_REQUEST['sub_id']));
		exit;
	}
	
}

