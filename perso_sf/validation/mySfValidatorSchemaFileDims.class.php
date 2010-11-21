<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
/**
 * mySfValidatorFile adds options to sfValidatorFile
 */
class mySfValidatorSchemaFileDims extends sfValidatorSchema
{
  public function __construct($options = array(), $messages = array())
  {
    $this->addOption('throw_global_error', false);

    parent::__construct(null, $options, $messages);
  }


  /**
   * @return sfValidatedFile object.
   */
  protected function doClean($values)
  {
  // @todo : 'pub' must be made dynamic !
  // myUtil::db($values);
  $type = $this->extractType(array_keys($values) );
  if (null === $values[$type . '_file']) return $values; // no file was uploaded !
  $File = $values[$type . '_file'];
  $tmp = array();
  $tmp['pos'] = $values[$type . '_pos'];
  $tmp['type'] = $type;

      $t = getimagesize($File->getTempName() );
	  $tmp['width']   = $t[0];
	  $tmp['height']  = $t[1];
	$this->checkDim('width', $tmp);
	$this->checkDim('height', $tmp);

	return $values;
  }

  protected function checkDim($dim, $value)
  {
  // if ($this->hasOption('max_' . $dim) && $this->getOption('max_' . $dim) < (int) $value[$dim] )
	try {
		myFiles::checkDim($value['type'], $value['pos'], $dim, $value[$dim]);
	} catch (Exception $e) {
		throw new sfValidatorError(
			$this, 
			$e->getMessage()
		);
	}
  }
  protected function extractType($fieldNames)
  {
  foreach ($fieldNames AS $name) {
	  if (strpos($name, '_file') ) {
		  $tmp = explode('_', $name);
		  if (2 < count($tmp) ) { throw new Exception('incorrect field name : ' . $field . ' (only one "_" is allowed'); }
		  return $tmp[0];
	  }
  }
  }
}
