<?php
$mail = null;
?>
<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTAlert"> 
  <?php echo image_tag('/sf/sf_default/images/icons/cancel48.png', array('alt' => 'page introuvable', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Oups! Page introuvable.</h1>
    <h5>Le serveur a renvoy� une erreur 404.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>Avez-vous vous-m�me tap� l'url ?</dt>
  <dd>Vous avez peut-�tre fait une erreur de frappe dans l'adresse du site (URL). 
  V�rifiez que vous avez tap� la bonne orthographe, avec les majuscules requises, etc.</dd>

  <dt>Etiez-vous d�ja sur notre site avant d'arriver � cette page ?</dt>
  <dd>Dans ce cas, veuillez nous envoyer un mail<?php if ($mail) : echo ' sur' . mail_to($mail); endif;?>.
  Nous mettrons tout en oeuvre pour rem�dier � ce probl�me au plus vite.</dd>

  <dt>Venez-vous d'un autre site ?</dt>
  <dd>Le lien �tait peut-�tre mal orthographi� ou obsol�te. 
  Veuillez nous envoyer un mail<?php if ($mail) : echo ' sur' . mail_to($mail); endif; ?> en nous indiquant le site en question. Nous les contacterons afin de leur signaler le probl�me.</dd>

  <dt>Et maintenant ?</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Retour � la page pr�c�dente</a></li>
      <li class="sfTLinkMessage"><?php echo link_to('Allez � la page d\'accueil', '@homepage') ?></li>
    </ul>
  </dd>
</dl>
