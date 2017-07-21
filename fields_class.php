<?php

if(!class_exists( 'fields_class' )){
class fields_class {
	
	public $fields = array('text' => 'Text', 'textarea' => 'Textarea', 'select' => 'Select', 'checkbox' => 'Checkbox', 'radio' => 'Radio Button', 'date' => 'Date', 'time' => 'Time', 'file' => 'File (Attachments)');
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'field_save_settings' ) );
		add_action( 'wp_head', array( $this, 'contact_afo_scripts_fr' ) );
	}
	
	public function field_save_settings(){
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "addNewField"){
			$field = sanitize_text_field($_REQUEST['field']);
			$this->newFieldForm($field);
			exit;
		}
		
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "saveField"){
			$field_type 		 = sanitize_text_field($_REQUEST['field_type']);
			$field_label 		 = sanitize_text_field($_REQUEST['field_label']);
			$field_name 		 = str_replace(" ","_",strtolower(trim(sanitize_text_field($_REQUEST['field_name']))));
			$field_desc 		 = sanitize_text_field($_REQUEST['field_desc']);
			$field_required 	 = sanitize_text_field($_REQUEST['field_required']);
			$field_values 		 = sanitize_text_field($_REQUEST['field_values']);
			
			echo $this->addedField($field_type,$field_label,$field_name,$field_desc,$field_required,$field_values);
			exit;
		}
	}
	
	public function contact_afo_scripts_fr(){
	?>
     <script type="application/javascript">
	function refreshCaptcha(){ jQuery(".captcha").attr("src", '<?php echo plugin_dir_url( __FILE__ ).'captcha/captcha.php'?>?rand=' + Math.random() ); }
	</script>
    <?php
	}
	
	public function get_field_desc($desc = ''){
		if($desc){
			return '<span class="field-desc">'.strip_tags(html_entity_decode($desc)).'</span>';
		}
	}
	
	public function genField($field = 'text',$name = '',$id='',$value = '', $desc = '', $options, $required=''){
		switch ($field){
			case 'text':
				echo '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" '.$required.'>';
				echo $this->get_field_desc($desc);
			break;
			case 'textarea':
				echo '<textarea name="'.$name.'" id="'.$id.'" '.$required.'>'.$value.'</textarea>';
				echo $this->get_field_desc($desc);
			break;
			case 'select':
				$options = explode(",",$options);
				echo '<select name="'.$name.'" id="'.$id.'" '.$required.'>';
					if(is_array($options)){
						foreach($options as $val){
							if($value == $val){
								echo '<option value="'.$val.'" selected="selected">'.$val.'</option>';
							} else {
								echo '<option value="'.$val.'">'.$val.'</option>';
							}
						}
					}
				echo '</select>';
				echo $this->get_field_desc($desc);
			break;
			case 'checkbox':
				$options = explode(",",$options);
					if(is_array($options)){
						foreach($options as $val){
							if(is_array($value) and in_array($val,$value)){
								echo ' <input type="checkbox" name="'.$name.'[]" id="'.$id.'" value="'.$val.'" checked="checked" '.$required.' />'.$val;
							} else {
								echo ' <input type="checkbox" name="'.$name.'[]" id="'.$id.'" value="'.$val.'" '.$required.'/>'.$val;
							}
						}
						echo $this->get_field_desc($desc);
						$this->checkboxJsCall($name);
					}
			break;
			case 'radio':
				$options = explode(",",$options);
					if(is_array($options)){
						foreach($options as $val){
							if($value == $val){
								echo ' <input type="radio" name="'.$name.'" id="'.$id.'" value="'.$val.'" checked="checked" '.$required.'/>'.$val;
							} else {
								echo ' <input type="radio" name="'.$name.'" id="'.$id.'" value="'.$val.'" '.$required.'/>'.$val;
							}
						}
					}
					echo $this->get_field_desc($desc);
			break;
			case 'date':
				echo '<input type="text" name="'.$name.'" class="wp_reg_date" id="'.$id.'" value="'.$value.'" '.$required.'>';
				echo $this->get_field_desc($desc);
				$this->dateJsCall();
			break;
			case 'time':
				echo '<input type="text" name="'.$name.'" class="wp_reg_time" id="'.$id.'" value="'.$value.'" '.$required.'>';
				echo $this->get_field_desc($desc);
				$this->dateJsCall();
			break;
			case 'file':
				echo '<input type="file" name="'.$name.'" id="'.$id.'" '.$required.'>';
				echo $this->get_field_desc($desc);
			break;
			default:
				echo '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" '.$required.'>';
				echo $this->get_field_desc($desc);
			break;
		}
	}
	
	
	public function dateJsCall(){?>
		<script type="text/javascript">
		jQuery(document).ready(function(jQuery) {
			jQuery('.wp_reg_date').datepicker({
				dateFormat : 'yy-mm-dd'
			});
			jQuery('.wp_reg_time').ptTimeSelect();
		});
		</script>
	<?php 
	}
	
	public function checkboxJsCall($name=''){?>
		<script type="text/javascript">
			jQuery('input[name="<?php echo $name;?>[]"]').click(function() {
			if(jQuery('input[name="<?php echo $name;?>[]"]:checked').length) {
					jQuery('input[name="<?php echo $name;?>[]"]').removeAttr('required');
				} else {
					jQuery('input[name="<?php echo $name;?>[]"]').attr('required', 'required');
				}
			 });
		</script>
	<?php 
	}
	
	public function contactFormFields($id = ''){
		if($id == '')
		return;
		
		$extra_fields = get_post_meta( $id, '_contact_extra_fields', true );
		$contact_enable_captcha = get_post_meta( $id, '_contact_enable_security', true );
		
		if(is_array($extra_fields)){
			foreach($extra_fields as $key => $value){
					if($value['field_required'] == 'Yes'){
						$required = 'required="required"';
					} else {
						$required = '';
					}
					?>
					<div class="form-group">
						<label for="<?php echo $value['field_label'];?>"><?php echo $value['field_label'];?></label>
						<?php $this->genField($value['field_type'], $value['field_name'], $value['field_name'], '', $value['field_desc'], $value['field_values'], $required);?>
					</div>
				<?php
			}
		}
		
		if($contact_enable_captcha == 'Yes'){ ?>
			<div class="form-group">
                <label for="<?php _e('Security Code','contact-form-with-shortcode');?>"><?php _e('Security Code','contact-form-with-shortcode');?></label>
                <div class="captcha_image"><?php $this->captchaImage();?></div>
				<input type="text" name="cont_captcha" id="cont_captcha" required="required" placeholder="<?php _e('Enter security code','contact-form-with-shortcode');?>"/>
            </div>
		<?php }
		
	}
	
	public function captchaImage(){
	?>
    <img src="<?php echo plugin_dir_url( __FILE__ ).'captcha/captcha.php';?>" class="captcha"> <a href="javascript:refreshCaptcha();"><?php _e('Reload Image','contact-form-with-shortcode');?></a>
    <?php
	}
	
	public function newFieldForm($field){
			echo '<div style="border:1px dashed #1E8CBE; padding:5px; margin:2px;">';
		switch ($field){
			case 'text':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<input type="hidden" name="field_values" id="field_values" value="not_required"/>';
			break;
			case 'textarea':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<input type="hidden" name="field_values" id="field_values" value="not_required"/>';
			break;
			case 'select':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<p><textarea name="field_values" id="field_values"></textarea> Enter field values separated by comma (,)</p>';
			break;
			case 'checkbox':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<p><textarea name="field_values" id="field_values"></textarea> Enter field values separated by comma (,)</p>';
			break;
			case 'radio':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<p><textarea name="field_values" id="field_values"></textarea> Enter field values separated by comma (,)</p>';
			break;
			case 'date':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<input type="hidden" name="field_values" id="field_values" value="not_required"/>';
			break;
			case 'time':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<input type="hidden" name="field_values" id="field_values" value="not_required"/>';
			break;
			case 'file':
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<input type="hidden" name="field_values" id="field_values" value="not_required"/>';
			break;
			default:
				echo '<p>Field Label <input type="text" name="field_label" id="field_label" placeholder="Field Label" required/></p>';
				echo '<p>Field Name <input type="text" name="field_name" id="field_name" placeholder="Field Name" required/><span>Use only letters</span></p>';
				echo '<p>Field Description <input type="text" name="field_desc" id="field_desc" placeholder="Field Description"/></p>';
				echo '<p>Field is required <select name="field_required" id="field_required"><option value="Yes">Yes</option><option value="No">No</option></select></p>';
				echo '<input type="hidden" name="field_values" id="field_values" value="not_required"/>';
			break;
		}
			echo '<p><input type="button" name="save" value="Add" class="button button-primary button-large" onclick="saveField(\''.$field.'\');">&nbsp;<input type="button" name="del" value="Delete" class="button button-primary button-large" onclick="delField(this);"></p>';
			echo '</div>';
	}
	
	
	public function fieldList(){
		$ret  = '<select name="field_list" id="field_list" onchange="selectField(this)">';
		$ret .= '<option value="">--</option>';
		foreach($this->fields as $key => $value){
			$ret .= '<option value="'.$key.'">'.$value.'</option>';
		}
		$ret .= '</select>';
		return $ret;
	}
	
	public function addedField($field_type,$field_label,$field_name,$field_desc,$field_required,$field_values){
		$ret  = '<div style="border:1px dotted #333333; padding:5px; height:80px; overflow:hidden; margin:2px;">';
		$ret .= '<div style="min-height: 37px;">';
		
		$ret .= '<font color="#BF4237;">Label:</font> '.substr($field_label,0,10);
		$ret .= ',&nbsp;';
		$ret .= '<font color="#BF4237;">Name:</font> '.substr($field_name,0,10);
		$ret .= ',&nbsp;';
		$ret .= '<font color="#BF4237;">Type:</font> '.substr($field_type,0,10);
		$ret .= ',&nbsp;';
		if($field_required == 'Yes'){
			$ret .= '<font color="#BF4237;">Required</font>,&nbsp;';
		}
		$ret .= '<font color="#BF4237;">Desc:</font> '.substr($field_desc,0,10).'..,&nbsp;';
		$ret .= '<font color="#BF4237;">Mail Body Code:</font> #'.$field_name.'#';
		
		$ret .= '</div>';
		
		$ret .= '<p><input type="button" name="edit" value="Edit" style="margin-right:2px;" class="button button-primary button-large" onclick="editField(this);">';
		
		$ret .= '<input type="button" name="del" value="Delete" style="" class="button button-primary button-large" onclick="delField(this);"></p>';
		
		switch ($field_type){
			case 'text':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				
				$ret .= '<input type="hidden" name="field_values_array[]" value="not_required"/>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			case 'textarea':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				$ret .= '<input type="hidden" name="field_values_array[]" value="not_required"/>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			case 'select':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				
				$ret .= '<p><textarea name="field_values_array[]">'.$field_values.'</textarea> Enter field values separated by comma (,)</p>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			case 'checkbox':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				
				$ret .= '<p><textarea name="field_values_array[]">'.$field_values.'</textarea> Enter field values separated by comma (,)</p>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			case 'radio':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				
				$ret .= '<p><textarea name="field_values_array[]">'.$field_values.'</textarea> Enter field values separated by comma (,)</p>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			case 'date':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				
				$ret .= '<input type="hidden" name="field_values_array[]" value="not_required"/>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			case 'time':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				$ret .= '<input type="hidden" name="field_values_array[]" value="not_required"/>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			case 'file':
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				$ret .= '<input type="hidden" name="field_values_array[]" value="not_required"/>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
			default:
				$ret .= '<p>Field Label <input type="text" name="field_labels[]" placeholder="Field Label" value="'.$field_label.'" required/></p>';
				$ret .= '<p>Field Name <input type="text" name="field_names[]" placeholder="Field Name" value="'.$field_name.'" required/><span>Use only letters</span></p>';
				$ret .= '<p>Field Description <input type="text" name="field_descs[]" value="'.$field_desc.'"  placeholder="Field Description"/></p>';
				
				$ret .= '<p>Field is required <select name="field_requireds[]"><option value="Yes" '.($field_required=='Yes'?'selected="selected"':'').'>Yes</option><option value="No" '.($field_required=='No'?'selected="selected"':'').'>No</option></select></p>';
				
				$ret .= '<input type="hidden" name="field_values_array[]" value="not_required"/>';
				$ret .= '<input type="hidden" name="field_types[]" value="'.$field_type.'"/>';
			break;
		}
	
		$ret .= '<p><input type="button" name="close" value="Close" style="margin-right:2px;" class="button button-primary button-large" onclick="closeField(this);"></p>';
		
		
		$ret .= '</div>';
		return $ret;
	}
	
	public function savedExtraFields($extra_fields){
		if(is_array($extra_fields)){
			foreach($extra_fields as $key => $value){
				echo $this->addedField($value['field_type'],$value['field_label'],$value['field_name'],$value['field_desc'],$value['field_required'],$value['field_values'] );
			}
		}
	}
	
	public function LoadFieldJs(){ ?>
		<script type="text/javascript">
			function selectField(t){
				addNewField(t.value);
			}
			
			function delField(t){
				jQuery(t).closest('div').remove();
			}
			
			function editField(t){
				jQuery(t).closest('div').css({'overflow':'auto','height':'auto'});
			}
			
			function closeField(t){
				jQuery(t).closest('div').css({'overflow':'hidden','height':'80px'});
			}
			
			function addNewField(field){
				jQuery.ajax({
					type: 'POST',
					async : false,
					data: { option : "addNewField", field : field},
					success: function(data) {
						jQuery('#newFieldForm').html(data);
					}
				});
			}
			
			function saveField(field_type){
										
				jQuery.ajax({
					type: 'POST',
					async : false,
					data: {option : "saveField", field_type : field_type, field_label : jQuery('#field_label').val(), field_name : jQuery('#field_name').val(), field_desc : jQuery('#field_desc').val(), field_required : jQuery('#field_required').val(), field_values : jQuery('#field_values').val()},
					success: function(data) {
						jQuery('#newFields').append(data);
						jQuery('#field_list').val('');
						jQuery('#field_label').val('');
						jQuery('#field_name').val('');
						jQuery('#field_desc').val('');
						jQuery('#field_required').val('');
						jQuery('#field_values').val('');
						jQuery('#newFieldForm').html('');
						
					}
				});
			}
		</script>
	<?php 
	} 
}
}
