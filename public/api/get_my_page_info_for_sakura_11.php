<?php
    include_once(dirname(__FILE__) . '/../../../account.php');
    header("Access-Control-Allow-Origin: *");
    $echoData = [
        'code' => 403,
        'message' => 'No results were found for the\nrequested user information.\nPlease check and try again.',
        'result' => null
    ];
    switch($_SERVER['REQUEST_METHOD']){
        case 'POST':
            $db = $account->getDB();
            $ck1 = $db->query("SELECT * FROM dearsakura_favourites GROUP BY msgId ORDER BY COUNT(msgId) DESC LIMIT 10"); //fav top 10
            $ck2 = $db->query("SELECT *, avg(rating) FROM dearsakura_rating group by msgId ORDER BY avg(rating) DESC LIMIT 10"); //rating top 10
            $data1 = $ck1->fetch_all(MYSQLI_ASSOC);
            $data2 = $ck2->fetch_all(MYSQLI_ASSOC);

            $msgs = [];
            $_msgids = [];

            foreach (array_merge($data1, $data2) as $v) {
                $msgId = $v['msgId'];
                if(!in_array($msgId, $_msgids)) $_msgids[] = $msgId;
            }
            $ck3 = $db->query("select `id`, `to`, `_from`, `message`, `createdTime` from `dearsakura` where `id` in (".join(", ",$_msgids).")");
            $_msgs = $ck3->fetch_all(MYSQLI_ASSOC);
            foreach ($_msgs as $msg) {
                $msgs[$msg['id']] = $msg;
            }
            
            $echoData['code'] = 200;
            $echoData['message'] = 'success';
            $echoData['result'] = [
                'favorites' => null,
                'rated' => null,
                '' => []
            ];

            $userIds = [];
            foreach($msgs as &$_data){
                $_data['canRating'] = true;
                $_data['isRated'] = false;
                $_rating = 10; //base
                $_rating_count = 1;
                $ck2 = $db->query("select * from `dearsakura_rating` where `msgId`='{$_data['id']}'");
                if($ck2->num_rows > 0){
                    $_rating_data = $ck2->fetch_all(MYSQLI_ASSOC);
                    foreach($_rating_data as $_d){
                        $_rating_count++;
                        $_rating += $_d['rating'];
                        if($_d['accountId'] == $account->accountId){
                            $_data['canRating'] = false;
                            $_data['isRated'] = true;
                            $_data['selfRating'] = $_d['rating'];
                        }
                    }
                }
                $_data['rating'] = round($_rating / $_rating_count, 2);
                
                $_data['has_favourited'] = false;
                $_data['favourite_count'] = 0;
                $ck3 = $db->query("select * from `dearsakura_favourites` where `msgId`='{$_data['id']}'");
                if($ck3->num_rows > 0){
                    $_fav_data = $ck3->fetch_all(MYSQLI_ASSOC);
                    foreach($_fav_data as $_d){
                        $_data['favourite_count']++;
                        if($_d['accountId'] == $account->accountId){
                            $_data['has_favourited'] = true;
                        }
                    }
                }

                $_data['comments'] = [];
                $_data['comment_count'] = 0;
                $ck4 = $db->query("select * from `dearsakura_comments` where `commentable_type`='messageset' and `commentable_id`='{$_data['id']}'");
                if($ck4->num_rows > 0){
                    $_cmt_data = $ck4->fetch_all(MYSQLI_ASSOC);
                    foreach($_cmt_data as $_d){
                        $_data['comment_count']++;
                        $cmtData = [
                            'id' => $_d['id'],
                            'userId' => $_d['accountId'],
                            'message' => $_d['message'],
                            'replies_count' => 0,
                            'createdTime' => $_d['createdTime']
                        ];
                        $_data['comments'][] = $cmtData;
                        if(!in_array($_d['accountId'], $userIds)) $userIds[] = $_d['accountId'];
                    }
                }

            }
            foreach ($data1 as $v1) {
                $_d = $msgs[$v1['msgId']];
                $echoData['result']['favorites'][] = $_d;
            }
            foreach ($data2 as $v2) {
                $_d = $msgs[$v2['msgId']];
                $echoData['result']['rated'][] = $_d;
            }
            if(count($userIds) > 0){
                foreach ($userIds as $mid) {
                    $userData = $account->getUserDataWithId($mid);
                    if($userData) $echoData['result']['users'][] = $userData;
                }
            }
        break;

    }
    http_response_code($echoData['code']);
    echo json_encode($echoData);
?>