<?php
class ADConf {
	const	AccessId = "595673796777";		//把...替换成自己的AccessId
	const	AccessKey = "eGKM84f3bLpePog33d16ONSLLtaN791k";		//把...替换成自己的AccessKey
	const 	TisId = "be69a80b38183882b2ab3f85815911ff";	
}
$accessId = ADConf::AccessId;
$accessKey = ADConf::AccessKey;
if(!empty($_REQUEST["tisId"])){
	$tisId = $_REQUEST["tisId"];
} else {
	$tisId = ADConf::TisId;
}
?>