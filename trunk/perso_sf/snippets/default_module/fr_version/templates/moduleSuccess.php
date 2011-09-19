<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTMessage"> 
  <?php echo image_tag('/sf/sf_default/images/icons/ok48.png', array('alt' => 'module créé', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Module "<?php echo $sf_params->get('module') ?>" cr&eacute;&eacute;</h1>
    <h5>F&eacute;licitations! L'infrastructure de votre projet est en place.</h5>
  </div>
</div>
  <dt>Installation r&eacute;ussie.</dt>

  <dt>Ceci est une page temporaire</dt>
  <dd>Elle sera bientôt remplac&eacute;e par la page d'accueil de votre site.</dd>

  <dt>Revenez bient&ocirc;t</dt>
