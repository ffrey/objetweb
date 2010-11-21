<?php

/*
* This file is part of the symfony package.
* (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * owDebugFilter prints content of user-defined session values
 *
 * @package    symfony
 * @subpackage filter
 * @author     François Freyssenge
 *
 * @uses muUser::getUserContext() !
 */
class owDebugFilter extends sfFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array('BEFORE CACHE')));
    }
    $this->context->getRouting()->setDefaultParameter('attr', 5); // ->setParameter('attr', 5);
    // execute next filter
    $filterChain->execute();
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array('AFTER CACHE')));
      do {
      	$app = sfContext::getInstance()->getConfiguration();
        $environment = $app->getEnvironment();
        //
        if ('dev' !== $environment) { break; }
        // execute this filter only once
        $response = $this->context->getResponse();

        // include javascripts and stylesheets
        $content = $response->getContent();
        
        if (false !== ($pos = strpos($content, '</body>'))) // let us skip ajax requests for instance...
        {
		 $html = '<!-- owCommonFilter !-) -->';
          $User = $this->context->getUser();
          // sfLoader::loadHelpers(array('Tag', 'Asset'));
          $app->loadHelpers(array('Tag', 'Asset') );
          $html = '<div class="owDebugBar">User Context : ';
          $html .= $User->getUserContext();
          $html .= '</div>';
        
		  $response->setContent(substr($content, 0, $pos).$html.substr($content, $pos));
		}
      } while (false);
    }

  }
}
