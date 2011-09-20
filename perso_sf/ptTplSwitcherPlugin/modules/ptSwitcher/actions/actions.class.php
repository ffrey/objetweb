<?php

class ptSwitcherActions extends sfActions
{
	public function executeIndex(sfWebRequest $Req)
	{
		// 		var_dump(sfConfig::getAll() ); exit;
		if (false) {
			$pt_switcher_config_file = sfConfig::get('sf_plugins_dir').DS.'ptTplSwitcherPlugin'.DS.'config'.DS.'app.yml';
			$config = sfYaml::load($pt_switcher_config_file);
			var_dump($config); exit;
		}
	}

	public function execute_deactivate(sfWebRequest $req)
	{
		$req->setParameter('tpl', 'none');
		$this->forward('ptSwitcher', '_activate');
	}

	/**
	 * sets (or unsets) the default set of templates
	 * 
	 * 1. set a new entry in ptTplSwitcherPlugin/config/app.yml
	 * 2. clears the cache so as to ensure both config & template caches are cleared
	 * 
	 * @param sfWebRequest $req
	 */
	public function execute_activate(sfWebRequest $req)
	{
		$tpl = $req->getParameter('tpl', 'none');
		$ok = ptTplSwitcher::setCurrentTpl($tpl);
		/**
		 * 		$pt_switcher_config_file = sfConfig::get('sf_plugins_dir').DS.'ptTplSwitcherPlugin'.DS.'config'.DS.'app.yml';
		$config = sfYaml::load($pt_switcher_config_file);
		$config['all']['ptTplSwitcher']['tplCurrent'] = $tpl;
		$yaml = sfYaml::dump($config);
		$ok = file_put_contents($pt_switcher_config_file, $yaml);
		 */
		do {
			// vider le cache !
			$subDir = sfConfig::get('sf_cache_dir');
			$dirs   = sfFinder::type('dir')->not_name('.*')->in($subDir);
			foreach ($dirs AS $dir) {
				sfToolkit::clearDirectory($dir);
			}
			$this->logMessage('ptSwitcher :: clear : ' . implode(', ',$dirs) );
			
			$status     = 'nok';
			$status_msg = 'erreur technique';
			if ($this->isSecure() AND !$this->getUser()->isAuthenticated() ) {
				$status_msg = '[vous devez être connecté pour utiliser le ptSwitcher]';
				break;
			}
			if (!$ok) {	break; }
			$this->getUser()->setAttribute('ptSwitcher.current_tpl', $tpl);
			$status_msg = sprintf('Templates activés : %s', $tpl);
			if ('none' == strtolower($tpl) ) {
				$status_msg = 'ptTplSwitcher est désactivé.';
			}
			$status = 'ok';
		} while (false);
		$msg = sprintf('{
		  "status": "%s",
		  "status_msg": "%s",
		  "current_tpl": "%s"
		}', $status, $status_msg, $tpl);
		$this->renderText($msg);

		return sfView::NONE;
	}

	public function executeShowAllDefaultTemplates(sfWebRequest $req) 
	{	
		$apps = array();
		$list = sfFinder::type('dir')->maxdepth(0)->in(sfConfig::get('sf_apps_dir') );
		foreach ($list AS $app) {
			$base = basename($app);
			$apps[$base] = $base;
		}
		$apps = array_merge(array('tous' => 'tous'), $apps);
		$this->apps = $apps;
	}

	public function execute_showTemplates(sfWebRequest $req)
	{
		$ret = '{"list":[';
		$app  = $req->getParameter('app', null);
		$app  = 'tous' == $app? null : $app;
		$list = ptTplSwitcher::getListOfDefaultTpls($app);
		if (count($list) ) {
			foreach ($list AS $dir) {
				$ret .= '"'.$dir.'",';
			}
			$ret = substr($ret, 0, strlen($ret)-1);
		}
		$ret .= ']}';
		$this->renderText($ret);
		return sfView::NONE;
	}

}
