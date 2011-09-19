<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTMessage"> 
  <?php echo image_tag('/sf/sf_default/images/icons/ok48.png', array('alt' => 'ok', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Projet créé</h1>
    <h5>Félicitations! L'infrastructure de votre projet est en place.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>Installation réussie.</dt>

  <dt>Ceci est une page temporaire</dt>
  <dd>Elle sera bientôt remplacée par la page d'accueil de votre site.</dd>

  <dt>Revenez bientôt</dt>

</dl>
