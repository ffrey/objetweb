<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTLock"> 
  <?php echo image_tag('/sf/sf_default/images/icons/lock48.png', array('alt' => 'Connection nécessaire', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Connection nécessaire</h1>
    <h5>Ceci est un espace privé.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>Comment accéder à cette page ?</dt>
  <dd>Veuillez vous rendre à la page de connection et y rentrer votre identifiant et mot de passe.</dd>

  <dt>Et maintenant ?</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTLinkMessage"><?php echo link_to('Allez à la page de connection', sfConfig::get('sf_login_module').'/'.sfConfig::get('sf_login_action')) ?></li>
      <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Retour à la page précédente</a></li>
    </ul>
  </dd>
</dl>
