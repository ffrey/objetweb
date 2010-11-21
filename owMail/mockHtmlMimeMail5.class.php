<?php

/**
 * mock class servant à simuler l'envoi de mail
 * les méthodes reprennent + ou - l'API de htmlMimeMail
 * @uses myUtil
 * @throws Exception
 */
if (!class_exists('htmlMimeMail5') ) require_once dirname(__FILE__) . '\vendor\htmlMimeMail5\htmlMimeMail5.php';
class mockHtmlMimeMail5 extends htmlMimeMail5
{
  protected $subject       = 'default Subject';
  protected $from          = 'default From';
  protected $myOutput      = 'default Output';
  protected $mail_dir   = ''; // __construct

  /*** OVERRIDDEN ***/
  /**
	 * @param string $env             : mode => 'dev'[mails are written to files] / 'prod'[mails are being sent]
	 * @param string $bal_dir      : dir where to store fake mails ('dev' mode only)
	 */
  public function __construct($bal_dir = 'mails')
  {
    // if (!is_dir($dir)) throw new Exception(__CLASS__ . '::' . __FUNCTION__ . ' : ' . $bal_dir . ' is not valid dir !');
    $this->mail_dir = $bal_dir;
    myUtil::log('creation with dir : ' . $bal_dir, __CLASS__);
    // myUtil::sf_log('creation with dir : ' . $bal_dir);
  }
  public function setSubject($string)
  {
    $this->subject = $string;
  }
  public function setFrom($string)
  {
    $this->from = $string;
  }
  public function setHTML($html, $images_dir = null)
  {
    $this->myOutput = $html;
  }

  public function send($recipients, $type = 'mail')
  {
    // $result = $this->fakeMail($recipients, $this->from, $this->subject, $this->myOutput);
    return $result = factorMailer::fakeMail($recipients, $this->from, $this->subject, $this->myOutput);
    // return $result;
  }
  public function getMailDir()
  {
    return $this->mail_dir;
  }
  public function getLog()
  {
    return myUtil::getLog(__CLASS__);
  }
  /*** *** protected **
  protected function fakeMail(array $to, $from, $subject, $myOutput)
  {
    $ret = false;
    $Now = new DateTime();
    $fileName      = $Now->format("d_m_Y_H_i") . '.inc';
    $mailContent = '';
    foreach ($to AS $mail) {
      $mailContent .= "to $mail\n";
    }
    $mailContent   .= "Subject $subject\nFrom $from\n$myOutput";
    // open file
    if (!$fp = fopen( $this->getMailDir() . DIRECTORY_SEPARATOR . $fileName, 'w')) throw new Exception($fileName . ' impossible à ouvrir');
    fwrite ($fp, $mailContent);
    $ret = true;
    // write params

    return $ret;
  }*/
  /***/

  /*** MAIN ***/
  /**
	 * TODO : send to webmaster when error 404 caught !
	 *
	 */
  public function sendError()
  {

  }

}