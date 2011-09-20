<?php
/**
 * @uses sfConfig, sfYaml
 * @author ffrey
 */
class ptTplSwitcher 
{
	/**
	 * allows change of current set of templates
	 * 
	 * templates will be loaded in that order : 
	 * - ptTplSwitcher_tplCurrent
	 * - ptTplSwitcher_tplDefault
	 * - symfony template dir
	 * 
	 * @param boolean
	 */
	static public function setCurrentTpl($tpl)
	{
		$origin = __CLASS__.'::'.__FUNCTION__; $db = true;		
		$ret = true;
		$enabled_modules = sfConfig::get('sf_enabled_modules');
		if (in_array('switch', $enabled_modules) ) { 
			// this allows for setting the current tpl "permanently" (for all sessions accessing the site)
			$pt_switcher_config_file = sfConfig::get('sf_plugins_dir').DS.'ptTplSwitcherPlugin'.DS.'config'.DS.'app.yml';
			$config = sfYaml::load($pt_switcher_config_file);
			$config['all']['ptTplSwitcher']['tplCurrent'] = $tpl;
			$yaml = sfYaml::dump($config);
			$ret = file_put_contents($pt_switcher_config_file, $yaml);
		}
		sfConfig::set('app_ptTplSwitcher_tplCurrent', $tpl);
		// if ($db) { var_dump($origin, $tpl); }
		return $ret;
	}
	
	static public function getCurrentTpl()
	{
		return sfConfig::get('app_ptTplSwitcher_tplCurrent', sfConfig::get('app_ptTplSwitcher_tplDefault') );
	}
	
	static public function getTplSwitcherDir()
	{
		$selected_tpl_type = self::getCurrentTpl();
		return sfConfig::get('sf_root_dir').DS.sfConfig::get('app_ptTplSwitcher_tplDir').DS.$selected_tpl_type;
	}
	
	static public function getListOfDefaultTpls($app = null)
	{
		// list all apps
		$dir = sfConfig::get('sf_apps_dir');
		if (null != $app) {
			$dir = $dir.DS.$app;
		}
		$tpl_dirs = sfFinder::type('dir')->name('templates')->in($dir);
		$tpl_files = sfFinder::type('file')->name('*.php')->not_name('sauv.*')->in($tpl_dirs);
		
		return $tpl_files;
	}
}
