<?php
/**
 * static methods to act as helper functions for routine string processing
 *
 * @version 1.1  / 2009 03 17
 * * added : 
 * * * function stripSpaces()
 * @throws Exception
 */
class myString
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
    $tmp = self::strToUtf8($str);
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
  public static function strToUtf8($str)
  {
      return strtr($str,
      'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ%', 
      'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy_'
      );

  }
  public static function slugify($str, $maxLength = 0)
  {
  	$str = html_entity_decode(trim($str), ENT_QUOTES, 'ISO-8859-1');
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
}