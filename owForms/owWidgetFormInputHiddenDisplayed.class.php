<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInput displays a value without input within a form.
 *
 * @package    symfony
 * @subpackage widget
 */
class owWidgetFormInputHiddenDisplayed extends sfWidgetFormInputHidden
{
  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
  	$hiddenTag = parent::render($name, $value, $attributes, $errors);
  }*/
  public function renderTag($tag, $attributes = array())
  {
  	$hiddenTag = parent::renderTag($tag, $attributes);
  	    return $attributes['value'] . $hiddenTag;
  	
  }
}
