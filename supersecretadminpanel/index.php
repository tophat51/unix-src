<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/getuser.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/api/webhookstuff.php');
$RBXTICKET = $_COOKIE['ROBLOSECURITY'] ?? null;

switch (true) {
  case ($RBXTICKET == null):
    header("Location: " . $baseUrl . "/");
    die();
    break;
  default:
    if ($admin < 1) {
      header("Location: " . $baseUrl . "/");
      die();
    }
    break;
}

$date = date("l");
$timeOfDay = date('a');
$time = "afternoon";

if ($timeOfDay == "pm") {
  $time = "afternoon";
} else {
  $time = "morning";
}

$currentkey = "";
if (isset($_POST['gen_key'])) {
	switch (true) {
  case ($RBXTICKET == null):
    header("Location: " . $baseUrl . "/");
    die();
    break;
  default:
    if ($admin < 1) {
      header("Location: " . $baseUrl . "/");
      die();
    }
    break;
}


  function CreateKey()
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = 'unixkey-';
    for ($i = 0; $i < 24; $i++) {
      $key .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $key;
  }

  $creatorId = $id;
  $elkey = CreateKey();

  $query = "INSERT INTO regkeys (elkey, used, creatorid) VALUES (:elkey, 0, :creatorid)";
  $statement = $MainDB->prepare($query);
  $statement->bindParam(':elkey', $elkey, PDO::PARAM_STR);
  $statement->bindParam(':creatorid', $creatorId, PDO::PARAM_INT);
  $statement->execute();

  $currentkey = $elkey;
  sendLog("New key(s) generated by ".$name."! (".$currentkey.")");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Index - Admin</title>

  <link rel="stylesheet" href="./index.css?v=<?php echo (rand(1, 50)); ?>">
  <link rel="stylesheet" href="../admindex.css?v=<?php echo (rand(1, 50)); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous" />
</head>

<body>
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admintopbar.php'); ?>
  <div class="main-div-container">
    <h1 class="main-title">Dashboard</h1>
    <p>
      <?php echo "Hello, $name. What would you like to do in this lovely $date $time?" ?>
    </p>
    <div class="admin-dashboard-div">
    <h2 class="admin-subheader">Generate Key</h2>
      <div class="admin-dashboard-div-section">
      <div class="admin-button-div">
        
        <button class="admin-button margin-right" onclick="copyText()">Copy</button>
        <form action="" method="post">
          <button name="gen_key" class="admin-button">Generate Key</button>
        </form>
      </div>
      <input type="text" placeholder="Key will be generated here" value="<?php echo $currentkey; ?>" id="ballsacks"
          class="admin-text-input" disabled>
      </div>

      <h2 class="admin-subheader">All Servers</h2>
      <div class="admin-dashboard-div-section">
        <table class="admin-table">
          <tr class="admin-tr-cell">
            <th class="admin-th-cell-start">Server</th>
            <th class="admin-th-cell">Players</th>
            <th class="admin-th-cell-end">Action</th>
          </tr>

          <?php

          $ActionFetch = $MainDB->prepare("
    SELECT *
    FROM open_servers
    ORDER BY playerCount DESC
");

          $ActionFetch->execute();
          $ActionRows = $ActionFetch->fetchAll();

          foreach ($ActionRows as $GameInfo) {
            $stmt = $MainDB->prepare("SELECT * FROM asset WHERE id = :gameID");
            $stmt->bindParam(':gameID', $GameInfo['gameID'], PDO::PARAM_INT);
            $stmt->execute();
            $resulto = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if the query returned any results
            if (count($resulto) > 0) {
              $playersOnline = intval($GameInfo['playerCount']);
              echo '<tr class="admin-tr-cell">
        <td class="admin-td-cell">' . $resulto[0]['name'] . '</td>
        <td class="admin-td-cell">' . $GameInfo['playerCount'] . '</td>
        <td class="admin-td-cell">
          <form action = "" method = "post">
            <button type="submit" class="admin-table-button">Shutdown</button>
          </form>
          
        </td>
      </tr>';
            } else {

            }
          }

          ?>
        </table>
      </div>
    </div>
  </div>
  <script>
    function copyText() {
      var copyText = document.getElementById("ballsacks");
      copyText.select();
      copyText.setSelectionRange(0, 99999); // For mobile devices

      // Copy the text inside the text field
      navigator.clipboard.writeText(copyText.value);

      // Alert the copied text
      alert("Copied the text: " + copyText.value);
    }
  </script>
</body>

</html>