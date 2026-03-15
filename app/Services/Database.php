<?php
namespace App\Services;

use PDO;
use PDOException;

/**
 * Database service. Manages a single PDO connection instance using the
 * provided configuration. This class implements the singleton pattern so
 * that the same connection can be reused throughout a request.
 */
class Database
{
    /**
     * @var Database|null The singleton instance
     */
    private static ?Database $instance = null;

    /**
     * @var PDO The PDO connection
     */
    private PDO $connection;

    /**
     * Private constructor to prevent external instantiation. Establishes
     * the PDO connection using the provided configuration.
     *
     * @param array $config
     */
    private function __construct(array $config)
    {
        $dbConfig = $config['db'] ?? [];
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['database']
        );
        try {
            $this->connection = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // In a production environment you would log this error and show
            // a generic message to the user instead of echoing the exception.
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve the singleton instance. If it doesn't exist yet, it will
     * be created using the provided configuration. Subsequent calls will
     * ignore the configuration argument and return the existing instance.
     *
     * @param array $config
     * @return Database
     */
    public static function getInstance(array $config): Database
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * Return the underlying PDO connection.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}