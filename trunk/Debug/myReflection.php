<?php
/**
 * php5 Reflection API : http://fr2.php.net/manual/en/language.oop5.reflection.php
 *
 * @package myReflection
 */
class myReflection extends ReflectionClass
{
    public function __construct($className)
    {
        parent::__construct($className);
    }

    // public function getAPI :
    /**
     * public members : methods & properties
    */
    public function getAPI ($restrictedToClass = '')
    {
      $ret = $fullApi = $this->getMethods(ReflectionMethod::IS_PUBLIC);
	  if (!empty($restrictedToClass) ) {
		$ret = $restrictedApi = $this->filter($restrictedToClass, $fullApi);
	  }
      // $this->getProperties(ReflectionProperty::IS_PUBLIC); ?
	  return $ret;
    }

    /**
     *
     */
    public function showAPI($restrictedToClass = '')
    {
      $A = $this->getAPI($restrictedToClass);
	  if (empty($restrictedToClass) ) {
	  $ret = '<h4>API for class ' . $this->getName() . '</h4>';
	  } else {
	  $ret = '<h4>API ***restricted*** to class ' . $restrictedToClass . '</h4>';
	  }
      $ret .= '<table>';
      foreach ($A as $Method) {
        $ret .= '<tr>';
        $ret .= '<td>' . $Method->getName() . '</td>';
        $ret .= '<td>' . $Method->getDocComment() . '</td>';
		// $ret .= '<td>' . $Method->getDeclaringClass()->getName() . '</td>';
        $ret .= '</tr>';
      }
      $ret .= '</table>';

      return $ret;
    }
	
	/*** PROTECTED ***/
	protected function filter($class, $api)
	{
	$ret = array();
		foreach ($api AS $Method) {
		if ($Method->getDeclaringClass()->getName() !== $class) { continue; }
			$ret[] = $Method;
		}
		return $ret;
	}
}