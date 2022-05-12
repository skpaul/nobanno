<?php
require_once("../Required.php");

require_once("prevent_access_if_not_localhost.php");
$queryString = $_SERVER['QUERY_STRING'];


Required::Logger();
$logger = new Logger(ROOT_DIRECTORY);

$iconName = "happy.png";
if ($logger->hasLogs()) {
    $iconName = "unhappy.png";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Error Logs</title>
    <link rel="shortcut icon" type="image/png" href="images/<?php echo $iconName; ?>" />
    <style>
        a {
            display: flex;
            justify-content: center;
            border: 1px solid #9e9393;
            width: 200px;
            margin: auto;
            border-radius: 4px;
            background-color: gray;
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-family: arial, sans-serif;
            letter-spacing: 0.04245em;
            padding: 10px 0;
        }
    </style>
</head>

<body onload="checkAutoRefreshStatus()">
    <input type="checkbox" name="" id="toggleAutoRefresh" onclick="checkChanged()">Auto Refresh
    <button onClick="window.location.reload();">Manual Refresh</button>

    <?php
    if ($logger->hasLogs()) {
        echo '<a href="clear-logs.php?' . $queryString . '">Clear error logs</a><br><br>';
    }
    ?>

    <?php
    try {
        echo '<div style="margin-top:20px;">';
        $logger->readLogs();
        echo "</div>";
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }

    ?>
    <?php
    if ($logger->hasLogs()) {
        echo '<a href="clear-logs.php?' . $queryString . '">Clear error logs</a><br><br>';
    }
    ?>

    <script>

        function checkAutoRefreshStatus(){
            if (localStorage.getItem("autoRefresh") !== null) {
                document.getElementById("toggleAutoRefresh").checked = true;
                checkChanged();
            }
            else{
                document.getElementById("toggleAutoRefresh").checked = false;
            }
        }

        var autoRefreshTimeout;
        function checkChanged() {
            let toggleAutoRefresh = document.getElementById("toggleAutoRefresh").checked;
            if(toggleAutoRefresh) {
                localStorage.setItem("autoRefresh", true);
                autoRefreshTimeout = setTimeout(function() {
                    window.location.reload(1);
                }, 20000);
            } else {
                clearTimeout(autoRefreshTimeout);
                localStorage.removeItem("autoRefresh");
            }
        }
    </script>

</body>

</html>