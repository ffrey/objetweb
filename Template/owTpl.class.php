<?php
// phpunit C:\wamp\lib\ow\Template\owTplTest.php
/**
 * 
 *
 */
 $string = "Service donateurs de Médecin Sans Frontières, 

Suite à une visite sur l'espace donateurs, une demande d'identifiant et de mot de passe a été faite.
Les coordonnées que le demandeur nous a communiquées sont les suivantes: 

Demandeur : [[!CIV2!]]
Bâtiment, appartement, escalier, étage : [[!V2!]]
Résidence, lotissement : [[!V3!]]
Voie (avenue, rue, allée etc...) : [[!V4!]]
Boîte postale, lieu dit : [[!V5!]]
[Code Postal : [!ZIP!]]]
Ville : [[!VILLE!]]
Code Pays : [[!PAYS!]]

Tél. fixe : [[!TELEPHONE!]]
Tél. mobile : [[!TELEPHONE MOBILE!]]
Email : [[!EMAIL!]]

Message : [[!MESSAGE!]]

Cordialement,
Service Webmaster.
";
class owTpl
{
		/**
	 * @todo : cette methode devraient renvoyer une Exception
	 *  si une variable du template n'est pas remplie (et non
	 *  l'inverse comme actuellement !!!)
	 * @param string $str : nom du template dans web/ext/mail_templates
	 * @param array $data
	 */
	static public function parse($str, array $data)
	{
		$db = false;
		$ret = '';
		$origin = $msg = __CLASS__.'::'.__FUNCTION__;
		$ret = $str;
		$unknown_vars = array();
		foreach ($data AS $varname => $v) {
            if ('' == $v) { continue; }
			if ($db) {
				printf('val : %s => %s doit remplacer %s', $varname, $v, '[[!'.$varname.'!]]'."\n");
			}
			// $ret = str_replace('[[!'.$varname.'!]]', $v, $ret, $count);
            $ret = preg_replace('#\[([^[]*)\[!'.$varname.'!]([^]]*)\]#', '${1}'.$v.'${2}', $ret);
			if ($db) {
				// 				var_dump($ret."\n");
			}
            /*
			if (0 == $count) {
				$unknown_vars[] = $varname;
				// throw new Exception ($msg.' : unknown tpl var : ' . $varname);
			}
*/		}
		// on enleve tous les place-holders vides
		$ret = preg_replace('#\[[^[]*\[![^[]*[^[]*\]#', '', $ret, -1, $missing);
		$msg = '';
		/**
		if ($missing) {
			$msg .= sprintf('Tpl mail %s has %s unfilled vars', $str, $missing);
		}
		if (count($unknown_vars) ) {
			$msg .= sprintf('Tpl mail %s got unused vars : %s.', $str, implode(', ', $unknown_vars) );
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
		*/
		return $ret;
	}
}