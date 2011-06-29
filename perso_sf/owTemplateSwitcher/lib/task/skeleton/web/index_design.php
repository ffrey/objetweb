<?php
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

##OW_TEMPLATE_SWITCHER_ACTIVATION##

$configuration = ProjectConfiguration::getApplicationConfiguration('##APP_NAME##', '##ENVIRONMENT##', ##IS_DEBUG##);
sfContext::createInstance($configuration)->dispatch();
