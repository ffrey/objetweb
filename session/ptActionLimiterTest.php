<?php
// local : phpunit C:\PROJETS_WEB\www\publicis_projets\projet_msf\_SITE\_NEW\test\phpunit\ptActionLimiterTest.php
//         phpunit C:\wamp\lib\ow\session\ptActionLimiterTest.php
session_start();
$root = dirname(__FILE__);
require_once $root.'/../Test/myPhpUnit2.php';
require_once $root.'/ptActionLimiter.class.php';
class ALB extends ptActionLimiter
{
	public static function getConfig()  { return self::$config; }
	public static function getStorage() { return parent::getStorage(); }
	public static function addAction($action, $limit = null)  
	{ return parent::addAction($action, $limit); }
	public static function clearActionsBefore($span, $action)
	{ parent::clearActionsBefore($span, $action); }
	public static function getDefaults() { return self::$defaults; }
	public static function getNS() { return self::$defaults['namespace']; }
}
class ActionLimiterTest extends PHPUnit_Framework_TestCase
{
	public function testInit()
	{
		$action = 'postLogin'; $max = 5; $span = 5;
		$init = array(
			array('action' => $action, 'max' => $max, 'span' => $span),
		);
		ALB::initActionLimits($init);
		$got = ALB::getConfig();
		$ok = array_key_exists($action, $got);
		$this->assertTrue($ok);
		
		$expected = array($action => array('max' => $max, 'span' => $span) );
		$this->assertEquals($expected, $got);
		
		$expected = ALB::getLimits($action);
		$this->assertEquals($expected, array($span, $max) );
		
		$now = time();
		ALB::addAction($action, $now);
		// var_dump($_SESSION[ActionLimiter::NS]);
		$this->assertEquals( 
		array($action => array($now) ),
		$_SESSION[ALB::getNS()],
		sprintf('%s dans $_SESSION doit etre mise a jour !', ALB::getNS())
		 );
		 $got = ALB::getStorage();
		 
		// var_dump('session at END of ' . __FUNCTION__, $_SESSION);
		return $init;
	}
	
	/**
	 * @depends testInit
	 */
	public function testInitWithDefaults($init)
	{
		$action = 'postLogin'; $new_max = 5;
		unset($init[0]['max']);
		$defaults = array('max' => $new_max);
		ALB::initActionLimits($init, $defaults);
		$got = ALB::getConfig();
		$ok = array_key_exists($action, $got);
		$this->assertTrue($ok);
		
		$expected = array($action => array('max' => $new_max, 'span' => $init[0]['span']) );
		$this->assertEquals($expected, $got);
	}
	
	public function testInitError()
	{		
		$action = 'postLogin'; $exp_max = 8;
		$init = array(
			array('action' => $action, 'max' => $exp_max),
		);
		ALB::initActionLimits($init);
		$got = ALB::getConfig();
		list($span, $max) = ALB::getLimits($action);
		$exp = ALB::getDefaults();
		$this->assertEquals($exp_max,     $max);
		$this->assertEquals($exp['span'], $span,
		'une action sans span/max herite des valeurs par defaut' );
		
		$init = array(
			array('actioner' => $action),
		);
		try {
			ALB::initActionLimits($init);
			$this->fail('chaque ligne de $init dt avoir au moins un index "action"');
		} catch(Exception $E) {	cmd('Test ok : Exception got : ' . $E->getMessage() ); }
	}
	
	/**
	 * @depends testInit
	 */
	public function testClearActionsBefore(array $init)
	{
		$init[0]['span'] = $threeQuartersOfAnHour = 45 * 60;
		ALB::initActionLimits($init);
		$action = 'postLogin';
		$t['now']           = $now = time();
		$t['oneHourAgo']    = $now - (60 * 60);
		$t['halfAnHourAgo'] = $now - (30 * 60);
		foreach ($t AS $h) {
			ALB::addAction($action, $h);
		}
		$got = ALB::getStorage();
		$exp = array(
			$action => array($t['oneHourAgo'], $t['halfAnHourAgo'], $t['now'], ),
		);
		$this->assertEquals($exp, $got);
		
		ALB::clearActionsBefore($threeQuartersOfAnHour, $action);
		$got = ALB::getStorage();
		$exp = array(
			$action => array($t['halfAnHourAgo'], $t['now'], ),
		);
		$this->assertEquals($exp, $got);
		/* */
	}
	
	public function testActionLimit()
	{
		$action = 'postLogin'; $max = 5; $span = 5;
		$init = array(
			array('action' => $action, 'max' => $max, 'span' => $span),
		);
		ALB::initActionLimits($init);
		if (0 != ALB::getActionNumber($action) ) {
			die('pb initialisation !');
		}
		ALB::initActionLimits($init);
		foreach (range(1, $max+1) AS $i) {
			$got = ALB::isActionOverLimit($action);
			if ($i > $max) {
				cmd(sprintf('max %s reached for action %s', $max, $action) );
				$this->assertTrue($got);
				break;
			} 
			// cmd($action . ' : ' . $i);
			$this->assertFalse($got);
		}
		$got = ALB::getStorage();
		$expNbActionsWithinSpan = $max;
		$gotNbActionsWithinSpan = count($got[$action]);
		$this->assertEquals($gotNbActionsWithinSpan, $expNbActionsWithinSpan);
//		$got = ActionLimiter::isActionOverLimit($action);
//		$this->assertTrue($got, sprintf('action %s can be done %s times in %s secondes', $action, $i, $span) );
		
		$got = ALB::isActionOverLimit($action);
		$this->assertTrue($got, 
		sprintf('action %s has reached max %s times in %s secondes', $action, $i, $span) );
		cmd('on attend ' . $span . ' secondes');
		sleep($span+1);
		$got = ALB::isActionOverLimit($action);
		$actions = ALB::getStorage();
		// var_dump('after wait', $actions);
		$this->assertFalse($got, 
		sprintf('after %s secondes, action can be done again', $span) );
		/* */
	}
	 
}