<?php
$postStr = file_get_contents('php://input');
file_put_contents('/www/wwwroot/wwo/web/component/ticket.txt',$postStr);

//$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
//$scenes = json_decode($postObj->EventKey, true);

  	// $AppId = $postObj->AppId; //Ticket
    //$Encrypt = $postObj->Encrypt; //openid
    //file_put_contents('/www/wwwroot/wwo/web/component_verify_ticket/'.$AppId.'.txt',$Encrypt);


