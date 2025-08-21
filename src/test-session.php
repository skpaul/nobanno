<?php
require_once __DIR__ . '/ExPDO.php';
require_once __DIR__ . '/Session.php';

use Nobanno\ExPDO;
use Nobanno\Session;

$db = new ExPDO("localhost", "ntrca_cycle6", "root", "");
$session = new Session($db);
$session->configure();


// Test session variable
if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = 'Hello, session world!';
    echo "Session variable set. Refresh to see it persist.";
} else {
    echo "Session variable: ", $_SESSION['test'];
}
?>
