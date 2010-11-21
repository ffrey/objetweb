<?PHP
/**
 * fabrique pour mailers
 */
if (!class_exists('myUtil') ) require_once 'C:\EasyPHP_3_0\php\perso_php/myUtil.class.php';
class factorMailer
{
  static $A_acceptedEnv = array('dev', 'prod', 'new', 'test');
  // static $acceptedCallers = array('mySender', 'testMySender');
  static $mailers = array('htmlMimeMail5', 'Swift');
  static $fakemails = array();
  static $mailDir = '';

  private function __construct() { }
  /**
	 * @param string $mode        : see self::$A_acceptedEnv for accepted values
	 * @param string $bal_dir  : abs path to directory to write fake mails to IF env = 'dev' !
	 */
  public static function get($mode = 'dev', $bal_dir = '', $mailer = 'htmlMimeMail5')
  {
    self::$mailDir = $bal_dir;
    try {
      myUtil::_checkIsIn($mailer, self::$mailers);
      $env = self::makeFormatted($mode);
      myUtil::_checkIsIn($env, self::$A_acceptedEnv);
      switch ($env) {
        case 'prod':
        if ('htmlMimeMail5' == $mailer) return new $mailer();
        if ('Swift'         == $mailer) return new $mailer(new Swift_Connection_NativeMail() );
        break;
        case 'new':
        case 'dev':
        case 'test':
        $mock = 'mock' . ucfirst($mailer);
        if ('htmlMimeMail5' == $mailer) return new $mock();
        if ('Swift'         == $mailer) return new $mock(new Swift_Connection_NativeMail() );
        return new $mock($bal_dir); // @see below !
        break;
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public static function fakeMail(array $to, $from, $subject, $myOutput)
  {
    $ret = false;
    $Now = new DateTime();
    $fileName      = $Now->format("d_m_Y_H_i_s") . '.inc';
    $mailContent = '';
    foreach ($to AS $mail) {
      $mailContent .= "to $mail\n";
    }
    $mailContent   .= "Subject $subject\nFrom $from\n$myOutput";
    // open file
    $fileName = substr($subject, 0, 3) . $fileName;
    if (!$fp = fopen( self::$mailDir . DIRECTORY_SEPARATOR . $fileName, 'w')) throw new Exception($fileName . ' impossible à ouvrir');
    fwrite ($fp, $mailContent);
    array_unshift(self::$fakemails, $fileName);
    $ret = true;
    // write params

    return $ret;
  }
/*** for testing purpose only ! ***/
  public function _getLastCreatedFile()
  {
    return self::$fakemails[0];
  }
  public function _cleanUp()
  {
    if (!sizeof(self::$fakemails) ) return;
    foreach (self::$fakemails AS $filename) {
      $res = unlink(self::$mailDir . DIRECTORY_SEPARATOR . $filename);
      if (!$res) myUtil::log('Failed deletion of ' . $filename);
    }
    self::$fakemails = array();
  }
  /*** protected ***/
  protected static function checkIsAcceptedEnv($env)
  {
    $ret = false;
    foreach (self::$A_acceptedEnv AS $a) {
      if (stristr($env, $a)) { $ret = true; break; }
    }
    if (!$ret) throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' : environnement [' . $env . '] inconnu !');
  }
  protected static function makeFormatted($env)
  {
    $env = strtolower($env);

    return trim($env);
  }
} // /Mailer
