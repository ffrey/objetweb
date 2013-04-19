<?php
/**
 * static methods to act as helper functions for routine string processing
 * 
 * @package    b2c
 * @subpackage utils
 * @author FFreyssenge
 * @version 1.1  / 2009 03 17
 * * added : 
 * * * function stripSpaces()
 * @throws Exception
 */
class owString
{
  /**
 * make a filename secure for all platforms + web
 * : no space, no non-utf8 char
 * => can be used to create slugs (@see self::slugity() )
 *
 * @param string $str
 * @return string
 */
  public static function strtoSecureFileNameFormat($str, $maxLength = 0)
  {
    if (empty($str)) { throw new Exception(__FUNCTION__ . ' : arg vide !'); }
    // enlever les accents
    $tmp = self::stripAccents($str);
    // remplacer les caracteres autres que lettres, chiffres et point par _
    $secureFormat= strtolower(preg_replace('/([^.a-z0-9]+)/i', '_', $tmp));
    if (empty($secureFormat)) { throw new Exception(__FUNCTION__ . ' : arg vide ! [' . $secureFormat . ']'); }

    if (0 < $maxLength AND $maxLength < strlen($secureFormat)) {
      $dbt = strlen($secureFormat) - $maxLength;
      $shortened = substr($secureFormat, $dbt, $maxLength);
      $secureFormat = $shortened;
    }

    return $secureFormat;
  }
  
  public static function stripAccents($str)
  {
  	$from = 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ%';
  	$to	  = 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy_';
  	if (mb_detect_encoding($str, 'UTF-8', true) ) {
  		$ret = self::strtr_utf8($str, $from, $to);
  	} else {
  		$ret = strtr($str, $from, $to);
  	}
  	return $ret;
  }
  
  public static function strToUtf8($str)
  {
      return strtr($str,
      'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ%', 
      'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy_'
      );

  }
  
  /**
   * strtr pour les strings encodees en utf-8
   * @author http://stackoverflow.com/questions/1454401/how-do-i-do-a-strtr-on-utf-8-in-php
   * @param string $str
   * @param string $from
   * @param string $to
   * @return string
   */
  public static function strtr_utf8($str, $from, $to) {
  	$keys = array();
  	$values = array();
  	preg_match_all('/./u', $from, $keys);
  	preg_match_all('/./u', $to, $values);
  	$mapping = array_combine($keys[0], $values[0]);
  	return strtr($str, $mapping);
  }
  
  public static function slugify($str, $maxLength = 0)
  {
  	$str = html_entity_decode(trim($str), ENT_QUOTES);
  	$str = strtr($str, '.', '_'); // strip . !
  	return self::strtoSecureFileNameFormat($str, $maxLength);
  }
  
  /**
  * removes all spaces + line breaks 
  * @author : curtis_3 on http://answers.yahoo.com/question/index?qid=20070830120013AATiccZ
  */
  public static function stripSpaces($str)
  {
  if (!is_string($str) ) { throw new Exception (__FUNCTION__ . ' accepts only string as param'); }
  $str = str_replace(array("\n", "\r", "\t", " ", "\o", "\xOB"), '', $str);
  return $str;
  }
  
  	public static function implodeNotEmpty($glue = ' ', array $d)
	{
		$ret = ''; $g = '';
		foreach ($d AS $k => $v) {
			if (is_null($v) OR empty($v) ) { continue; }
			$ret .= $g.$v;
			$g = $glue;
		}
		return $ret;
	}
	
  ### chiffres en lettres ! ###

	public function convertir($Montant)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$grade = array(0 => "zero ",1=>" milliards ",2=>" millions ",3=>" mille ");
		$Mon = array(0=>"euro",1=>"euros",2=>"centime",3=>"centimes");

		// Mise au format pour les ch�ques et le SWI
		$Montant = number_format($Montant,2,".","");

		if ($Montant == 0)
		{
			$result = $grade[0].$Mon[0];
		}else
		{
			$result = "";

			// Calcule des Unit�s
			$montant = intval($Montant);

			// Calcul des centimes
			$centime = round(($Montant * 100) - ($montant * 100),0);

			// Traitement pour les Milliards
			$nombre = $montant / 1000000000;
			$nombre = intval($nombre);
			if ($nombre > 0)
			{
				if ($nombre > 1)
				{
					$result = $result.self::cenvtir($nombre).$grade[1];
				}else
				{
					$result = $result." Un ".$grade[1];
					$result = substr($result,0,13)." ";
				}
				$montant = $montant - ($nombre * 1000000000);
			}

			// Traitement pour les Millions
			$nombre = $montant / 1000000;
			$nombre = intval($nombre);
			if ($nombre > 0)
			{
				if ($nombre > 1)
				{
					$result = $result.self::cenvtir($nombre).$grade[2];
				}else
				{
					$result = $result." Un ".$grade[2];
					$result = substr($result,0,12)." ";
				}
				$montant = $montant - ($nombre * 1000000);
			}

			// Traitement pour les Milliers
			$nombre = $montant / 1000;
			$nombre = intval($nombre);
			if ($nombre > 0)
			{
				if ($nombre > 1)
				{
					$result = $result.self::cenvtir($nombre).$grade[3];
				}else
				{
					$result = $result.$grade[3];
				}
				$montant = $montant - ($nombre * 1000);
			}

			// Traitement pour les Centaines & centimes
			$nombre = $montant;
			if ($nombre>0)
			{
				$result = $result.self::cenvtir($nombre);
				if ($db) {
					var_dump($origin, 'centaine', $nombre, $result);
				}
			}
			// Traitement si le montant = 1
			if ((substr($result,0,7) == " et un " and strlen($result) == 7))
			{
				$result = substr($result,3,3);
				$result = $result.$Mon[0];
				if (intval($centime) != 0)
				{
					$differ = self::cenvtir(intval($centime));
					if (substr($differ,0,7) == " et un ")
					{
						if ($result == "")
						{
							$differ = substr($differ,3);
						}
						$result = $result." ".$differ.$Mon[2];
					}else
					{
						$result = $result." et ".$differ.$Mon[3];
					}
				}
				// Traitement si le montant > 1 ou = 0
			}else
			{
				if ($result != "")
				{
					$result = $result.$Mon[1];
				}
				if (intval($centime) != 0)
				{
					$differ = self::cenvtir(intval($centime));
					if (substr($differ,0,7) == " et un ")
					{
						if ($result == "")
						{
							$differ = substr($differ,3);
						}
						$result = $result." ".$differ.$Mon[2];
					}else
					{
						if ($result != "")
						{
							$result = $result." et ".$differ.$Mon[3];
						}else
						{
							$result = $differ.$Mon[3];
						}
					}
				}
			}
		}
		return trim($result);
	}

	// fonction de convertion d'un chiffre � 3 digits en lettre
	protected function cenvtir($Valeur)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		if ($db) {
			var_dump($origin, $Valeur);
		}
		$code = "";
		//texte en clair
		$SUnit = array(1=>"et un ", 2=>"deux ", 3=>"trois ", 4=>"quatre ", 5=>"cinq ", 6=>"six ", 7=>"sept ", 8=>"huit ", 9=>"neuf ", 10=>"dix ", 11=>"onze ", 12=>"douze ", 13=>"treize ", 14=>"quatorze ", 15=>"quinze ", 16=>"seize ", 17=>"dix-sept ", 18=>"dix-huit ", 19=>"dix-neuf ");
		$sDiz = array(20=> "vingt ", 30=> "trente ", 40=>"quarante ", 50=>"cinquante ", 60=>"soixante ", 70=>"soixante ", 80=>"quatre-vingt ", 90=>"quatre-vingt ");

		if ($Valeur>99)
		{
			$N1= intval($Valeur/100);
			if ($N1>1)
			{
				$code = $code.$SUnit[$N1];
			}
			$Valeur = $Valeur - ($N1*100);
			if ($code != "")
			{
				if ($Valeur == 0)
				{
					$code = $code."cents ";
				} else {
					$code = $code."cent ";
				}
			} else {
				$code = "cent ";
			}
		}
		if ($Valeur != 0)
		{
			$sep = '';
			$complete = $Valeur;
			if ($Valeur > 19)
			{
				$N1 = intval($Valeur/10)*10;
				$code = $code.$sDiz[$N1];
				if (($Valeur>=70) and($Valeur<80)or($Valeur>=90))
				{
					if ($db) {
						var_dump('dizaines french', $Valeur);
					}
					$Valeur = $Valeur + 10;
				}
				$Valeur = $Valeur - $N1;
				if ($db) { var_dump('apres soustr', $Valeur, 'code', $code); }
			}
			if ($Valeur > 0)
			{
				$sep = ' ';
				if (($complete>=70) and($complete<80) ) {
					if ($db) {
						var_dump('dizaine french', $Valeur);
					}
					$sep = '-';
				}
				$code = trim($code);
				$code = $code.$sep.$SUnit[$Valeur];
				if ($db) { var_dump($origin, $code); }
			}
		}
		if ($db) {
			var_dump($origin, 'result', $code);
		}
		return $code;
	}
  
  /**
   * @param string $s : string of pairs (key/value) delimited by $sep
   * $pairs are separated by '='
   * if '=' is found multiple times within 2 $sep => only first one is considered a separator / others are considered part of value
   */
  public static function keyValueDecode($s, $sep = ';')
  {
    $ret = array();
	$t = explode($sep, $s);
	foreach ($t AS $chaine) {
		$firstEqual = strpos($chaine, '=');
		if (!$firstEqual) { continue; } // 0 ou false
		$key = substr($chaine, 0, $firstEqual);
		$val = substr($chaine, $firstEqual+1);
		$ret[$key] = $val;
	}
	return $ret;
  }
  
}