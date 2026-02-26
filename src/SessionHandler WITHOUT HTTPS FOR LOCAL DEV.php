<?php
namespace Nobanno;

use SessionHandlerInterface;

class SessionHandler implements SessionHandlerInterface
{
    private $pdo;
    private $maxlifetime;

    public function __construct($pdo, $maxlifetime = 3600)
    {
        $this->pdo = $pdo;
        $this->maxlifetime = $maxlifetime;
    }

    /**
     * Static method to bootstrap the session in one call.
     */
    public static function register(\PDO $pdo, array $options = []): void
    {
        $lifetime = $options['lifetime'] ?? 3600;

        // 1. PHP configuration
        ini_set('session.gc_maxlifetime', (string) $lifetime);
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '100');

        // 2. Cookie settings
        session_set_cookie_params([
            'lifetime' => $options['cookie_lifetime'] ?? 0,
            'path' => $options['path'] ?? '/',
            'domain' => $options['domain'] ?? '', // Pass logic to check for HTTPS if needed
            'secure' => $options['secure'] ?? false,
            'httponly' => $options['httponly'] ?? true,
            'samesite' => $options['samesite'] ?? 'Lax'
        ]);

        // 3. Register this handler
        $handler = new self($pdo, $lifetime);
        session_set_save_handler($handler, true);

        // 4. Start session
        if (session_status() === PHP_SESSION_NONE) {
            if (!session_start()) {
                throw new \RuntimeException("Failed to start session.");
            }
        }

        // 5. Force new session ID only for new visits
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }

    public function open(string $save_path, string $session_name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $session_id): string
    {
        $stmt = $this->pdo->prepare("SELECT data, lastAccess FROM sessions WHERE sessionId = :sessionId");
        $stmt->execute(['sessionId' => $session_id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return '';
        }

        // Parse lastAccess as UTC explicitly to avoid local timezone skew.
        $lastAccessDateTime = \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            (string)$result['lastAccess'],
            new \DateTimeZone('UTC')
        );

        if ($lastAccessDateTime === false) {
            $this->destroy($session_id);
            return '';
        }

        $lastAccessTimestamp = $lastAccessDateTime->getTimestamp();
        if ((time() - $lastAccessTimestamp) > $this->maxlifetime) {
            $this->destroy($session_id);
            return '';
        }

        return (string) $result['data'];
    }

    public function write(string $session_id, string $session_data): bool
    {
        $stmt = $this->pdo->prepare("REPLACE INTO sessions (sessionId, data, lastAccess) VALUES (:sessionId, :data, UTC_TIMESTAMP())");
        return $stmt->execute([
            'sessionId' => $session_id,
            'data' => $session_data
        ]);
    }

    public function destroy(string $session_id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE sessionId = :sessionId");
        return $stmt->execute(['sessionId' => $session_id]);
    }

    public function gc(int $maxlifetime): int|false
    {
        // Use UTC timestamp to match database NOW() which stores UTC
        $cutoff = gmdate('Y-m-d H:i:s', time() - $maxlifetime);
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE lastAccess < :cutoff");
        return $stmt->execute(['cutoff' => $cutoff]) ? $stmt->rowCount() : false;
    }
}
?>

