<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
/**
 * mySfValidatorFile adds options to sfValidatorFile
 */
class mySfValidatorFile extends sfValidatorFile
{
  /**
   * Configures the current validator.
   *
   * Supplementary options :
   *
   *  * width:           array('min' => <min>, 'max' => <max>)
   *  * height:          array('min' => <min>, 'max' => <max>)
   *
   * @see sfValidatorFile
   */
  protected function configure($options = array(), $messages = array())
  {
	  parent::configure($options, $messages);
	  $this->addOption('max_width');
	  $this->addOption('min_width');
	  $this->addOption('max_height');
	  $this->addOption('min_height');
  }

  /**
   * @return sfValidatedFile object.
   */
  protected function doClean($value)
  {
   
    if (!isset($value['height']))
    {
      $t = getimagesize($value['tmp_name']);
	  $value['width']   = $t[0];
	  $value['height']  = $t[1];
    }
    // check file size
	$this->checkMax('width', $value);
	$this->checkMax('height', $value);

	return parent::doClean($value);
  }

  protected function checkMax($dim, $value)
  {
  if ($this->hasOption('max_' . $dim) && $this->getOption('max_' . $dim) < (int) $value[$dim] )
    {
      throw new sfValidatorError(
	  $this, 
	  'max_' . $dim, 
	  array('max_' . $dim => $this->getOption('max_' . $dim), $dim => (int) $value[$dim])
	  );
    }
  }
  
}
