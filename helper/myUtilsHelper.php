<?php
/**
 * functions depending only no vendor library
 * @version 1 2009/04/25
 * @author pacoweb
 */

/*** navigation ***/
/**
 * ensures path is web compatible : starts with '/' + all slashes are forward
 */
function _web_path($path)
{
	if (empty($path)) return $path;
	if ( !strpos($path, '\\') AND '/' === $path[0] ) return $path;
	$webPath = str_replace('\\', '/', $path);
	// ? : $webPath = str_replace('//', '/', $webPath);
	if ( '/' !== $webPath[0] ) $webPath = '/' . $webPath;
	return $webPath;
}
/**
 * shortcut
 * @uses _web_path()
 */
function _W($path)
{
	return _web_path($path);
}

/*** date ***/
/**
 * idem date() BUT from sql format date instead of timestamp
 *
 * @param string : idem date()
 * @param string : sql format yyyy-mm-dd hh:ii:ss
 * @return string
 * @todo : add to myUtil or inside myDateHelper.php ?
 */
function myDate($format, $sqlDate)
{
	$Date = new DateTime($sqlDate);
	return $Date->format($format);
}

/*** url ***/
/**
 * @author : url pattern from sfValidatorUrl by Fabien Potencier <fabien.potencier@symfony-project.com>
 * @return array :
 * * 'fullHost' => 'http://hello.com'
 * * 'fullPath' => '/index/sub/index.htm'
 * * 'fullQuery => '?hello=bonjour#first'
 */
function owUtilsParseUrl($url)
{
	$valid_url_pattern = '~^
      (https?)://                       # http (+SSL)
      (
        ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
          |                                   #  or
        \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
      )
      (:[0-9]+)?                              # a port (optional)
      (/?|/\S+)                               # a /, nothing or a / with something
    $~ix';
	if (!preg_match($valid_url_pattern, $url) ) {
		throw new Exception ('invalid url : ' . $url);
	}
	$tmp = parse_url($url);
	$scheme = isset($tmp['scheme'])? $tmp['scheme'] . '://' : '';
	$host = isset($tmp['host'])? $tmp['host'] : '';
	$port = isset($tmp['port'])? ':' . $tmp['port'] : '';
	$user = isset($tmp['user'])? $tmp['user'] : '';
	$pass = isset($tmp['pass'])? $tmp['pass'] : '';
	$path = isset($tmp['path'])? $tmp['path'] : '';
	$query = isset($tmp['query'])? $tmp['query'] : '';
	$fragment = isset($tmp['fragment'])? $tmp['fragment'] : '';

	return array(
	'fullHost'    => $scheme . $host . $port,
	'fullPath'    => $path,
	'fullQuery'   => $query . $fragment,
	);
}

/*** links ***/
function addParams(array $params, $url)
{
	if (!sizeof($params) ) return $url;
	do {
		if (!$pos = strpos($url, '?') ) { // no existing param
			$base_url = $url;
			$all_params = $params;
			break;
		}
		$url_parts = explode('?', $url);
		$base_url = $url_parts[0];
		// we add old params replaced if needed
		$replaced_params = array();
		$temp = $url_parts[1]; // ex.: 'lkj=78&oiui=99
		$raw_params = explode('&', $temp);
		foreach ($raw_params AS $raw) {
			$t = explode('=', $raw);
			if (2 != sizeof($t) ) { continue; } // anomalie !
			if (!key_exists($t[0], $params) ) {
				$replaced_params[$t[0]] = $t[1];
				continue; } // no new value
				$replaced_params[$t[0]] = $params[$t[0]]; // replaced by new value
				unset($params[$t[0]]);
		}
		$all_params = array_merge($replaced_params, $params);
	} while (false);
	$new_url = $base_url . '?';
	foreach ($all_params AS $var => $val) {
		$new_url .= $var . '=' . $val . '&';
	}

	return substr($new_url, 0, strlen($new_url)-1);
}
