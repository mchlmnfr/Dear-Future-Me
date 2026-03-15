<?php
namespace App\Core;

use App\Services\Database;

/**
 * Base model class. All models should extend this class. It establishes
 * a database connection via the Database service and exposes it to
 * subclasses. Models can then perform queries on the PDO instance.
 */
abstract class Model
{
    /**
     * @var \PDO The PDO connection
     */
    protected \PDO $db;

    /**
     * Constructor. Retrieves the PDO connection from the Database service.
     *
     * @param array $config Configuration array loaded from config.php
     */
    public function __construct(array $config)
    {
        $this->db = Database::getInstance($config)->getConnection();
    }
}