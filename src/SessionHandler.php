<?php
namespace Nobanno;

class SessionHandler implements \SessionHandlerInterface {
    private $pdo;
    private $maxlifetime;

    public function __construct($pdo, $maxlifetime = 600) {
        $this->pdo = $pdo;
        $this->maxlifetime = $maxlifetime;
    }

    public function open(string $save_path, string $session_name): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read(string $session_id): string {
        $stmt = $this->pdo->prepare("SELECT data, lastAccess FROM sessions WHERE sessionId = :sessionId");
        $stmt->execute(['sessionId' => $session_id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) return '';
        $lastAccess = strtotime($result['lastAccess']);
        if ((time() - $lastAccess) > $this->maxlifetime) {
            $this->destroy($session_id);
            return '';
        }
        return $result['data'];
    }

    public function write(string $session_id, string $session_data): bool {
        $stmt = $this->pdo->prepare("REPLACE INTO sessions (sessionId, data, lastAccess) VALUES (:sessionId, :data, NOW())");
        return $stmt->execute([
            'sessionId' => $session_id,
            'data' => $session_data
        ]);
    }

    public function destroy(string $session_id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE sessionId = :sessionId");
        return $stmt->execute(['sessionId' => $session_id]);
    }

    public function gc(int $maxlifetime): int|false {
        $cutoff = date('Y-m-d H:i:s', time() - $maxlifetime);
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE lastAccess < :cutoff");
        return $stmt->execute(['cutoff' => $cutoff]) ? $stmt->rowCount() : false;
    }
}
?>

