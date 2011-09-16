<?php
// phpunit C:\wamp\lib\ow\Template\owTplTest.php
/**
 * 
 *
 */
$string = "Service donateurs de M�decin Sans Fronti�res, 

Suite � une visite sur l'espace donateurs, une demande d'identifiant et de mot de passe a �t� faite.
Les coordonn�es que le demandeur nous a communiqu�es sont les suivantes: 

Demandeur : [[!CIV2!]]
B�timent, appartement, escalier, �tage : [[!V2!]]
R�sidence, lotissement : [[!V3!]]
Voie (avenue, rue, all�e etc...) : [[!V4!]]
Bo�te postale, lieu dit : [[!V5!]]
[Code Postal : [!ZIP!]]]
Ville : [[!VILLE!]]
Code Pays : [[!PAYS!]]

T�l. fixe : [[!TELEPHONE!]]
T�l. mobile : [[!TELEPHONE MOBILE!]]
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