<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTLock"> 
  <?php echo image_tag('/sf/sf_default/images/icons/lock48.png', array('alt' => 'credentials required', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Autorisation nécessaire</h1>
    <h5>Ceci est un espace privé.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>Il semble que vous n'ayez pas les autorisations nécessaires pour accéder à cette page</dt>
  <dd>Bien que vous soyez déja connecté, l'accés à cette page demande des droits spécifiques que vous ne semblez pas posséder.</dd>

  <dt>Comment accéder à cette page ?</dt>
  <dd>Veuillez vous adresser à l'administrateur du site afin qu'il vous donne les droits nécessaires.</dd>

  <dt>Et maintenant ?</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Retour à la page précédente</a></li>
      <li class="sfTLinkMessage"><?php echo link_to('Allez à la page d\'accueil', '@homepage') ?></li>
    </ul>
  </dd>
</dl>
