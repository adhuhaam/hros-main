<?php
/**
 * Centralized Database Connection Class
 * 
 * This class provides a unified interface for database connections
 * with proper error handling, connection pooling, and security features.
 */

class Database {
    private static $instance = null;
    private $connections = [];
    private $configs = [];
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->loadConfigurations();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load database configurations from environment variables
     */
    private function loadConfigurations() {
        // Main HRMS database configuration
        $this->configs['main'] = [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'username' => $_ENV['DB_USERNAME'] ?? 'rccmgvfd_hros_user',
            'password' => $_ENV['DB_PASSWORD'] ?? 'Ompl@65482*',
            'database' => $_ENV['DB_NAME'] ?? 'rccmgvfd_hros',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'port' => $_ENV['DB_PORT'] ?? 3306
        ];
        
        // Recruit database configuration
        $this->configs['recruit'] = [
            'host' => $_ENV['RECRUIT_DB_HOST'] ?? 'localhost',
            'username' => $_ENV['RECRUIT_DB_USERNAME'] ?? 'rccmgvfd_recruit_user',
            'password' => $_ENV['RECRUIT_DB_PASSWORD'] ?? 'Ompl@65482*',
            'database' => $_ENV['RECRUIT_DB_NAME'] ?? 'rccmgvfd_recruit',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'port' => $_ENV['RECRUIT_DB_PORT'] ?? 3306
        ];
    }
    
    /**
     * Get database connection
     * 
     * @param string $connection_name Connection name (main, recruit, etc.)
     * @return mysqli Database connection
     * @throws Exception If connection fails
     */
    public function getConnection($connection_name = 'main') {
        if (!isset($this->configs[$connection_name])) {
            throw new Exception("Unknown database connection: {$connection_name}");
        }
        
        // Return existing connection if available
        if (isset($this->connections[$connection_name]) && 
            $this->connections[$connection_name] instanceof mysqli &&
            !$this->connections[$connection_name]->connect_error) {
            return $this->connections[$connection_name];
        }
        
        // Create new connection
        $config = $this->configs[$connection_name];
        
        try {
            $connection = new mysqli(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database'],
                $config['port']
            );
            
            if ($connection->connect_error) {
                error_log("Database connection failed for {$connection_name}: " . $connection->connect_error);
                throw new Exception("Database connection failed");
            }
            
            // Set charset
            $connection->set_charset($config['charset']);
            
            // Set timezone
            $connection->query("SET time_zone = '+05:00'");
            
            // Store connection
            $this->connections[$connection_name] = $connection;
            
            return $connection;
            
        } catch (Exception $e) {
            error_log("Database connection error for {$connection_name}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Execute a prepared statement
     * 
     * @param string $connection_name Connection name
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @param string $types Parameter types (i, d, s, b)
     * @return mysqli_result|bool Query result
     */
    public function executePrepared($connection_name, $sql, $params = [], $types = '') {
        $connection = $this->getConnection($connection_name);
        
        try {
            $stmt = $connection->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $connection->error);
            }
            
            if (!empty($params)) {
                if (empty($types)) {
                    $types = str_repeat('s', count($params));
                }
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Prepared statement error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Execute a query with automatic parameter sanitization
     * 
     * @param string $connection_name Connection name
     * @param string $sql SQL query
     * @param array $params Parameters to sanitize
     * @return mysqli_result|bool Query result
     */
    public function executeQuery($connection_name, $sql, $params = []) {
        $connection = $this->getConnection($connection_name);
        
        // Sanitize parameters
        foreach ($params as $key => $value) {
            $sanitized_value = $connection->real_escape_string($value);
            $sql = str_replace(":{$key}", "'{$sanitized_value}'", $sql);
        }
        
        $result = $connection->query($sql);
        
        if (!$result) {
            throw new Exception("Query execution failed: " . $connection->error);
        }
        
        return $result;
    }
    
    /**
     * Begin a transaction
     * 
     * @param string $connection_name Connection name
     * @return bool Success status
     */
    public function beginTransaction($connection_name = 'main') {
        $connection = $this->getConnection($connection_name);
        return $connection->begin_transaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @param string $connection_name Connection name
     * @return bool Success status
     */
    public function commit($connection_name = 'main') {
        $connection = $this->getConnection($connection_name);
        return $connection->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @param string $connection_name Connection name
     * @return bool Success status
     */
    public function rollback($connection_name = 'main') {
        $connection = $this->getConnection($connection_name);
        return $connection->rollback();
    }
    
    /**
     * Close all database connections
     */
    public function closeConnections() {
        foreach ($this->connections as $connection) {
            if ($connection instanceof mysqli) {
                $connection->close();
            }
        }
        $this->connections = [];
    }
    
    /**
     * Get connection status
     * 
     * @param string $connection_name Connection name
     * @return array Connection status information
     */
    public function getConnectionStatus($connection_name = 'main') {
        if (!isset($this->connections[$connection_name])) {
            return ['status' => 'not_connected'];
        }
        
        $connection = $this->connections[$connection_name];
        
        return [
            'status' => $connection->connect_error ? 'error' : 'connected',
            'host' => $connection->host_info,
            'server_version' => $connection->server_info,
            'client_version' => $connection->client_info,
            'error' => $connection->connect_error
        ];
    }
    
    /**
     * Test database connectivity
     * 
     * @param string $connection_name Connection name
     * @return bool Connection test result
     */
    public function testConnection($connection_name = 'main') {
        try {
            $connection = $this->getConnection($connection_name);
            $result = $connection->query("SELECT 1");
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get database statistics
     * 
     * @param string $connection_name Connection name
     * @return array Database statistics
     */
    public function getDatabaseStats($connection_name = 'main') {
        $connection = $this->getConnection($connection_name);
        
        $stats = [];
        
        // Get table sizes
        $result = $connection->query("
            SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
        ");
        
        if ($result) {
            $stats['table_sizes'] = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get connection info
        $stats['connection_info'] = $this->getConnectionStatus($connection_name);
        
        return $stats;
    }
    
    /**
     * Destructor to ensure connections are closed
     */
    public function __destruct() {
        $this->closeConnections();
    }
}

// Global database instance for backward compatibility
if (!function_exists('getDB')) {
    function getDB($connection_name = 'main') {
        return Database::getInstance()->getConnection($connection_name);
    }
}

// Global database instance for backward compatibility
if (!function_exists('executeQuery')) {
    function executeQuery($connection_name, $sql, $params = []) {
        return Database::getInstance()->executeQuery($connection_name, $sql, $params);
    }
}

// Global database instance for backward compatibility
if (!function_exists('executePrepared')) {
    function executePrepared($connection_name, $sql, $params = [], $types = '') {
        return Database::getInstance()->executePrepared($connection_name, $sql, $params, $types);
    }
}
?>