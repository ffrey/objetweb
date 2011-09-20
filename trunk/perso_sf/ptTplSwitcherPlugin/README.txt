#
pour fonctionner : 
## activer le plugin dans /config/ProjectConfiguration.class.php

### AU NIVEAU DU PROJET
=== $this->enablePlugins(<...>, 'ptTplSwitcherPlugin');
###
// si 'none' => désactivation des templates alternatifs (gain de temps de génération)
// le dossier de templates alternatifs doit se trouver sous le dossier des templates tplSwitcher => @see ptTplSwitcherPlugin/config/app.yml !
sfConfig::set('pt_tpl_switcher_tpl_type', '<nom du dossier de templates alternatifs>'); 


### AU NIVEAU DE CHAQUE APPLICATION
## dans chaque fichier <app>Configuration.class.php des applications o� l'on souhaite travailler avec le plugin : 
//  sfApplicationConfiguration
+++ require_once ROOT.DS.'plugins'.DS.'ptTplSwitcherPlugin'.DS.'lib'.DS.'ptTplSwitcherApplicationConfiguration.class.php';
+++ class <app>Configuration extends ptTplSwitcherApplicationConfiguration 
--- class <app>Configuration extends sfApplicationConfiguration 

## activer le module dans <app>/config/settings.yml
---   enabled_modules:      [default, <...>]
+++   enabled_modules:      [default, <...>, switch]
