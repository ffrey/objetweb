<?PHP
/**
 * management of javascript : online / local
 @uses ! A mecanism must have set $is_local before-hand !
 @use sfConfig + variables in app.yml
 */
 function ow_use_javascript($js, $is_local, $position = '', $options = array() ) {
	//  get javascripts
	$js_list = sfConfig::get('app_assets_javascripts');
	// 	var_dump($js_list);
	// exit;
	if (!key_exists($js, $js_list) ) {
		throw new Exception('Unknown javascript : ' . $js);
	}
	$files = $js_list[$js];
	if (!key_exists('local', $files) OR !key_exists('online', $files) ) {
		throw new Exception('Mandatory key missing for javascript : ' . $js);
	}
	$file = ($is_local)? $js_list[$js]['local'] : $files['online'];
	
	//	print $file;
	if (!empty($file) ) { // if entry is empty => we do want to include for this environment
		use_javascript($file, $position = '', $options = array() ); 
	}
	/**/
 }