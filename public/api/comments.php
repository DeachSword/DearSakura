<?php
    include_once(dirname(__FILE__) . '/../../../account.php');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
    $echoData = [
        'code' => 403,
        'message' => 'No results were found for the\nrequested user information.\nPlease check and try again.',
        'result' => null
    ];
    $isDev = false;
    if($account->isLogin){
        $db = $account->getDB();
        switch($_SERVER['REQUEST_METHOD']){
            case 'POST':
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
                        if(!$isDev || $account->isAdmin){
                            $message = str_replace("'", "\'", $message);
                            $message = str_replace("<", "", $message);
                            $message = str_replace(">", "", $message);
                            $ck2 = $db->query("INSERT INTO `dearsakura_comments` (`accountId`, `commentable_type`, `commentable_id`, `message`) VALUES ('{$account->accountId}', '{$tableType}', '{$tableId}', '{$message}')") or $isErr = true;
                            if($isErr){
                                error_log($db->error);
                                $echoData['code'] = 500;
                                $echoData['message'] = '失敗...內部錯誤(請聯絡FB專頁)';
                            }else{
                                $echoData['code'] = 200;
                                $echoData['message'] = 'success';
                                $latest_id = $db->insert_id;
                                $ck3 = $db->query("select * from `dearsakura_comments` where `id`='{$latest_id}'");
                                $_d = $ck3->fetch_all(MYSQLI_ASSOC)[0];
                                $cmtData = [
                                    'id' => $_d['id'],
                                    'userId' => $_d['accountId'],
                                    'message' => $_d['message'],
                                    'replies_count' => 0,
                                    'createdTime' => $_d['createdTime']
                                ];
                                $echoData['result'] = $cmtData;
                            }
                        }else{
                            $echoData['code'] = 503;
                            $echoData['message'] = 'This function is not public.';
                        }
                    }else{
                        $echoData['code'] = 404;
                        $echoData['message'] = 'This post has been deleted.';
                    }
                }else{
                    $echoData['code'] = 400;
                }
            break;
            case 'DELETE':
                $raw = file_get_contents('php://input');
                try {
                    $data = json_decode($raw, true);
                    if(isset($data['comment_id'])){
                        $comment_id = (int)$data['comment_id'];
                        $ck1 = $db->query("select * from `dearsakura_comments` where `id`='{$comment_id}' and `accountId`='{$account->accountId}' and `deletedTime` is null");
                        if($ck1->num_rows > 0){
                            $deletedTime = date('Y-m-d H:i:s'); 
                            $ck2 = $db->query("UPDATE `dearsakura_comments` set `deletedTime`='{$deletedTime}' where `id`='{$comment_id}' and `accountId`='{$account->accountId}'");
                            if($ck2){
                                $echoData['code'] = 200;
                                $echoData['message'] = 'success';
                            }else{
                                error_log($db->error);
                                $echoData['code'] = 500;
                                $echoData['message'] = '失敗...內部錯誤(請聯絡FB專頁)';
                            }
                        }else{
                            $echoData['message'] = 'This post has been deleted.';
                            $echoData['code'] = 404;
                        }
                    }else{
                        $echoData['code'] = 400;
                    }
                } catch (\Throwable $th) {
                    $echoData['code'] = 500;
                }
            break;
        }
    }
    http_response_code($echoData['code']);
    echo json_encode($echoData);
?>