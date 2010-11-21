<?php
/**
 *  functions depending upon symfony lib
 **/
 /**
 * @param $url string : full url
 * @return string : url without controller & start trail ("/")
 * @example : 'index.htm/hello/you' => 'hello/you'
 */
function makeInternal($url)
{
    $ret = '';
	$tmp = parse_url($url);
	$scheme = isset($tmp['scheme'])? $tmp['scheme'] . '://' : '';
	$host = isset($tmp['host'])? $tmp['host'] : '';
	$port = isset($tmp['port'])? ':' . $tmp['port'] : '';
	$user = isset($tmp['user'])? $tmp['user'] : '';
	$pass = isset($tmp['pass'])? $tmp['pass'] : '';
	$path = isset($tmp['path'])? $tmp['path'] : '';
	$query = isset($tmp['query'])? '?' . $tmp['query'] : '';
	$fragment = isset($tmp['fragment'])? '#' . $tmp['fragment'] : '';
    $ret = $path . $query . $fragment;
    
    if ('/' === substr($ret, 0, 1) ) {
        $ret = substr($ret, 1); // take off start / if any
    }
    $start = substr($ret, 0, strpos($ret, '/') );
    if (false !== strpos($ret, '.') ) {
        $ret = substr($ret, strpos($ret, '/') );
    }
    if ('/' === substr($ret, 0, 1) ) {
        $ret = substr($ret, 1); // take off start / if any
    }
    return $ret;
}
/*** links ***/
/**
 * @uses mail_to()
 */
function nospam_mail($email, $name = '', $options = array(), $default_value = array() ) {
  $name = (empty($name) )? $email : $name;
  $fuzz = (key_exists('nospam', $options) )? $options['nospam'] : '_CHEZ_';
  $no_spam_mail = preg_replace('/@/', $fuzz, $email);
  $name         = preg_replace('/@/', $fuzz, $name);
  $options['encode'] = true;
  return mail_to(
  $no_spam_mail.'?body=[n\'oubliez-pas de remplacer ' . $fuzz . ' par @ dans l\'adresse, merci]',
  $name,
  $options,
  $default_value
  );
  return mail_to($email, $name, $options, $default_value);
}
/**
 * @uses sfFinder, sfConfig
 * @return string : html <ul> of links <module>/index [! index actions are not checked !]
 */
function basic_ul_navigation($class = '') {
	$ret = '';
	if (!empty($class)) { $ret .= '<ul class="' . $class . '">'; }
	else { $ret .= '<ul>'; }
	$dirs = sfFinder::type('directory')->maxdepth(0)->in(sfConfig::get('sf_app_module_dir') );
	foreach ($dirs AS $module) {
		$ret .= '<li>';
		$ret .= link_to(basename($module), basename($module) . '/index') . '</li>';
	}
	return $ret . '</ul>';
}
/**
 * returns xhtml : links to previous & next link (not page !)
 *
 * @param string $uri : ! must have appended the param to retrieve the item (ex. : ?id_item=) ! <= ! LIMIT !
 * @uses sfPropelPager
 * @uses helper('Text') : truncate_text !
 * @uses helper('Url')   : url_for(), link_to()
 * @uses helper('Assets') : image_tag()
 */
function my_pager_navigation_by_item_v2(sfPropelPager $pager, $uri, $options)
{
$acceptedOptions = array(
'method'        => 'getId',
'textMethod'    => 'getNom', 
'cursorParam'   => 'c', 
'pagerAssets'   => '/images/pager/',
'previousImg'   => 'previous.png',
'nextImg'       => 'next.png',
);
$opt = myUtil::getOpt($options, $acceptedOptions);
  $navigation = '';
  do {
    if (1 >= $pager->getNbResults() ) { $navigation .= '<!-- moins de 2 liens dans cette catégorie -->'; break; }
    if (1 < $pager->getCursor() ) {
      $previous_lien = $pager->getPrevious();
      $fullLink  = url_for($uri . $previous_lien->$opt['method']() );
      $sep = (strpos($fullLink, '?') )? '&' : '?';
      $fullLink .= $sep . $opt['cursorParam'] . '=' . ($pager->getCursor()-1);
      $img       = image_tag($opt['pagerAssets'] . $opt['previousImg']);
      $textLink  = truncate_text($previous_lien->$opt['textMethod'](), 10);
      $navigation .= link_to($img . $textLink, $fullLink);
      $navigation .= ' - ';
    }

    $current_lien = $pager->getObjectByCursor($pager->getCursor() );
    $navigation .= truncate_text($current_lien->$opt['textMethod'](), 10);

    if ($pager->getNbResults() > $pager->getCursor() ) {
      $navigation .= ' - ';
      $next_lien = $pager->getNext();
      $fullLink  = url_for($uri . $next_lien->$opt['method']() );
      $sep = (strpos($fullLink, '?') )? '&' : '?';
      $fullLink .= $sep . $opt['cursorParam'] . '=' . ($pager->getCursor()+1);
      $img       = image_tag($opt['pagerAssets'] . 'next.png');
      $textLink  = truncate_text($next_lien->$opt['textMethod']());
      $navigation .= link_to($textLink . $img, $fullLink);
    }
  } while (false);
  return $navigation;
}

/**
	 * gets login-needing uri (before forwarding to auth form)
	 * - if POST => returns 'POST'
	 * - ! if auth form submission fails => requested Uri remains the same !!!
	 *
	 * @param sfWebRequest $req
	 * @param string $requestedUriField : name of the field where redirection uri is stored
	 * @return string : <uri> / 'POST'
	
	function mySfGetRequestedUri(sfWebRequest $req, $requestedUriField = 'P_requestedUri') {
		$uri = '';
		do {
			if ($req->hasParameter ( $requestedUriField )) { // auth form submission has failed
				$uri = $req->getParameter ( $requestedUriField );
				owSf::sf_log ( 'auth form being re-submitted => no new redirection' );
				break;
			}
			if ('POST' === $req->getMethod ()) {
				$uri = 'POST';
				owSf::sf_log ( 'no redirection due to Post' );
				break; // we won't redirect onto a form submission !
			}
			$uri = $req->getUri (); // ? should not work <= login failure triggers a REDIRECT ! => use getReferrer instead ?
			owSf::sf_log ( 'redirection : ' . $uri . ' [methodName : ' . $req->getMethod () . ']' );
		} while ( false );
		return $uri;
	}
     */