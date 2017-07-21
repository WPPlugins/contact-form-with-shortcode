<?php
class contact_db_list_class {
    
	public $plugin_page;
	
	public $plugin_page_base;
	
	public $plugin_table = "contact_stored_data";
	
	public static $status_array = array( 'processing', 'attending', 'unresolved', 'resolved' );
	
    public function __construct(){
      $this->plugin_page_base = 'contact_form_afo_db_data';
	  $this->plugin_page = admin_url('admin.php?page='.$this->plugin_page_base);
    }
	
	public static function sd_data_selected( $sel = '' ){
		$ret = '';
		if(is_array(self::$status_array)){
			foreach( self::$status_array as $value ){
				if($sel == $value){
					$ret .= '<option value="'.$value.'" selected="selected">'.ucfirst($value).'</option>';
				} else {
					$ret .= '<option value="'.$value.'">'.ucfirst($value).'</option>';
				}
			}
		}
		return $ret;
	}
	
	public function get_table_colums(){
		$colums = array(
		'sd_id' => __('ID','contact-form-with-shortcode'),
		'con_id' => __('Contact Form','contact-form-with-shortcode'),
		'sd_data' => __('Data','contact-form-with-shortcode'),
		'sd_added' => __('Added','contact-form-with-shortcode'),
		'sd_ip' => __('IP','contact-form-with-shortcode'),
		'sd_status' => __('Status','contact-form-with-shortcode'),
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
		
		$ret .= $this->row_actions($value['sd_id']);
		return $ret;
	}
	
	public function row_actions($id){
		return '<td><a href="'.$this->plugin_page.'&action=view&id='.$id.'">'.__('View','contact-form-with-shortcode').'</a> | <a href="'.$this->plugin_page.'&action=cf_data_delete&id='.$id.'">'.__('Delete','contact-form-with-shortcode').'</a></td>';
	}
	
	public function row_data($key = '',$value = ''){
		$v = '';
		switch ($key){
			case 'sd_id':
			$v = $value;
			break;
			case 'con_id':
			$v = get_contact_form_name($value);
			break;
			case 'sd_data':
			$v = get_contact_stored_data_for_list($value);
			break;
			case 'sd_added':
			$v = $value;
			break;
			case 'sd_ip':
			$v = $value;
			break;
			case 'sd_status':
			$v = ucfirst($value);
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
		$ret = '';
		$cnt = 0;
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
		$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->plugin_table." where sd_id = %d", $id );
		$result = $wpdb->get_row( $query, ARRAY_A );
		return $result;
	}
	
	public function prepare_data(){
		global $wpdb;
		$query = "SELECT * FROM ".$wpdb->prefix.$this->plugin_table." where sd_id<>'0' order by sd_added desc";
		//$data = $wpdb->get_results($query,ARRAY_A);
		$ap = new afo_paginate(1,$this->plugin_page);
		$data = $ap->initialize($query,0);
		return $data;
	}
	
	public function search_form(){
	?>
	<form name="s" action="" method="get">
	<input type="hidden" name="page" value="<?php echo $this->plugin_page_base;?>" />
	<input type="hidden" name="search" value="contact_data_search" />
	<table width="100%" border="0" style="background-color:#ffffff; margin-bottom:10px; padding:5px;">
	  <tr>
		<td align="left">
        <select id="c_form_id" name="c_form_id">
			<option value="">-</option>
			<?php $this->contactFormSelected(sanitize_text_field($_REQUEST['c_form_id']));?>
		</select>
        <input type="submit" name="submit" value="Filter" class="button"/>
        </td>
	  </tr>
	</table>
	</form>
	<?php
	}
	
	public function contactFormSelected($sel){
		$args = array( 'post_type' => 'contact_form', 'posts_per_page' => -1 );
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
	
	
	public function wrap_start(){
		return '<div class="wrap">';
	}
		
	public function wrap_end(){
		return '</div>';
	}
	
	public function view(){
	$id = $_REQUEST['id'];
	$data = $this->get_single_row_data($id);
	$sdata = unserialize($data['sd_data']);
	?>
    <form name="f" action="" method="post">
	<input type="hidden" name="sd_id" value="<?php echo $id;?>" />
	<input type="hidden" name="action" value="sd_data_edit" />
	<h2><?php _e('Details','contact-form-with-shortcode');?></h2>
	<table width="100%" border="0" cellspacing="10" style="background-color:#FFFFFF; padding:5px; border:1px solid #CCCCCC;">
		<tr>
			<td><strong><?php _e('Contact Form','contact-form-with-shortcode');?></strong></td>
			<td><?php echo get_contact_form_name( $data['con_id'] );?></td>
		</tr>
		<tr>
			<td><strong><?php _e('Added On','contact-form-with-shortcode');?></strong></td>
			<td><?php echo $data['sd_added'];?></td>
		</tr>
        <tr>
			<td><strong><?php _e('IP','contact-form-with-shortcode');?></strong></td>
			<td><?php echo $data['sd_ip'];?></td>
		</tr>
        <tr>
			<td colspan="2"><h3><?php _e('Form Data','contact-form-with-shortcode');?></h3></td>
		</tr>
        <?php
			if(is_array($sdata['data'])){
				foreach($sdata['data'] as $key => $value){
		?>
		<tr>
			<td><strong><?php echo $key;?></strong></td>
			<td><?php echo stripslashes($value);?></td>
		</tr>
        <?php
		}
			}
			?>
            
         <tr>
        <td colspan="2"><h4><?php _e('Attachments','contact-form-with-shortcode');?></h4></td>
    </tr>
    <?php
        if(is_array($sdata['attachments'])){
            foreach($sdata['attachments'] as $key => $value){
    ?>
    <tr>
        <td colspan="2"><?php echo $value;?></td>
    </tr>
    <?php
    }
        }
        ?>
        
        <tr>
			<td><strong>Status</strong></td>
			<td><select name="sd_status">
            <?php echo $this->sd_data_selected( $data['sd_status'] );?>
            </select></td>
		</tr>
        <tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Update" class="button"></td>
		</tr>
	</table>
    </form>
	<?php
	}	
	
	public function lists(){
	$this->view_message();
	$this->contact_wid_pro_add();
	?>
	<h2><?php _e('Stored Data','contact-form-with-shortcode');?></h2>
	<?php
		global $wpdb;
		$srch_extra = '';
		if(isset($_REQUEST['search']) and $_REQUEST['search'] == 'contact_data_search'){
			if($_REQUEST['c_form_id']){
				$srch_extra .= " and con_id='".intval(sanitize_text_field($_REQUEST['c_form_id']))."'";
			}
		}
		$query = "SELECT * FROM ".$wpdb->prefix.$this->plugin_table." where sd_id<>'0' ".$srch_extra." order by sd_added desc";
		$ap = new afo_paginate(10,$this->plugin_page);
		$data = $ap->initialize($query,@$_REQUEST['paged']);
		
		echo $this->wrap_start();
		echo $this->search_form();
		echo $this->table_start();
		echo $this->get_table_header();
		echo $this->get_table_body($data);
		echo $this->table_end();
		echo $ap->paginate($_REQUEST);
		echo $this->wrap_end();
	}
	
    public function display_list() {
		echo '<div class="wrap">';
		if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'view'){
			$this->view();
		} else{
			$this->lists();
		}
		echo '</div>';
  }
  
  public function contact_wid_pro_add(){ ?>
        <table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55; padding:0px 0px 0px 10px; margin:2px 0px;">
      <tr>
        <td><p>In the <strong>PRO</strong> version you can create a new type of user <strong>Query Manager</strong>. Query Managers will be able to <strong>Attend</strong> user quaries, <strong>Reply</strong> back to user directly from admin panel, <strong>Export</strong> query data, <strong>Change Status</strong> of the queries for easy management <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">Click here for details</a></p></td>
      </tr>
    </table>
        <?php 
	}
  
  public function start_session(){
  	if(!session_id()){
		@session_start();
	}
  }
}

function process_contact_stored_data(){
	if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'cf_data_delete'){
		global $wpdb;
		$cdl = new contact_db_list_class;
		$where = array('sd_id' => sanitize_text_field($_REQUEST['id']));
		$data_format = array( '%d' );
		$rr = $wpdb->delete( $wpdb->prefix.$cdl->plugin_table, $where, $data_format );
		$cdl->add_message(__('Data deleted successfully.','contact-form-with-shortcode'), 'success');
		wp_redirect($cdl->plugin_page);
		exit;
	}	
	
	if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'sd_data_edit'){
		global $wpdb;
		$cdl = new contact_db_list_class;
		$update = array('sd_status' => sanitize_text_field($_REQUEST['sd_status']));
		$update_format = array( '%s' );
		$where = array('sd_id' => sanitize_text_field($_REQUEST['id']));
		$where_format = array( '%d' );
		$rr = $wpdb->update( $wpdb->prefix.$cdl->plugin_table, $update, $where, $update_format, $where_format );
		$cdl->add_message(__('Status updated successfully.','contact-form-with-shortcode'), 'success');
		wp_redirect($cdl->plugin_page.'&action=view&id='.sanitize_text_field($_REQUEST['id']));
		exit;	
	}		
}
