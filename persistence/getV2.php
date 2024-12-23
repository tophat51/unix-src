<?php
error_reporting(E_ERROR | E_PARSE);
  include_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
   header("Content-Type: application/json");
    $errorResponse = [
        "errors" => [
            [
                "code" => 0,
                "message" => "MethodNotAllowed"
            ]
        ]
    ];

function removeEverythingBefore($in, $before) {
    $pos = strpos($in, $before);
    return $pos !== FALSE
        ? substr($in, $pos + strlen($before), strlen($in))
        : "";
}

function removeEverythingAfter($in, $before) {
    $pos = strpos($in, $before);
    return $pos !== FALSE
        ? substr($in, $pos - strlen($before), strlen($in))
        : "";
}
if(isset($_GET["placeId"])&&isset($_GET["scope"])&&isset($_GET["type"])){
	$values=[];
	$input = file_get_contents('php://input');
	$qkeys = explode("&",substr($input, 1));
	$tempTable = array();
	foreach($qkeys as &$val){
		$after = substr($val, 0, strpos($val, "="));
		$tempTable[$after]=removeEverythingBefore($val,"=");
	}
	$qkeys = $tempTable;
	$tempTable = null;
		
	if(isset($qkeys['qkeys[0].key'])&&isset($qkeys['qkeys[0].target'])){
		$key = (string)urldecode($qkeys['qkeys[0].key']);
		$pid = (int)$_GET["placeId"];
		$scope = (string)urldecode($_GET["scope"]);
		$type = (string)urldecode($_GET["type"]);
		$target = (string)urldecode($qkeys['qkeys[0].target']);
			if($pid == 179997949) {
				$pid = 179997950;
			}

		$stmt = $MainDB->prepare("SELECT * FROM asset_datastore WHERE placeId=:pid AND scope=:scope AND type=:type AND `key`=:key AND target=:target");
		$stmt->bindParam(':key', $key, PDO::PARAM_STR); 
		$stmt->bindParam(':pid', $pid, PDO::PARAM_INT); 
		$stmt->bindParam(':scope', $scope, PDO::PARAM_STR); 
		$stmt->bindParam(':type', $type, PDO::PARAM_STR); 
		$stmt->bindParam(':target', $target, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as &$data){
			array_push($values,array("Value"=>$data["value"],"Scope"=>$data["scope"],"Key"=>$data["key"],"Target"=>$data["target"]));
		}
		$conn=null;
		exit(json_encode(["data"=>$values], JSON_NUMERIC_CHECK));
	}
}
exit(json_encode(["error"=>"well i mean something happened."]));