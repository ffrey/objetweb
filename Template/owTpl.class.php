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
    protected static 
      $last_unknown_vars = array(),
      $last_unfilled_vars = 0,
      $last_missing_vars  = 0;
    
    static public function getUnknownVars()
    {
        $ret = self::$last_unknown_vars;
        self::$last_unknown_vars = array();
        return $ret;
    }
    
    static public function getUnfilledVars()
    {
        $ret = self::$last_unfilled_vars;
        self::$last_unfilled_vars = 0;
        return $ret;
    }
    
    static public function getMissingVars()
    {
        $ret = self::$last_missing_vars;
        self::$last_missing_vars = 0;
        return $ret;
    }
    
		/**
	 * @todo : cette methode devraient renvoyer une Exception
	 *  si une variable du template n'est pas remplie (et non
	 *  l'inverse comme actuellement !!!)
	 * @param string $str : nom du template dans web/ext/mail_templates
	 * @param array $data
	 */
	static public function parse($str, array $data)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;

		$ret = $str;
		$unknown_vars = array();
        $iEmpty       = 0;
        $missing      = 0;
		foreach ($data AS $varname => $v) {
            if ('' == $v) { $iEmpty++; continue; }
			if ($db) {
				printf('val : %s => %s doit remplacer %s', $varname, $v, '[[!'.$varname.'!]]'."\n");
			}
            $found = 0;
            $ret = preg_replace('#\[([^[]*)\[!'.$varname.'!]([^]]*)\]#', '${1}'.$v.'${2}', $ret, -1, $found);
			if ($db) {
				var_dump($ret."\n");
			}
			if (0 == $found) {
				$unknown_vars[] = $varname;
			}
/**/		}
		// on enleve tous les place-holders vides
		$ret = preg_replace('#\[[^[]*\[![^[]*[^[]*\]#', '', $ret, -1, $missing);
        self::$last_unknown_vars  = $unknown_vars;
        self::$last_unfilled_vars = $missing;
        self::$last_missing_vars  = $missing - $iEmpty;

		return $ret;
	}
}