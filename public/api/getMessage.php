<?php
include_once(dirname(__FILE__) . '/../../../account.php');
header("Access-Control-Allow-Origin: *");
$to = @$_GET['to'];
$res_data = [
    "code" => 20,
    "message" => "無效請求",
	"result" => null
];
$db = $account->getDB();
if(!empty($to)){
	$name = str_replace("'", "\'", $to);
	$ck = $db->query("select `id`, `to`, `_from`, `message`, `createdTime` from `dearsakura` where `to`='{$name}' OR tags REGEXP CONCAT('(^| )', '{$name}' ,'( |$)')");
	$data = $ck->fetch_all(MYSQLI_ASSOC);

	$res_data['code'] = 0;
	$res_data['message'] = 'success';
	$res_data['result'] = $data;
}else if(isset($_GET['msgid'])){
	$msgId = $_GET['msgid'];
	$res_data['code'] = 0;
	$res_data['message'] = 'success';
	$ck = $db->query("select `id`, `to`, `_from`, `message`, `createdTime` from `dearsakura` where `id`='{$msgId}'");
	$data = $ck->fetch_all(MYSQLI_ASSOC);
	$res_data['result'] = $data;
}

if(!empty($res_data['result']) && count($res_data['result']) > 0){
	foreach($res_data['result'] as &$_data){
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
	}
}
echo json_encode($res_data);