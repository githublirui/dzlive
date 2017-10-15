<?php 
require DISCUZ_ROOT . "./source/plugin/wxz_live/lib/tis/conf.php";
require DISCUZ_ROOT . "./source/plugin/wxz_live/lib/tis/tis.php";

$api = new TisApi($accessId,$accessKey);
$method = $_REQUEST['method'];

$rst = $api->$method($_REQUEST,$tisId);
echo json_encode($rst);
?>