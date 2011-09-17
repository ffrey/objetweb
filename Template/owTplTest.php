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
            'VILLE'   => 'Orléans',
        );
        $tests = array(
            array(
'[Demandeur : [!CIV2!]
][Bâtiment, appartement, escalier, étage : [!V2!]
][Résidence, lotissement : [!V3!]
][Ville : [!VILLE!]]',
'Demandeur : Monsieur
Résidence, lotissement : Les Lilas
Ville : Orléans',),
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
[Chère [!CIV2!] ][[!SURNAME!] ][[!NAME!]],
[Votre commentaire est [!COMMENT!].]';
        $expected = '
Chère Madame Michaud,
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
            'V2'   => '3ème étage',
            // 'V3'   => 'Bât. Les Lilas',
            
            'V4'   => 'rue de la liberté',
            'V5'   => '',
            'VILLE' => 'Orléans',
            'PAYS'  => 'France',
            'TELEPHONE' => '',
            'TELEPHONE MOBILE' => '06 12 78 34 43',
            'MESSAGE' => 'Pour la Somalie',//
        );
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
         
        // $this->markTestSkipped('tester a partir d\'un fichier');
   }
/**/
   }