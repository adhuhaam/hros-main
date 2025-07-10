<?php
class Security {
    
    /**
     * Sanitize and validate input
     */
    public static function sanitizeInput($input, $type = 'string') {
        if (empty($input)) {
            return null;
        }
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate employee number format
     */
    public static function validateEmployeeNumber($empNo) {
        return preg_match('/^[A-Za-z0-9]{3,20}$/', $empNo);
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     */
    public static function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        $errors = [];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "File upload error occurred";
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = "File size exceeds maximum allowed size";
        }
        
        // Check file type if specified
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = "File type not allowed";
            }
        }
        
        // Verify it's actually an uploaded file
        if (!is_uploaded_file($file['tmp_name'])) {
            $errors[] = "Invalid file upload";
        }
        
        return $errors;
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? 'guest',
            'event' => $event,
            'details' => $details
        ];
        
        error_log('SECURITY: ' . json_encode($logEntry));
    }
    
    /**
     * Rate limiting check
     */
    public static function checkRateLimit($action, $maxAttempts = 5, $timeWindow = 300) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = "rate_limit_{$action}_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $timeWindow];
        }
        
        // Reset if time window has passed
        if (time() > $_SESSION[$key]['reset_time']) {
            $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $timeWindow];
        }
        
        // Check if limit exceeded
        if ($_SESSION[$key]['count'] >= $maxAttempts) {
            return false;
        }
        
        // Increment counter
        $_SESSION[$key]['count']++;
        return true;
    }
    
    /**
     * Sanitize SQL query parameters
     */
    public static function sanitizeSQL($conn, $value) {
        if (is_array($value)) {
            return array_map(function($item) use ($conn) {
                return self::sanitizeSQL($conn, $item);
            }, $value);
        }
        
        return $conn->real_escape_string($value);
    }
}
?>