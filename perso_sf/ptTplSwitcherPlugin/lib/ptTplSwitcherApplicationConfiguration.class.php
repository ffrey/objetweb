<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if (!defined('DS') ) {
	define('DS', DIRECTORY_SEPARATOR);
}
/**
 * sfConfiguration represents a configuration for a symfony application.
 * @todo :: 2011 05 06
 * http://pilotage.pubtech.fr/flyspray/index.php?do=details&task_id=1377
 */
abstract class ptTplSwitcherApplicationConfiguration extends sfApplicationConfiguration
{
	protected
	 $tpl_type_none = 'none'; // case insensitive : de-activates additional tpl directory
	 
	/**
	 * adds a directory for templates
	 */
	public function getTemplateDirs($moduleName)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$dirs = parent::getTemplateDirs($moduleName);
		// var_dump(sfConfig::getAll() ); exit;
		if ($this->hasTplType() ) {
			$alt = ptTplSwitcher::getTplSwitcherDir().DS.sfConfig::get('sf_app').DS.$moduleName;
			array_unshift($dirs, $alt);
			if ($db) {
				var_dump($origin, $dirs, sfConfig::getAll() );
			}
		}
		return $dirs;
	}

	/**
	 * adds a directory for decorators
	 */
	public function getDecoratorDirs()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;

		if ($this->hasTplType() ) {
			$alt = ptTplSwitcher::getTplSwitcherDir().DS.sfConfig::get('sf_app').DS.sfConfig::get('sf_module');
			$alt = substr($alt, 0, strlen($alt)-1);
			$dirs = array(
			$alt,
			sfConfig::get('sf_app_template_dir'),
			);
			if ($db) {
				var_dump($origin, $dirs);
			}
		} else {
			$dirs = parent::getDecoratorDirs();
		}
		return $dirs;
	}
	
	public function hasTplType()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$tpl_type = ptTplSwitcher::getCurrentTpl();
		$ret = $this->tpl_type_none == strtolower($tpl_type);
		if ($db) {
			var_dump($origin, $ret);
		}
		return !$ret;
	}
}

