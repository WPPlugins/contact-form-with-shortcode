<?php
$template_files = array('default_template' => array('name' => 'Default Template', 'path' =>  dirname( __FILE__ ) . '/template/default.php', 'loop_path' => dirname( __FILE__ ) . '/template/loop.php'));

function select_template($id){
	global $template_files;
	$template_id = get_post_meta( $id, '_templete_file', true );
	$template_file_path = $template_files[$template_id]['path'];
	if(file_exists($template_file_path)){
		return $template_file_path;
	} else {
		return $template_files['default_template']['path'];
	}
}

function select_template_loop($id){
	global $template_files;
	$template_id = get_post_meta( $id, '_templete_file', true );
	$template_file_path = $template_files[$template_id]['loop_path'];
	if(file_exists($template_file_path)){
		return $template_file_path;
	} else {
		return $template_files['default_template']['loop_path'];
	}
}

function templates_selected($sel = ''){
	global $template_files;
	foreach($template_files as $key => $value){
		$ret .= '<option value="'.$key.'" '.($sel == $key?'selected="selected"':'').'>'.$value['name'].'</option>';
	}
	return $ret;
}