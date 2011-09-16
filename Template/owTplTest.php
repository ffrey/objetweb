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
 require_once 'owTpl.class.php';
class owTplTest extends PHPUnit_Framework_TestCase
{
	public function testParse()
	{
		       // we do not set {placeholder3}
        $data = array(
            'placeholder1' => 'var1',
            'placeholder2' => 'var2',
        );
		$result = owTpl::parse('nom=" [[!placeholder1]] ",prenom="[[!placeholder2]]"[[!placeholder3]]', $data);
		// $result = owTpl::parse('[nom="[!placeholder1]"][, prenom="[!placeholder2]"][, [!placeholder3]]', $data);

 
        $this->assertEquals('nom="var1", prenom="var2"', $result);
	}	
}