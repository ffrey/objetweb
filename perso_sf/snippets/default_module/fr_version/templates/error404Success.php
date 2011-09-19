<?php
$mail = null;
?>
<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTAlert"> 
  <?php echo image_tag('/sf/sf_default/images/icons/cancel48.png', array('alt' => 'page introuvable', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Oups! Page introuvable.</h1>
    <h5>Le serveur a renvoy&eacute; une erreur 404.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>Avez-vous vous-m&ecirc;me tap&eacute; l'url ?</dt>
  <dd>Vous avez peut-&ecirc;tre fait une erreur de frappe dans l'adresse du site (URL). 
  V&eacute;rifiez que vous avez tap&eacute; la bonne orthographe, avec les majuscules requises, etc.</dd>

  <dt>Etiez-vous d&eacute;ja sur notre site avant d'arriver &agrave; cette page ?</dt>
  <dd>Dans ce cas, veuillez nous envoyer un mail<?php if ($mail) : echo ' sur' . mail_to($mail); endif;?>.
  Nous mettrons tout en oeuvre pour rem&eacute;dier &agrave; ce probl&egrave;me au plus vite.</dd>

  <dt>Venez-vous d'un autre site ?</dt>
  <dd>Le lien &eacute;tait peut-&ecirc;tre mal orthographi&eacute; ou obsol&egrave;te. 
  Veuillez nous envoyer un mail<?php if ($mail) : echo ' sur' . mail_to($mail); endif; ?> en nous indiquant le site en question. Nous les contacterons afin de leur signaler le prob&egrave;me.</dd>

  <dt>Et maintenant ?</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Retour &agrave; la page pr&eacute;c&eacute;dente</a></li>
      <li class="sfTLinkMessage"><?php echo link_to('Allez &agrave; la page d\'accueil', '@homepage') ?></li>
    </ul>
  </dd>
</dl>
