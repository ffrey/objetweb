<?php

/*
 * This file is part of the owTemplateSwitcher plugin
 */

/**
 * Create a front-controller + directories needed
 *
 * @package    symfony
 * @subpackage task
 * @author     François Freyssenge
 */
class owTemplateSwitcherBuildFrontTask extends sfBaseTask
{
	/**
	 * @see sfTask
	 */
	protected function configure()
	{
		$this->addArguments(array(
		new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
		));

		$this->aliases = array('ow-build-front');
		$this->namespace = 'owTemplateSwitcher';
		$this->name = 'build-front';
		$this->briefDescription = 'Creates needed files in public dir : front-controller + template directories';

		$this->detailedDescription = <<<EOF
		This task creates a front-controller <app>_design.php. This controller looks for any needed template
		 first inside the <public_directory>/_design_templates/ dir, and then inside the classical symfony structure.
		 Basically, this allows the existence of second set of template for any given application. The aim is
		 to ease co-working between designer and developer.
EOF;
	}

	/**
	 * @see sfTask
	 */
	protected function execute($arguments = array(), $options = array())
	{
		$app = $arguments['application'];
			
		// Create the directories
		$appDir = sfConfig::get('sf_apps_dir').'/'.$app;
		if (!is_dir($appDir))
		{
			throw new sfCommandException(sprintf('The application "%s" does not exist.', $appDir));
		}
		$tplDir = sfConfig::get('sf_web_dir').'/_design_templates/apps/' . $app;
		if (is_dir($tplDir))
		{
			throw new sfCommandException(sprintf('The templates directory "%s" already exists.', $tplDir));
		}
		$switcherController = $app.'_design.php';
		if (is_readable(sfConfig::get('sf_web_dir').'/'.$switcherController))
		{
			throw new sfCommandException(sprintf('The controller "%s" already exists.', $switcherController));
		}

		// Create basic template structure
		$finder = sfFinder::type('directory')->name('templates');
		$this->getFilesystem()->mirror($appDir, $tplDir, $finder);
		//  	sfFinder
			
		// create front-controller
		$this->getFilesystem()->copy(dirname(__FILE__).'/skeleton/web/index_design.php', sfConfig::get('sf_web_dir').'/'.$switcherController);
		// OW_TEMPLATE_SWITCHER_ACTIVATION <= sfConfig::set('ow_template_swither_activated', true);
		$this->getFilesystem()->replaceTokens(sfConfig::get('sf_web_dir').'/'.$switcherController, '##', '##', array(
	      'APP_NAME'    => $app,
	      'ENVIRONMENT' => 'dev',
	      'IS_DEBUG'    => 'true',
	      'OW_TEMPLATE_SWITCHER_ACTIVATION'    => 'sfConfig::set("ow_template_switcher_activated", true);'.PHP_EOL
		));

		$this->logSection('owTemplateSwitcher', sprintf('task was performed successfully'));
	}
}
