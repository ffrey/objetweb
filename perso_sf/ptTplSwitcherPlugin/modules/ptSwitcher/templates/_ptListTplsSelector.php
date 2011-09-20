<?php 
/**
 * $apps array
 */

$current_tpl = ptTplSwitcher::getCurrentTpl();

use_javascript('/ptTplSwitcherPlugin/js/onListTplsSelector.js'); 

?>
<script>
ptSwitcher = {};
ptSwitcher.current_tpl = '<?php echo $current_tpl ?>';
</script>
<h5>
ptTplSwitcher - liste des templates
</h5>

<?php 
$WidgetSelectTpls = new sfWidgetFormChoice(array('choices' => $apps ) );
$n = 'jq-switcher-list-tpls-selector';
echo $WidgetSelectTpls->render($n, $current_tpl, array('class' => 'selectInput required valid', 'title' => '/'.tr_front_controller('test').'/ptSwitcher/_showTemplates', 'id' => $n) );
?>
<p>[tpl : <?php echo $current_tpl ?>]</p>
<div id="jq-switcher-reponse-ajax">
<p>
</p>
</div>