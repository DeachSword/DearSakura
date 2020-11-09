<?php
    include_once(dirname(__FILE__) . '/../../../account.php');
    header("Access-Control-Allow-Origin: *");
    $echoData = [
        'code' => 403,
        'message' => 'No results were found for the\nrequested user information.\nPlease check and try again.',
        'result' => null
    ];
    if($account->isLogin){
        switch($_SERVER['REQUEST_METHOD']){
            case 'POST':
                $db = $account->getDB();
                $tableType = @$_POST['commentable_type'];
                $tableId = @$_POST['commentable_id'];
                $message = @$_POST['message'];
                $isErr = false;
                if(trim($message) == ''){
                    $echoData['code'] = 422;
                    $echoData['message'] = 'message is null';
                }
                else if(!empty($tableType) && !empty($tableId)){
                    $ck1 = $db->query("select `id` from `dearsakura` where `id`='{$tableId}'") or $isErr = true;
                    if($ck1->num_rows > 0){
                        if($account->isAdmin){
                            $message = str_replace("'", "\'", $message);
                            $message = str_replace("<", "", $message);
                            $message = str_replace(">", "", $message);
                            $ck1 = $db->query("INSERT INTO `dearsakura_comments` (`accountId`, `commentable_type`, `commentable_id`, `message`) VALUES ('{$account->accountId}', '{$tableType}', '{$tableId}', '{$message}')") or $isErr = true;
                            if($isErr){
                                error_log($db->error);
                                $echoData['code'] = 500;
                                $echoData['message'] = '失敗...內部錯誤(請聯絡FB專頁)';
                            }else{
                                $echoData['code'] = 200;
                                $echoData['message'] = 'success';
                                $latest_id = $db->insert_id;
                            }
                        }else{
                            $echoData['code'] = 503;
                            $echoData['message'] = 'This function is not public';
                        }
                    }else{
                        $echoData['code'] = 404;
                        $echoData['message'] = 'This post has been deleted';
                    }
                }else{
                    $echoData['code'] = 400;
                }
            break;

        }
    }
    http_response_code($echoData['code']);
    echo json_encode($echoData);
?>