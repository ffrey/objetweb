<?php 
$current_tpl = ptTplSwitcher::getCurrentTpl();
use_javascript('/ptTplSwitcherPlugin/js/onAll.js');
use_javascript('/ptTplSwitcherPlugin/js/onIndex.js'); 
?>
<script>
ptSwitcher = {};
ptSwitcher.current_tpl = '<?php echo $current_tpl ?>';
</script>

<div id="jq-switcher-reponse-ajax">
<p>
</p>
</div>
<p>
<?php 
$WidgetSelectTpls = new sfWidgetFormChoice(array('choices' => array('none' => 'aucun', 'design' => 'design') ) );
$n = 'jq-switcher-tpl-selector';
echo $WidgetSelectTpls->render($n, $current_tpl, array('class' => 'selectInput required valid', 'title' => '/'.tr_front_controller('test').'/ptSwitcher/_activate', 'id' => $n) );
?>
</p>