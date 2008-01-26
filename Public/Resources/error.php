<?php

$size = ($_GET['size']>0) ? (int)$_GET['size']: 100;


echo "<pre>";
passthru("tail -$size /var/log/apache/visudo-smartest.com-error.log");
echo "</pre>";

?>
