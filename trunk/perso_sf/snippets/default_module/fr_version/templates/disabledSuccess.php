<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTAlert"> 
  <?php echo image_tag('/sf/sf_default/images/icons/disabled48.png', array('alt' => 'Page désactivée', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Cette page n'est pas disponible.</h1>
    <h5>Elle a été d&eacute;sactiv&eacute; par l'administrateur du site.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">

  <dt>Et maintenant ?</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Retour &agrave; la page pr&eacute;c&eacute;dente</a></li>
      <li class="sfTLinkMessage"><?php echo link_to('Page d\'accueil', '@homepage') ?></li>
    </ul>
  </dd>
</dl>
