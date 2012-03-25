<?php
ini_set('short_open_tags', 1);
var_dump('short_open_tag', ini_get('short_open_tag') );
?>
<?php
echo "<?php is standard\n\r";
?>
<script language="php">
echo "script also !?\n\r";
</script>
<?php ini_set('short_open_tags', 1); ?>
<?
echo "short tags only work if enabled ?";
?>
<?= "as do echo implied tags"; ?>

<% 
echo "idem for asp style tags";
?>