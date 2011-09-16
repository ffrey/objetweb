<?php
/**
 * @author FFreyssenge
 * @uses myMysql (pour value ras !),
 * 		 Contexte (get('countries'), etc.)
 * 	   ! $mails array : liste des emails <= recupere via Contexte (les noms des cles
 * 		 sont notamment tres importantes car utilisees a l interieur de la classe !)
 */
class Mailer extends Zend_Mail
{
	/**
	 * @todo :: $this->mails devrait etre initialise depuis mails.php !
	 * (pour centralisation !!!)
	 * @todo :: 2011/06/01 :: certaines methodes devraient etre static : @see self::sendAnomaly()/sendTechError()
	 * @todo ::     "      :: harmoniser les methodes. Ex : sendFromBatch() s emploi sur un objet / sendTechError s'emploie en methode statique...
	 */
	protected
	$_charset = 'iso-8859-1', // <= overridden on construct
	$tpl_ref = '',
	// @deprecated : pour l instant encore utilise par self::getLabel()
	$mails = array(
		'donateurs@paris.msf.org' => 'Médecins Sans Frontières',
	),
	$is_dev_added = false;

	/* deprecated : @see self::readTpl()
	 $mail_templates = array(
	 'WCHGCP' => 'Modification de coordonnées',
	 'WCHGPW' => 'Modification de mot de passe',
	 'WNOPASSWORD' => 'Envoi de l’identifiant et du mot de passe',
	 'WRECA' => 'Envoi de duplicata de reçu annuel',
	 'WRECU' => 'envoi de duplicata de don unique',
	 ),
	 $has_used_template = false;*/
	public
	$mail_templates_dir = '',
	$subjects = array(
		'start_test'  => '[TEST]',
		'start_error' => '[MSF][error]',
	);

	public function __construct($charset = 'iso-8859-1')
	{
		parent::__construct($charset);
		$this->mail_templates_dir = DS.'web'.DS.'ext'.DS.'mail_templates'.DS;
	}

	public function addTo($email, $name='')
	{
		if ($this->isDevEnvt() ) { // en dev/local, tous les mails sont envoyes a publicis !
			if (!$this->is_dev_added) { // ! n'ajouter les mails pub kune fois !
				$mails = $this->getMails();
				$email = $mails['groups']['DevPublicis'];
				$this->is_dev_added = true;
				parent::addTo($email, $name);
			}
		} else {
			parent::addTo($email, $name);
		}
	}

	public function setFrom($email, $name = null)
	{
		if (is_null($name) ) {
			$name = $this->getLabel($email);
		}
		parent::setFrom($email, $name);
	}

	/**
	 * _traitementEmailContact.php : besoin de utf8_decode() avant d'appeler cette fonction ?
	 * @param string $subject
	 */
	public function setSubject($subject)
	{
		if ($this->isDevEnvt() ) {
			$subject = '[TEST] ' . $subject;
		}
		// @todo : bug pb d'affichage des sujets avec accents ! (sous Gmail ou Lotus / pas sous yahoo !???)
		// $subject = myString::strToUtf8($subject);
		parent::setSubject($subject);
	}

	public function setBodyTextTpl($ref, array $data, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		foreach ($data AS $k => $v) {
			if (!is_string($v) ) { continue; }
			$data[$k] = stripslashes($v);
		}
		$tpl = $this->fillTpl($ref, $data);
		if ($db) {
			var_dump($origin, 'texte rempli', $tpl);
			exit;
		}
		parent::setBodyText($tpl, $charset, $encoding );
		$this->tpl_ref = $ref;
	}

	public function buildAdresseEntreprise(array $d)
	{
		$ret = '';
		foreach (array('Raison sociale' => 'rs',
		'Siret' => 'siret', 'Code naf' => 'code_naf') AS $k => $v) {
		// if (array_key_exists($v, $d) AND !empty($d[$v]) ) {
		if (array_key_exists($v, $d) AND !$this->isEmpty($d[$v]) ) {
			$ret .= $k . ' : ' . $d[$v] . "\r\n";
		}
		}
		return $ret;
	}

	protected function trimEmpty(array $t) 
	{
		foreach ($t AS $k => $v) {
			if (!is_string($v) ) { continue; }
			if ($this->isEmpty($v) ) {
				unset($t[$k]);
			}
		}
		return $t;
	}
	
	/**
	 * ! ici, empty = empty + 'R/S' !!!
	 */
	protected function isEmpty($val)
	{
		$val = trim($val);
		$val = strtoupper($val);
		$ret = (empty($val) OR myMysql::$ras == $val);
		
		return $ret;
	}
	
	/**
	 * @todo : cette methode devraient renvoyer une Exception
	 *  si une variable du template n'est pas remplie (et non
	 *  l'inverse comme actuellement !!!)
	 * @param string $ref : nom du template dans web/ext/mail_templates
	 * @param array $data
	 */
	public function fillTpl($ref, array $data)
	{
		$db = false;
		$ret = '';
		$origin = $msg = __CLASS__.'::'.__FUNCTION__;
		$ret = $this->readTpl($ref);
		$unknown_vars = array();
		foreach ($data AS $varname => $v) {
			if (myMysql::$ras == $v) { continue; }
			if ($db) {
				print 'val : ' . $v . "<br/>\n";
			}
			if (is_string($v) ) {
				//

				$v = html_entity_decode($v, ENT_NOQUOTES, 'UTF-8');
				$v = stripslashes($v);
				if ($db) {
					print '=> '.$v . " => encoding : " . mb_detect_encoding($v) . "<br/>\n";
				}
			}
			if ('UTF-8' == mb_detect_encoding($v) ) {
				if ($db) { print 'UTF-8 : ' . $v . "<br/>\n"; }
				// 2010 10 14 : accents non affiches dans mail !
				// DEV : je décommente : en PROD aussi ???
				// 2010 10 29 : :-O je recommente ???!!!
				// 2010 11 18 : je re-décommente : comportement instable et imprévisible !!!
				if ($this->isDevEnvt() ) {
					//
					$v = mb_convert_encoding($v,"ISO-8859-1","UTF-8");
				} else {
					//
					$v = mb_convert_encoding($v,"ISO-8859-1","UTF-8");
				}
				//
				if ($db) { print '=> '.$v . " => NEW encoding : " . mb_detect_encoding($v) . "<br/>\n"; }
			}
			$ret = str_replace('[[!'.$varname.'!]]', $v, $ret, $count);
			if (0 == $count) {
				$unknown_vars[] = $varname;
				// throw new Exception ($msg.' : unknown tpl var : ' . $varname);
			}
		}
		// on enleve tous les place-holders vides
		$ret = preg_replace('#\[\[![^[]*\]#', '', $ret, -1, $missing);
		$msg = '';
		if ($missing) {
			$msg .= sprintf('Tpl mail %s has %s unfilled vars', $ref, $missing);
		}
		if (count($unknown_vars) ) {
			$msg .= sprintf('Tpl mail %s got unused vars : %s.', $ref, implode(', ', $unknown_vars) );
		}
		if (!empty($msg) ) {
			if ($this->isDevEnvt() ) {
				$this->sendAnomaly($msg);
			}
		}
		if ($db) {
			var_dump($origin, $ret);
			exit;
		}
		return $ret;
	}

	public function readTpl($ref)
	{
		$msg = __CLASS__.'::'.__FUNCTION__;
		// deprecated : no use storing the names internally ? check on real files sufficient & better ?
		//		if (!array_key_exists($ref, $this->mail_templates) ) {
		//			throw new Exception ($msg.' : unknown mail template : ' . $ref);
		//		}
		$path = ROOT.$this->mail_templates_dir.$ref.'.txt';
		if (!file_exists($path) ) {
			throw new Exception ($msg.' : missing file : '.$path);
		}
		return file_get_contents($path);
	}

	/**
	 *
	 * @param string $txt : texte du tpl
	 * @see self::getVars()
	 */
	public function checkVars($txt, $data)
	{
		$msg = __CLASS__.'::'.__FUNCTION__;
		// $nb = preg_match_all('/\[\[!([^\!]*)/', $txt, $matches);
		list($nb, $tpl_vars) = $this->getVars($txt);
		if ($diff = array_diff($tpl_vars, array_keys($data) ) ) {
			throw new Exception ($msg.' : missing vars '.implode(', ', $diff) );
		}
		return array('nb' => $nb, 'vars' => $tpl_vars);
	}

	/**
	 * @param array $data : ['nb' => <nb of vars>]['vars' => <tpl var names>']
	 */
	public function getVars($txt)
	{
		$ret = preg_match_all('/\[\[!([^\!]*)/', $txt, $matches);
		return array($ret, $matches[1]);
	}

	public function showVars($txt)
	{
		$origin = __CLASS__.'::'.__FUNCTION__;
		list($nb, $vars) = $this->getVars($txt);
		print '<h3>'.$origin.'</h3>';
		foreach ($vars AS $varname) {
			print "'$varname' => '',\n";
		}
	}


	/**
	 * @deprecated $User should not be a parameter => @see send2() !
	 * @param unknown_type $transport
	 */
	public function send($transport = null, newUser $User = null)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		if ($db) {
			var_dump($origin, 'ENVT DEV ?', $this->isDevEnvt() );
		}
		// @todo :: 2011 06 09 :: bug => les mails avec bcc provoquent un echec d envoi !
		// "SMTP server response: 554 Relay rejected for policy reasons"
		// INC856696
		// if (!CONTEXTE::isDevEnvt() OR Contexte::isRecetteEnvt() ) { // si PROD ou RECETTE
		$this->addBccToED();
		// }
		/** @todo :: 2011 06 30 :: ATTENTION, desactivation temporaire => @see INC890285 **/
		parent::send($transport);
		if ($this->hasUsedTemplate() ) {
			if ($db) { print 'We have used tpl !'; }
			// save into db : mails_envoyes
			$dest_mails = implode(', ', $this->getRecipients() );
			$data = array(
			'dest_mails' => $dest_mails, 
			'ref_msf' => $this->tpl_ref
			);
			if (null != $User AND $User->isAuthenticated() ) {
				$data['nid'] = $User->getInfo('NID');
				if ($db) {
					var_dump($origin.' WE ARE CONNECTED !', $User->getInfosDonateur());
					exit;
				}
			}
			MailEnvoyeWeb::save($data, $User);
		}
		$this->tpl_ref = '';
		return $this;
	} // /send()
	
	/**
	 * 
	 * @param unknown_type $transport
	 * @param int $nid
	 */
	public function send2($transport = null, $nid = null)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		if ($db) {
			var_dump($origin, 'ENVT DEV ?', $this->isDevEnvt() );
		}
		// @todo :: 2011 06 09 :: bug => les mails avec bcc provoquent un echec d envoi !
		// "SMTP server response: 554 Relay rejected for policy reasons"
		// INC856696
		// if (!CONTEXTE::isDevEnvt() OR Contexte::isRecetteEnvt() ) { // si PROD ou RECETTE
		$this->addBccToED();
		// }
		/** @todo :: 2011 06 30 :: ATTENTION, desactivation temporaire => @see INC890285 **/
		parent::send($transport);
		if ($this->hasUsedTemplate() ) {
			if ($db) { print 'We have used tpl !'; }
			// save into db : mails_envoyes
			$dest_mails = implode(', ', $this->getRecipients() );
			$data = array(
			'dest_mails' => $dest_mails, 
			'ref_msf' => $this->tpl_ref
			);
			if (null != $nid) {
				$data['nid'] = $nid;
				if ($db) {
					var_dump($origin.' We have a nid !', $nid);
					exit;
				}
			}
			MailEnvoyeWeb::save($data);
		}
		$this->tpl_ref = '';
		return $this;
	} // /send()
	

	/**
	 * envoyer le mail de confirmation des dons passes par le formulaire PUBLIC (hors ED !)
	 *
	 * @param array $d
	 * @param mixed $User : newUser / int (nid)
	 * @param bool $isAlreadyDonateur
	 * @param string $paiement : CB / PayPal
	 */
	public function sendPaiement(array $d, $User, $isAlreadyDonateur, $paiement = 'CB')
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$log = true;
		if ($log) {
			$this->log($origin);
			$this->log(print_r($d, true) );
		}
		$mails = $this->getMails();
		$mail_ed = $mails['service donateur']['Medecins sans frontieres'];

		# 1/ preparation des variables du mail
		$data_email = $this->_prepSendPaiement($d);

		# 2/ envoi du mail
		// $typePaiement = ('PayPal' != $paiement)? 'DON' : 'PPL';
		// mail du 2011 09 12 13h09 : DDU demande ke mails ppl == mails cb !
		$typePaiement = 'DON';
		if ($isAlreadyDonateur) { // ! has not always a nid <= may have clicked "J'ai déjà fait un don à MSF
			//... mais je ne connais pas mon identifiant donateur" over step 2 of don
			if ($d['type.don'] == 'Reg') {
				$this->setBodyTextTpl('WPAANCCB', $data_email);
				$this->setSubject('Merci pour votre don régulier');
			}else { // WDONANC ou WPPLANC
				$this->setBodyTextTpl('W'.$typePaiement.'ANC', $data_email);
				$this->setSubject('Merci pour votre don');
			}
		} else {
			if ($d['type.don'] == 'Reg') {
				$this->setBodyTextTpl('WPANVXCB', $data_email);
				$this->setSubject('Merci pour votre don régulier');
			} else { // WDONNVX ou WPPLNVX
				$this->setBodyTextTpl('W'.$typePaiement.'NVX', $data_email);
				$this->setSubject('Merci pour votre don');
			}
		}
		$this->addTo($d['email']);
		// utile ajout pour surveiller l encodage des mails !-o
		// $this->addBcc($mails['Dev2']);
		$this->setFrom($mail_ed);
		if ($User instanceof $User) {
			$this->send(null, $User);
		} else {
			$nid = $User;
			$this->send2(null, $nid);
		}
	} // /sendPaiement()

	public function sendPaiementEchec(array $d, $User)
	{
		myUtil::_checkNeededSet(array(
		'email'
		), $d);
		$data['DATE']   = date('d/m/y');
		$data['HH :MM'] = date('H:i');
		$this->addTo($d['email']);
		$this->setSubject('Echec de votre don');
		$this->setFrom('donateurs@paris.msf.org');
		$this->setBodyTextTpl('WINTER', $data);
		$this->send(null, $User);
	} // /sendPaiementEchec()

	/**
	 * ! cette methode utilise parent::send => les traitements specifiques ne sont pas appliques,
	 *  notamment l envoi de bcc a msf, etc.
	 */
	public function sendFromBatch($msg, $subject, $mailTo, $filename = null )
	{
		$this->setFrom('msf@publicis.com');
		if (!is_null($filename) ) {
			if (!file_exists($filename) ) {
				$msg .= "\n".'## echec attachement du fichier ' . $filename . '##';
			} else {
				$str = file_get_contents($filename);
				parent::createAttachment($str,
				'text/plain', 
				Zend_Mime::DISPOSITION_ATTACHMENT,
				Zend_Mime::ENCODING_BASE64,
				basename($filename)
				);
			}
		}
		parent::addTo($mailTo);
		$this->setSubject($subject);
		$this->setBodyHtml($msg);
		parent::send();
	}

	public function sendMailBilan($mails,$erreurs,$bilan,$subject)
	{
		$db = false;

		$corps_mail = 'BILAN'. '<br/>'."\n\r";
		foreach ($bilan AS $ligne) {
			$corps_mail .= $ligne . '<br/>'."\n\r";
		}

		$corps_mail .=  '----------------------<br/>'."\n\r";
		if (count($erreurs) ) {
			$corps_mail .= 'ERREURS'. '<br/>'."\n\r";
			foreach ($erreurs AS $ligne) {
				$corps_mail .= $ligne . '<br/>'."\n\r";
			}
		}
		if ($db) {
			var_dump('Mail', $corps_mail);
			echo "<hr>";
			exit;
		}

		$Mailer = new Mailer();
		$this->sendFromBatch($corps_mail, $subject, $mails );
	}

	public function attachPdf($str, $filename)
	{
		parent::createAttachment($str,
			'application/pdf', 
		Zend_Mime::DISPOSITION_INLINE,
		Zend_Mime::ENCODING_BASE64,
		$filename
		);
	}

	/*** STATIC ***/

	static public function sendAnomaly($msg)
	{
		$M = new Mailer();
		$mails = $M->getMails();
		$M->addTo($mails['groups']['DevPublicis']);
		$M->setSubject($M->subjects['start_error'].' Anomaly ');
		$m = $mails['service donateur']['Medecins sans frontieres'];
		$M->setFrom($m);
		$M->setBodyText($msg);
		$M->send();
	}

	static public function sendTechError($msg)
	{
		$msg = html_entity_decode($msg);
		$M = new Mailer();
		$mails = $M->getMails();
		$M->addTo($mails['Dev2']);
		$M->setSubject($M->subjects['start_error'].' Technical error');
		$m = $mails['service donateur']['Medecins sans frontieres'];
		$M->setFrom($m);
		$M->setBodyText($msg);
		$M->send();
	}


	/*** PROTECTED ***/

	/**
	 * preparation des variables a remplier du template de mail de confirmation (@see self::sendPaiement() )
	 *
	 * @param array $data
	 */
	protected function _prepSendPaiement(array $data)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$data_email = array();
		$countries = Contexte::get('countries');
		$data = $this->trimEmpty($data);
		
		$login = '';
		if (@isset($data['login'])) {
			$login = $data['login'];
		}
		$civ1 = $data['civ'].' '.$data['prenom'].' '.$data['nom'];
		if (owArray::g_if('rs', $data) AND $data['rs'] != '') {
			$civ1 = $data['rs'];
		}
		$civ2 = $data['civ'].' '.$data['prenom'].' '.$data['nom'];
		$adresse_entreprise = $this->buildAdresseEntreprise($data);
		$data_email = array(
					'CIV1' => $civ1,
					'CIV2' => $civ2,
					'MNTCHIFFRE'	=> owArray::g_if('montant', $data),
					'mntchiffre'	=> owArray::g_if('montant', $data),
					'V2' => owArray::g_if('adr_v2', $data),
					'V3' => owArray::g_if('adr_v3', $data),
					'V4' => owArray::g_if('adr_v4', $data),
					'V5' => owArray::g_if('adr_v5', $data),
					'V6' => owArray::g_if('zip', $data).' '.owArray::g_if('localite', $data),
					'PAYS' => $countries[$data['pays']],
					'TELEPHONE' => owArray::g_if('tel', $data),
					'TELEPHONE_MOBILE' => owArray::g_if('tel_mobile', $data),	
					'TEL_MOBILE' => owArray::g_if('tel_mobile', $data),			
					'EMAIL' => owArray::g_if('email', $data),
					'CYCLELIB2' => 'tous les mois',	
					'ENTREPRISE' => $adresse_entreprise,
		);
		$data_email['CHRO'] = $data_email['IDENTIFIANT'] = '';
		if ($login != '') {
			$data_email['CHRO']        = 'PS : nous vous rappelons que votre num&eacute;ro de donateur est le suivant : ' . $login;
			$data_email['IDENTIFIANT'] = 'Identifiant donateur : '.$login;
		}
		if ($db) {
			var_dump('INFOS MAIL', $data_email);
			exit;
		}
		return $data_email;
	} // /prepSendPaiement()

	protected function getMails()
	{
		$mails = Contexte::get('mails');
		return $mails;
	}

	protected function isDevEnvt()
	{
		return Contexte::isDevEnvt();
	}

	protected function addBccToED()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$this->log($origin);
		if (!$this->isSentToServiceDonateur() AND !$this->isTechMail() ) {
			/**
			 * 2011 06 01 : tous les mails envoyes depuis le site sont envoyes en "blind copy" a MSF !
			 * ! MAIS : pas les mails d alerte technique (sendAnomaly/sendTechError) => pour cette, raison,
			 * l ajout ne se fait pas dans self::addTo()...
			 */
			$mails = $this->getMails();
			$k = 'msf bcc';
			if (array_key_exists($k, $mails) ) {
				parent::addBcc($mails[$k]);
				$this->log('adding ' . print_r($mails[$k], true) );
			} else {
				$this->log('bcc mail not found : ' . $k);
				// @todo :: 2011 06 01 :: envoyer alerte MAIS attention au bouclage infini sur self::send() !!!
			}
		} else {
			$this->log('no bcc to add !');
		}
	}

	protected function hasUsedTemplate()
	{
		return ('' != $this->tpl_ref)? true : false;
	}

	/**
	 * wether mail of service donateur is present AS MAIN recipient
	 * (! it does NOT check wether service donateur is cc or bcc !)
	 * @see $mails['service donateur']['Medecins sans frontieres']
	 *
	 */
	protected function isSentToServiceDonateur()
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$is_sent_to_sd = false;
		$mails = $this->getMails();
		$mail_ed = $mails['service donateur']['Medecins sans frontieres'];
		if ($db) {
			var_dump($origin, $this->_to);
		}
		if (in_array($mail_ed, $this->_to) ) {
			$is_sent_to_sd = true;
		}
		return $is_sent_to_sd;
	}

	protected function isTechMail()
	{
		$ret = false;
		$sub = '[MSF][error]';
		if (false !== strpos($this->_subject, $sub) ) {
			$ret = true;
		}
		return $ret;
	}

	protected function getLabel($email)
	{
		$ret = null;
		$email = trim(strtolower($email) );
		if (array_key_exists($email, $this->mails) ) {
			$ret = $this->mails[$email];
		}
		return $ret;
	}

	protected function log($msg)
	{
		$prefix = date('Ymd').'_log_';
		ptLog::setPrefix($prefix.__CLASS__);
		ptLog::log($msg);
	}
	
}