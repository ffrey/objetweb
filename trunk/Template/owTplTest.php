<?php
// phpunit C:\wamp\lib\ow\Template\owTplTest.php
// phpunit C:\EasyPHP_3_0\php\ow\Template\owTplTest.php
/**
 * 
 *
 */
require_once dirname(__FILE__).'/../helper/owConsoleHelper.php';
require_once 'owTpl.class.php';
class owTplTest extends PHPUnit_Framework_TestCase
{
    public function testRegex()
    {
        $replace = $v = '3val1';
        $tests = array(
            array('#\[([^[]*)\[!var1]\]#',
            '${1}'.$v,
            '[name=[!var1]]',
            'name='.$v),
            array('#\[([^[]*)\[!var1]([^]]*)\]#',
            '${1}'.$v.'${2}',
            '[name="[!var1]"]',
            'name="'.$v.'"',)
        ,);
        foreach ($tests AS $t) {
            $expect = $t[3];
            $got = preg_replace($t[0], $t[1], $t[2]);
            //             cmd(sprintf('%s => %s', $t[1], $got) );
            $this->assertEquals($expect, $got);
        }
    }

	public function testParse()
	{
		       // we do not set {placeholder3}
        $data = array(
            'placeholder1' => 'var1',
            'placeholder2' => 'var2',
        );
        $tests = array(
            array('remplacement de variables',
            'nom="[[!placeholder1!]]", prenom="[[!placeholder2!]]"[[!placeholder3!]]',
            'nom="var1", prenom="var2"'),
            array('texte conditionnel',
            '[name="[!placeholder1!]"][, surname="[!placeholder2!]"][, nickname="[!placeholder3!]"]',
            'name="var1", surname="var2"',)
        ,);
        foreach ($tests AS $t) {
            step($t[0]);
            // cmd($t[1]."\n");
            $got = owTpl::parse($t[1], $data);
            // cmd('got : ' . $got . "\n");
            $this->assertEquals($t[2], $got);
        }
		// $got = owTpl::parse('[nom="[!placeholder1]"][, prenom="[!placeholder2]"][, [!placeholder3]]', $data);
	}	
   
   public function testParseSeveralLines()
   {
        $data = array(
            'CIV2'    => 'Monsieur',
            'V3'      => 'Les Lilas',
            'VILLE'   => 'Orl�ans',
        );
        $tests = array(
            array(
'[Demandeur : [!CIV2!]
][B�timent, appartement, escalier, �tage : [!V2!]
][R�sidence, lotissement : [!V3!]
][Ville : [!VILLE!]]',
'Demandeur : Monsieur
R�sidence, lotissement : Les Lilas
Ville : Orl�ans',),
        );

        foreach ($tests AS $t) {
            $expected = $t[1];
            $got = owTpl::parse($t[0], $data);
            // cmd('got : ' . $got);
            $this->assertEquals($expected, $got);
        }
   }
   
   public function testEmptyVariables()
   {
        $data = array(
            'CIV2'    => 'Madame',
            'SURNAME' => '',
            'NAME'    => 'Michaud',
            'COMMENT' => ' ',
        );
        $text = '
[Ch�re [!CIV2!] ][[!SURNAME!] ][[!NAME!]],
[Votre commentaire est [!COMMENT!].]';
        $expected = '
Ch�re Madame Michaud,
Votre commentaire est  .';
        $got = owTpl::parse($text, $data);
        $this->assertEquals($expected, $got);
   }
   /**/
   
   public function testFromFile()
   {
        $db = false;
        
        $data = array(
            'CIV2' => 'Monsieur',
            'V2'   => '3�me �tage',
            // 'V3'   => 'B�t. Les Lilas',
            'V44' => 'variable qui n\'existe pas dans le template !',
            'V4'   => 'rue de la libert�',
            'V5'   => '',
            'VILLE' => 'Orl�ans',
            'PAYS'  => 'France',
            'TELEPHONE' => '',
            'TELEPHONE MOBILE' => '06 12 78 34 43',
            'MESSAGE' => 'Pour la Somalie',//
        );
        // V3,  ZIP, EMAIL : absents | V5, TELEPHONE : vides
        $expected_unfilled_vars = 5;
        $expected_missing_vars  = 3;
        $dir = dirname(__FILE__);
        $file          = $dir.'/data/template.txt';
        $file_expected = $dir.'/data/template_expected.txt';
        $text       = file_get_contents($file);
        $expected   = file_get_contents($file_expected);
     
        $got = owTpl::parse($text, $data);
        if ($db) {
            step('*** AVT PARSE ***');
            var_dump($text);
            step('*** then... ***');
            var_dump($got);
            step('****');
        }
        $this->assertEquals($expected, $got);
        
        $got = owTpl::getUnknownVars();
        $expected = array('V44');
        $this->assertEquals($expected, $got, 
        'getUnknownVars() indique les variables passees au tpl et
        qui n\'existaient pas !');
        
        $got = owTpl::getMissingVars();
        $this->assertEquals($expected_missing_vars, $got,
        'getMissingVars() indique les variables du tpl qui n\'etaient
         pas dans les donnees fournies !');
        
        $got = owTpl::getUnfilledVars();
        $this->assertEquals($expected_unfilled_vars, $got);
   } // /testFromFile()
   
   public function testCaracteresSpeciaux()
   {
		/* *** comment echapper le caractere special "[" dans le texte optionnel ??? ***
		$test     = 'Message : [ceci est un ultimatum \[echeance fin janvier] pour mofif [!MOTIF!]. Veuillez en tenir compte \[avt fin janvier]]. Vivement les vacances !';
		$data     = array('MOTIF' => 'Too much noise !-O');
		$expected = 'Message : ceci est un ultimatum [echeance fin janvier] pour mofif '. $data['MOTIF'] .'. Veuillez en tenir compte [avt fin janvier]. Vivement les vacances !';
		$got      = owTpl::parse($test, $data);
		$this->assertEquals($expected, $got);
		*/
		// 
		$this->markTestSkipped('Comment accepter les [ dans le texte optionnel ?');
   }
   
/**/
}