<?php
    include_once(dirname(__FILE__) . '/../../../account.php');
    header("Access-Control-Allow-Origin: *");
    $echoData = [
        'code' => 403,
        'message' => 'No results were found for the\nrequested user information.\nPlease check and try again.',
        'result' => null
    ];
    if($account->isLogin){
        $db = $account->getDB();
        switch($_SERVER['REQUEST_METHOD']){
            case 'POST':
                if(isset($_POST['msgId'])){
                    $msgId = (int)$_POST['msgId'];

                    $ck1 = $db->query("select * from `dearsakura_favourites` where `msgId`='{$msgId}' and `accountId`='{$account->accountId}'");
                    if($ck1->num_rows == 0){
                        $isErr = false;
                        $ck2 = $db->query("INSERT INTO `dearsakura_favourites` (`msgId`, `accountId`) VALUES ('{$msgId}', '{$account->accountId}')") or $isErr = true;
                        if($isErr){
                            error_log($db->error);
                            $echoData['message'] = '失敗...內部錯誤(請聯絡FB專頁)';
                        }else{
                            $echoData['code'] = 200;
                            $echoData['message'] = 'success';
                            $ck3 = $db->query("select id from `dearsakura_favourites` where `msgId`='{$msgId}'");
                            $echoData['result'] = [
                                'favourite_count' => $ck3->num_rows
                            ];
                        }
                    }else{
                        //重複
                        //應該提醒DELETE? 還是自動刪除就好呢?
                        $echoData['code'] = 101;
                    }
                }else{
                    $echoData['code'] = 400;
                }
            break;
            case 'DELETE':
                $raw = file_get_contents('php://input');
                try {
                    $data = json_decode($raw, true);
                    if(isset($data['msgId'])){
                        $ck1 = $db->query("DELETE FROM `dearsakura_favourites` WHERE `msgId`='{$data['msgId']}' and `accountId`='{$account->accountId}'");
                        $echoData['code'] = 200;
                        $echoData['message'] = 'success';
                        $ck2 = $db->query("select id from `dearsakura_favourites` where `msgId`='{$data['msgId']}'");
                        $echoData['result'] = [
                            'favourite_count' => $ck2->num_rows
                        ];
                    }else{
                        $echoData['code'] = 400;
                    }
                } catch (\Throwable $th) {
                    $echoData['code'] = 400;
                }
            break;
        }
    }else{
        $echoData['code'] = 401;
    }
    http_response_code($echoData['code']);
    echo json_encode($echoData);
?>