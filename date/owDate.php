<?php

/**
 * static methods to act as helper functions for routine date processing
 *
 * @version 1.1  / 2009 03 17
 * * added : 
 * * * function stripSpaces()
 * @throws Exception
 */
class owDate 
{
    /**
     * ! la date du jour sans heure est consideree comme PASSEE !!!
	 * 
     * @param string $str : 'dd/mm/yyyy' / 'dd/mm/yyyy hh:mn:ss'
	 *              separateurs acceptes : - / espace
     * @return bool
     */
    public static function isPassee($date) {
        $db = false;
        $origin = __CLASS__ . '::' . __FUNCTION__;
		if ($db) { var_dump($origin, $date, 'format date', $format_date, 'format date+heure', $format_date_heure); }
		$sep = '[/\-\s]{1,1}'; // separateurs acceptes pour la date
		$format_date       = '(\d{2})'.$sep.'(\d{2})'.$sep.'(\d{4})';
		$format_date_heure = $format_date .'\s{1}(\d{2}):(\d{2}):(\d{2})'; 
        if (preg_match('#^'.$format_date.'$#', $date, $p)) {
		    if ($db) {
				var_dump($origin, 'apres preg_match', $p);
				// exit;
			}
            $formatUS = $p[3] . '-' . $p[2] . '-' . $p[1];
        } else if (preg_match('#^'.$format_date_heure.'$#', $date, $p)) {
            $formatUS = $p[3] . '-' . $p[2] . '-' . $p[1] . ' ' . $p[4] . ':' . $p[5] . ':' . $p[6];
        } else {
            throw new Exception($date . ' n\'est pas au format "dd/mm/yyyy" ou "dd/mm/yyyy hh:mn:ss"');
        }
        if ($db) {
            var_dump($origin, 'apres preg_match', $p);
            // exit;
        }
        $now = time();
        $dateTime = strtotime($formatUS);
        $ret = ($dateTime < $now);
        if ($db) {
            var_dump($origin, $ret);
            print '<h3>' . $date . ' < NOW (' . strftime('%d/%m/%Y %H:%M:%S', $now) . ') ?</h3>';
            print '<h3>' . $dateTime . ' < ' . $now . ' ?</h3>';
        }
        return $ret;
    }

}