<?php
    header("Access-Control-Allow-Origin: *");
    include_once(dirname(__FILE__) . '/../../../account.php');
    $echoData = [
        'code' => 401,
        'message' => 'No results were found for the\nrequested user information.\nPlease check and try again.',
        'result' => null
    ];
    if($account->isLogin){
        $db = $account->getDB();
        $ck = $db->query("select * from `dearsakura_bans` where `accountId`='{$account->accountId}'");
        if($ck->num_rows > 0){
            $echoData['code'] = 403;
            $echoData['message'] = 'You have been banned.\nPlease go to the Facebook page to appeal after the cooldown of three months.';
            $echoData['result'] = null;
        }else{
            $echoData['code'] = 200;
            $echoData['message'] = 'success';
            $echoData['result'] = $account->issetToken('DearSakura');
        }
        echo json_encode($echoData);
        return;
    }
    http_response_code(401);
?>