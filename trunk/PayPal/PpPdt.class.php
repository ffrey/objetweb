<?php
/**
 * code from https://www.paypal.com/us/cgi-bin/webscr?cmd=p/pdn/pdt-codesamples-pop-outside#php
 *
 * @uses myUtil for logging
 */
class PpPdt {
	/**
	 * @param string $auth_token   : paypal identification token (identifies the seller)
	 * @param string $pp_server     : domain from which sync is asked
	 * @param string $pp_page          : page from which    "    "     (path from server root / ! BUT on localhost, with a virtual-host, I have to send the whole url : "http://my.domain/page.php)
	 *
	 * @return array               : array('isOk' => bool, 'msg' => string, 'data' => array() )
	 * ! you can retrieve debug msg with myUtil::get_log() immediately after using this method
	 */
	static public function checkPayment($auth_token, $pp_server, $pp_page)
	{
		$res = array('isOK' => false, 'msg' => '', 'data' => null ); // 'data' : array returned from PayPal
		try {
			if (!@isset($_GET['tx']) ) {
				throw new Exception('Pas d\'information de paiement reçue. ');
			}
			$req = 'cmd=_notify-synch'; // the action we want performed : sync (= confirmation)
			$req .= '&tx=' . $_GET['tx']; // the transaction we want confirmed
			$req .= '&at=' . $auth_token;
			// post back to PayPal system to get confirmation + more info...
			$header  = "POST " . $pp_page . " HTTP/1.0\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
			$fp = fsockopen ($pp_server, 80, $errno, $errstr, 30); 
			if (!$fp) {	// synch with PayPal
				myUtil::log(' > Paypal 1 : ' . $errno . ' : ' . $errstr);
				throw new Exception('Votre commande n\'a pu être traitée 
				sur notre site du fait d\'un incident de connection avec
				le serveur PayPal [' . $errno . ' : ' . $errstr . ']');
			} 
			myUtil::log(' > Paypal 1 : socket opened on ' . $pp_server);
			if (!fputs ($fp, $header . $req) ) {
				myUtil::log(' > writing failed : ' . $header . $req);
				throw new Exception('Votre commande n\'a pu être traitée 
				sur notre site du fait d\'un incident de connection avec
				le serveur PayPal'); 
			}
			myUtil::log(' > Paypal 2 : req sent : ' . $header . $req);
			$response = '';
			$headerdone = false; // read the body data
			while (!feof($fp)) {
				$line = fgets ($fp, 1024);
				if (strcmp($line, "\r\n") == 0) { // read the header
					$headerdone = true;
				} else if ($headerdone) { // header has been read. now read the contents
					$response .= $line;
				}
			} // /while
			$lines = explode("\n", $response);  // parse the data
			// @todo : implement checks :
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			$keyarray = array();
			if (strcmp ($lines[0], "SUCCESS") == 0) { // process payment
				$res['msg'] = 'Paiement accepté. '; $res['isOK'] = true;
				for ($i=1; $i<count($lines);$i++){
					if (false === strpos($lines[$i], '=') ) continue;
					list($key,$val) = explode("=", $lines[$i]);
					$keyarray[urldecode($key)] = urldecode($val);
				}
				fclose ($fp);
				myUtil::log(' > Paypal 3 : success => ' . implode(', ', $keyarray) );
			} else if (strcmp ($lines[0], "FAIL") == 0) {
				myUtil::log(' > Paypal 3 : failure');
				throw new Exception('Le paiement a échoué. ');
			} else {
				myUtil::log(' > Paypal 3 : response unreadable !');
				throw new Exception('Le serveur PayPal n\'a pu nous fournir une notification lisible
				 de votre paiement');
			}
			$res['data'] = $keyarray;
			// $res['msg'] .= ' [ ' . myUtil::getLog() . ' ]';
		} catch (Exception $e) { // paiement has failed
			if (isset($response) ) { 
				myUtil::log('GOT RESPONSE : ' . $response); 
			} else {
				myUtil::log('NO SYNC ATTEMPT : missing "tx" GET var');
			}
			$res['msg'] = $e->getMessage();
		}
		return $res;
		/*
		 $firstname = $keyarray['first_name'];
		 $lastname = $keyarray['last_name'];
		 $itemname = $keyarray['item_name'];
		 $amount = $keyarray['payment_gross'];

		 echo ("<p><h3>Thank you for your purchase!</h3></p>");
		 echo ("<b>Payment Details</b><br>\n");
		 echo ("<li>Name: $firstname $lastname</li>\n");
		 echo ("<li>Item: $itemname</li>\n");
		 echo ("<li>Amount: $amount</li>\n");
		 echo ("");
		 */
	} // /checkPayment()

}