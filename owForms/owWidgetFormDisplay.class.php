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
class owWidgetFormDisplay extends sfWidgetForm {

  
	protected protected function configure($options = array(), $attributes = array()) {
		parent::configure ( $options, $attributes );
		$this->addOption ( 'display_value', '' );
		if (key_exists ( 'display_value', $options )) {
			$this->setOption ( 'display_value', $options ['display_value'] );
		}
	}
	/**
	 * @param  string $name        The element name
	 * @param  string $value       The value displayed in this widget
	 * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
	 * @param  array  $errors      An array of errors for the field
	 *
	 * @return string An HTML tag string
	 *
	 * @see sfWidgetForm
	 */
	public function render($name, $value = null, $attributes = array(), $errors = array()) {
		// myUtil::db ( $this->getOption('display_value') );
		$value = ($this->getOption('display_value') ) ? $this->getOption('display_value') : $value;
		if (null === $value) { $value = '[]'; } // WORKAROUND : when empty, line is crushed (display pb only)
		return $value;
	}
}
