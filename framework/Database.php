<?php

namespace Framework;

use PDO;
use PDOException;

/**
 * Class Database
 * 
 * A simple PDO-based database wrapper for MySQL connections.
 * 
 * Features:
 * - Lazy connection (connects only when needed)
 * - Automatic DSN setup for MySQL
 * - Prepared statement support
 */
class Database {
    /**
     * @var PDO|null The PDO connection instance
     */
    private $connection = null;

    /**
     * Establishes a PDO connection to the database.
     * 
     * @param array $config Database configuration:
     *                      [
     *                          'driver'   => 'mysql',
     *                          'host'     => 'localhost',
     *                          'dbname'   => 'mydb',
     *                          'username' => 'user',
     *                          'password' => 'pass',
     *                          'charset'  => 'utf8'
     *                      ]
     */
    public function connect(array $config): void {
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $username = $config['username'];
        $password = $config['password'];

        try {
            // Create PDO connection
            $this->connection = new PDO($dsn, $username, $password);
            // Set error mode to exceptions
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set default fetch mode to associative array
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Connection failed — output error message
            echo 'Connection failed: ' . $e->getMessage();
            exit; // Stop execution if connection fails
        }
    }

    /**
     * Returns the PDO connection instance.
     * 
     * Creates the connection if it doesn't exist yet (lazy loading).
     * 
     * @param array $config Database configuration
     * @return PDO The PDO connection
     */
    public function getConnection(array $config): PDO {
        if ($this->connection === null) {
            $this->connect($config);
        }
       return $this->connection;
    }

    /**
     * Closes the database connection by setting it to null.
     */
    public function closeConnection(): void {
        $this->connection = null;
    }

    /**
     * Executes a prepared SQL query with optional parameters.
     * 
     * @param string $sql    The SQL statement with placeholders
     * @param array  $params Optional parameters for the statement
     * 
     * @return PDOStatement The executed statement object
     */
    public function query(string $sql, array $params = []): \PDOStatement {
        if (!$this->connection) {
            throw new \Exception("Database connection not initialized");
        }
        $stmt = $this->connection->prepare($sql); // Pass your config here if needed
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Executes a prepared SQL query with named or positional bindings.
     * 
     * Example:
     *   $db->bindQuery("SELECT * FROM users WHERE id = :id", [':id' => 1]);
     * 
     * @param string $sql    The SQL statement
     * @param array  $params Key-value pairs of parameters
     * @return PDOStatement
     */
    public function bindQuery(string $sql, array $params = []): \PDOStatement {
        if (!$this->connection) {
            throw new \Exception("Database connection not initialized");
        }

        $stmt = $this->connection->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

}
