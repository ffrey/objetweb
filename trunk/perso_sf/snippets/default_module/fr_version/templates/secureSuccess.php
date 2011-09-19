<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTLock"> 
  <?php echo image_tag('/sf/sf_default/images/icons/lock48.png', array('alt' => 'credentials required', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Autorisation n&eacute;cessaire</h1>
    <h5>Ceci est un espace priv&eacute;.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>Il semble que vous n'ayez pas les autorisations n&eacute;cessaires pour acc&eacute;der � cette page</dt>
  <dd>Bien que vous soyez d�ja connect&eacute;, l'acc&eacute;s � cette page demande des droits sp&eacute;cifiques que vous ne semblez pas poss&eacute;der.</dd>

  <dt>Comment acc&eacute;der � cette page ?</dt>
  <dd>Veuillez vous adresser � l'administrateur du site afin qu'il vous donne les droits n&eacute;cessaires.</dd>

  <dt>Et maintenant ?</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Retour � la page pr�c�dente</a></li>
      <li class="sfTLinkMessage"><?php echo link_to('Allez � la page d\'accueil', '@homepage') ?></li>
    </ul>
  </dd>
</dl>
