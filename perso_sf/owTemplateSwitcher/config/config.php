<?php

if (sfConfig::get('ow_template_switcher_activated', false))
{
	$design_app_dir = sfConfig::get('sf_web_dir') . '/_design_templates/apps/' . $this->application;
	sfConfig::set('sf_module_dirs', array(
	$design_app_dir . '/modules' => true)
	);
	if (is_readable($design_app_dir . '/templates/layout.php'))
	{
		sfConfig::set('sf_app_template_dir', $design_app_dir . '/templates');
	}
}