<?php
namespace Nobanno;
// Prevent re-initialization if the file is included multiple times.
if (session_status() === PHP_SESSION_ACTIVE) {
    return;
}

require_once(__DIR__ . '/SessionHandler.php');

class Session {
    private $pdo;
    private $maxlifetime;
    private $handler;

    public function __construct($pdo, $maxlifetime = 1800) {
        $this->pdo = $pdo;
        $this->maxlifetime = $maxlifetime;
    }

    public function configure() {
        // Set garbage collection probability
        ini_set('session.gc_maxlifetime', $this->maxlifetime);
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 100);

        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Initialize custom session handler
        $this->handler = new SessionHandler($this->pdo, $this->maxlifetime);
        session_set_save_handler($this->handler, true);

        // Start session
        session_start();

        // Force new session ID only for new visits
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }
}

?>
