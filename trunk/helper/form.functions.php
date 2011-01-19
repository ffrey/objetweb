<?php
/**
 * @uses owArray => e_if() :
 */
if (!function_exists('e_if') ) {
	/**
	 * to fill in already filled form fields
	 */
	function e_if ($var_name_exists, $tab = null )
	{
		echo g_if($var_name_exists, $tab);
	}

	function e_checked_if ($var_name_exists, $value, $tab = null)
	{
		$db = false; $origin = __FUNCTION__;
		if ($db) {
			var_dump($origin, $var_name_exists, $value, $tab); exit;
		}
		if (g_if ($var_name_exists, $tab) ) {
			if ($value == $tab[$var_name_exists]) {
				// echo 'CHECKED';
				echo 'checked="checked"';
			}
		}
	}

	/**
	 * renvoie vide ou $default_val si $var_name_exists n'existe pas dans $tab / la valeur sinon
	 * @param $var_name_exists
	 * @param $tab
	 * @param $default_val
	 * @uses owArray::array_flatten_sep() to enable searching for multidimensional keys (ex : [don][transac_date][to])
	 *       => searches for don_transac_date_to inside of flattened array
	 */
	function g_if ($var_name_exists, $tab = null, $default_val = null )
	{
		$db = false;
		$ret = '';
		
		list($tab, $var_name_exists) = _flatten_if_needed($tab, $var_name_exists);
		if (is_null($tab) OR !is_array($tab) ) {
			return $ret;
		}
		if ($db) {
			echo 'existe ' . $var_name_exists . ' ? ';
		}
		$vals = $GLOBALS;
		if (count($tab) ) {
			$vals = $tab;
		}
		if (array_key_exists($var_name_exists, $vals) ) {
			if ($db) { echo ' YES : ' . print_r($vals, true); }
			$ret = $vals[$var_name_exists];
		} else {
			if ($db) { 
				echo ' NO :  '; 
				// echo print_r($vals, true); 
			}
		}
		
		if (empty($ret) AND !is_null($default_val) ) {
			$ret = $default_val;
		}
		return $ret;
	}

	function _flatten_if_needed($tab, $var_name_exists)
	{
		$db = false;
		do {
			if (!class_exists('owArray') ) { break; }	
			if (preg_match('#^\[.*\]$#', $var_name_exists) ) { // if [ & ] exists 
				$sep = '_';
				$key = strtr($var_name_exists, array('][' => $sep) ); //   transform into _ : strip 1st & last > '][' into '_'
				$key = substr($key, 1, strlen($key)-2);
				if ($db) { echo 'new key : ' . $key; }
				$var_name_exists = $key;
				$tab             = owArray::flatten_sep($sep, $tab); //   flatten the array
			}
		} while (false);
		
		return array($tab, $var_name_exists);
	}
	
	/**
	 * différence avec la fonction e_select_options ?!
	 */
	function selected_if($var_name_exists,$value, array $tab = array()){

		$db = false;
		//	print_r($tab); exit;
		if ($db) {
			echo 'selected ' . $var_name_exists . ' ? ';

		}
		$vals = $GLOBALS;
		if (count($tab) ) {
			$vals = $tab;
		}
		if (array_key_exists($var_name_exists, $vals) ) {
			if ($db) { echo ' YES : ' . print_r($vals, true); }
			if($vals[$var_name_exists] == $value ){return 'selected="selected"';};
		} else {
			if ($db) { echo ' NO :  ' . print_r($vals, true); }
		}
	}

	/**
	 * to print select options with $selected_val selected if needed
	 */
	function e_select_options(array $options, $selected_val = null)
	{
		$ret = '';
		$db = false;
		foreach ($options AS $value => $label) {
			if ($db) { echo '<h6>selected ? : ' . $value . ' == ' . $selected_val . ' ?</h6>'; }
			$selected = '';
			if (trim($selected_val) == trim($value) ) {
				if ($db) { echo '<h1> YES : ' . $value . '</h1>'; }
				$selected = 'selected = "selected"';
			} else {
				if ($db) { echo '<h1>NO : ' . $value . '</h1>'; }
			}
			$ret .= '<option value = "'. $value;
			$ret .= '" ' . $selected . '>' . $label . '</option>'."\n";
		}
		print $ret;
	}



	function fm_getListAnnees($emptyOption = null, $anneeSelected = null)
	{
		$listeAnnee = '';
		if (!is_null($emptyOption) ) {
			$listeAnnee .= '<option value="">'.$emptyOption.'</option>';
		}
		$anneeCourante = date("Y") - 1;
		$anneeValue    = date("y") - 1;

		for($i=0; $i<10; $i++){
			$selected = '';
			if ($anneeValue == $anneeSelected) {
				$selected = 'SELECTED';
			}
			$anneeValue++;
			if($anneeValue < 10) {
				$anneeValue = '0'.$anneeValue;
			}
			$anneeCourante ++;
			$listeAnnee .= '<option '.$selected.' value="'.$anneeValue.'">'.$anneeCourante.'</option>';
		}
		return $listeAnnee;
	}

	function fm_getListMoisAnnees($emptyOption = null, array $options = null)
	{
		$options = array_merge(array('annees' => 10, 'mois' => 4), $options);

		$listeAnnee = '';
		if (!is_null($emptyOption) ) {
			$listeAnnee .= '<option value="">'.$emptyOption.'</option>';
		}
		$moisCourant = date("n")+1;
		$moisValue   = date("n")+1;
		$anneeCourante = date("Y") - 1;
		$anneeValue    = date("Y") - 1;
		var_dump($options);
		$annees = $options['annees'];
		$nbMois = 0;
		for($i=0; $i<$annees; $i++){
			$anneeValue++;
			if($anneeValue < 10) {
				$anneeValue = '0'.$anneeValue;
			}
			$anneeCourante ++;
			for($j = $moisValue; $j < 13; $j++) {
				if ($moisValue < 10) {$moisVal = '0'.$moisValue;}
				else $moisVal = $moisValue;
				$listeAnnee .= '<option value="'.$moisVal.$anneeValue.'">'.getMois($moisValue).'  '.$anneeValue.'</option>'."\n";
				$moisValue++;
				$nbMois++;
				if ($options['mois'] <= $nbMois) {
					break 2;
				}
			}
			$moisValue = 1;
		}
		return $listeAnnee;
	}
	
	function getMois($num)	{
			
		$nom_mois = array(
		'1'	=> 'janvier',
		'2'	=> 'f&eacute;vrier',
		'3'	=> 'mars',
		'4'	=> 'avril',
		'5'	=> 'mai',
		'6' => 'juin',
		'7' => 'juillet',
		'8' => 'ao&ucirc;t',
		'9' => 'septembre',
		'10'=> 'octobre',
		'11'=> 'novembre',
		'12'=> 'd&eacute;cembre',
		);

		return $nom_mois[$num];
	}
}
