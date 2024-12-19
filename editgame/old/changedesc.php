<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/getuser.php');

switch (true) {
  case ($RBXTICKET == null):
    die(header("Location: " . $baseUrl . "/"));
    break;
}

$id = $_POST["gameid"] ?? die("enter a game id");

$getgameinfo = $MainDB->prepare("SELECT * FROM asset WHERE id = :id AND itemtype = 'Place'");
$getgameinfo->bindParam(":id", $id, PDO::PARAM_INT);
$getgameinfo->execute();
$results = $getgameinfo->fetch(PDO::FETCH_ASSOC);

if ($results["creatorname"] !== $name) {
    die("you dont own this game");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['desc'])) {
        if (preg_match('/<script\b[^>]*>(.*?)<\/script>/i', $_POST['desc'])) {
            die(header("Location: {$baseUrl}/media/videos/haha.mp4"));
        }
          // set the time var for creation dates
          $time = date("d/m/Y");
          // set the sql query
          $query = "
          UPDATE asset
          SET moreinfo = :newdesc, updatedon = :date
          WHERE id = :id 
          AND itemtype = 'Place';
          ";
          $stmt = $MainDB->prepare($query);
          /*$stmt->bindParam(":newdesc", $_POST['desc'], PDO::PARAM_STR);
          $stmt->bindParam(":date", $date, PDO::PARAM_STR);*/
          $stmt->execute([":newdesc" => $_POST["desc"], ":date" => $time, ":id" => $id]);
          header("Location: {$baseUrl}/editgame/?id={$id}&text=changes%20saved%20successfully!");
    } else {
        die("nop");
    }
} else {
    die("nope");
}

?>