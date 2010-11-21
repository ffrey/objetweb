<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php $path = sfConfig::get('sf_relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="title" content="Projet ObjetWeb" />
<meta name="robots" content="index, follow" />
<meta name="description" content="Projet ObjetWeb" />
<meta name="keywords" content="projet, symfony, objetweb" />
<meta name="language" content="fr" />
<title>Projet</title>

<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/sf/sf_default/css/screen.css" />
<!--[if lt IE 7.]>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/sf/sf_default/css/ie.css" />
<![endif]-->

</head>
<body>
<div class="sfTContainer">
  <a title="symfony website" href="http://www.symfony-project.org/"><img alt="symfony PHP Framework" class="sfTLogo" src="<?php echo $path ?>/sf/sf_default/images/sfTLogo.png" height="39" width="186" /></a>
  <div class="sfTMessageContainer sfTAlert">
    <img alt="page not found" class="sfTMessageIcon" src="<?php echo $path ?>/sf/sf_default/images/icons/tools48.png" height="48" width="48" />
    <div class="sfTMessageWrap">
      <h1>Oups! Une erreur s'est produite.</h1>
      <h5>Le serveur a renvoyé : "<?php echo $code ?> <?php echo $text ?>".</h5>
    </div>
  </div>

  <dl class="sfTMessageInfo">
    <dt>Un dysfonctionnement est apparu.</dt>
    <dd>Veuillez nous signaler l'incident par mail, en nous indiquant ce que vous faisiez au moment où s'est produite l'erreur. 
    Nous mettrons tout en oeuvre pour remédier au problème le plus vite possible.
    Veuillez nous excuser pour ce désagrément.</dd>

    <dt>Et maintenant ?</dt>
    <dd>
      <ul class="sfTIconList">
        <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Retour à la page précédente</a></li>
        <li class="sfTLinkMessage"><a href="/">Retournez à la page d'accueil</a></li>
      </ul>
    </dd>
  </dl>
</div>
</body>
</html>
