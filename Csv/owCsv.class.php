<?php

/*
 */
class owCsv
{
	static protected 
		$erreurs = array(),
		$encoding = 'ISO-8859-1'
	;
	/**
	 *
	 * @param array $data
	 * @param string $dest : absolute path
	 * @param array $options
	 */
	static public function write(array $lines, $dest, array $options = array() )
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$ret = false;
		$defaults = array('separator' => ';');
		$options = array_merge($defaults, $options);
		if (!$fichier = @fopen($dest, "w+") ) { // efface si existe
			throw new Exception ('Erreur majeure : creation du fichier refusee : ' . $dest);
		}
		$i = 0; 
		$expectedCount = count($lines[0]);
		if ('UTF-8' == self::$encoding) {
			fwrite($fichier, "\xEF\xBB\xBF");
		}
		foreach ($lines AS $l) {
			$new_line = implode($options['separator'], $l);
			// 
			if ($db) {
				var_dump('csv ' . $i, $l);
			}
			if(!$ok = fwrite($fichier, $new_line."\r\n" ) ) {
				self::$erreurs[] = 'Erreur ecriture ';
			}
			$i++;
			// $lastCount = $newCount;
		}
		if (!count(self::$erreurs) ) {
			$ret = true;
		}
		if ($db) {
			var_dump('EXIT', $origin);
			exit;
		}
		return $ret;
	}

	/**
	 * lecture d'un fichier csv dans un tableau
	 */
	static public function read($filepath, $csv_separator = ';')
	{
		$ret = array();
		if ( ($handle = fopen($filepath, "r") ) !== FALSE) {
			while ( ($data = fgetcsv($handle, 0, $csv_separator) ) !== FALSE) {
				$ret[] = $data;
			}
		}
		
		return $ret;
	}

}
