<?php
// phpunit C:\PROJETS_WEB\www\publicis_projets\projet_autodistribution_b2c\_SITE\test\phpunit\ptForm\FormHelperTest.php
require_once dirname(__FILE__).'/../../bootstrap/phpunit.php';
$app = 'frontend';
require_once dirname(__FILE__).'/../../bootstrap/functional.php';
require_once dirname(__FILE__).'/../../../lib/helper/FormHelper.php';
error_reporting(E_ALL); ini_set('display_errors', true);
class testForm extends ptForm
{
	protected function clean()
	{
				$val = $this->getRawValue('chp1');
				$c   = trim($val);
				$this->setClean('chp1', $c);
	}
}
class FormHelperTest extends PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		
	}

	protected function setUp()
	{
		$this->form = new testForm();
	}
	/*	*/
	public function test_e_selected_if()
	{
		$data = array ( 'contact-civilite' => 'Mr', 'contact-nom' => '', 'contact-prenom' => '', 'email' => '', 'contact-tel' => '', 'objet' => '', 'message' => '');
		$data['option-alerte'] = '1';
		$data['option-newsletter'] = '';
		
		$selected = g_selected_if('option-alerte', '1', $data);
		$this->assertEquals('selected="selected"', $selected);
		
		$data['option-alerte'] = '0';
		$selected = g_selected_if('option-alerte', '0', $data);
		$this->assertEquals('selected="selected"', $selected);
		
		$data['option-alerte'] = 0;
		$selected = g_selected_if('option-alerte', '0', $data);
// 		var_dump($selected);
		$this->assertEquals('selected="selected"', $selected, 
				'e_selected_if() n est pas sensible au type !');
	}
	
		
	public static function tearDownAfterClass()
	{
		// wait('before teardown');
	}


}