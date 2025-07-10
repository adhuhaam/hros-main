<?php
/**
 * SQL Injection Fix Script
 * 
 * This script automatically fixes SQL injection vulnerabilities
 * by replacing direct string concatenation with prepared statements.
 */

require_once 'utils/Database.php';

class SQLInjectionFixer {
    private $db;
    private $fixedFiles = [];
    private $errors = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Fix SQL injection vulnerabilities in all identified files
     */
    public function fixAllVulnerabilities() {
        echo "Starting SQL injection vulnerability fixes...\n\n";
        
        $filesToFix = [
            'accommodation/floors.php' => [
                'line' => 10,
                'pattern' => '/\$conn->query\("SELECT building_name FROM accommodation_buildings WHERE id = \$building_id"\)/',
                'replacement' => '$stmt = $conn->prepare("SELECT building_name FROM accommodation_buildings WHERE id = ?");
                $stmt->bind_param("i", $building_id);
                $stmt->execute();
                $building = $stmt->get_result()->fetch_assoc();
                $stmt->close();'
            ],
            'accommodation/floors.php' => [
                'line' => 11,
                'pattern' => '/\$result = \$conn->query\("SELECT \* FROM accommodation_floors WHERE building_id = \$building_id"\);/',
                'replacement' => '$stmt = $conn->prepare("SELECT * FROM accommodation_floors WHERE building_id = ?");
                $stmt->bind_param("i", $building_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();'
            ],
            'accommodation/rooms.php' => [
                'line' => 17,
                'pattern' => '/\$result = \$conn->query\("SELECT \* FROM accommodation_rooms WHERE floor_id = \$floor_id"\);/',
                'replacement' => '$stmt = $conn->prepare("SELECT * FROM accommodation_rooms WHERE floor_id = ?");
                $stmt->bind_param("i", $floor_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();'
            ],
            'loan/edit_loan.php' => [
                'line' => 6,
                'pattern' => '/\$result = \$conn->query\("SELECT \* FROM salary_loans WHERE id = \$id"\);/',
                'replacement' => '$stmt = $conn->prepare("SELECT * FROM salary_loans WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();'
            ],
            'cards/view_cards.php' => [
                'line' => 11,
                'pattern' => '/\$sql = "SELECT \* FROM card_print WHERE id = \$id";/',
                'replacement' => '$stmt = $conn->prepare("SELECT * FROM card_print WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();'
            ]
        ];
        
        foreach ($filesToFix as $file => $fix) {
            $this->fixFile($file, $fix);
        }
        
        $this->generateReport();
    }
    
    /**
     * Fix a specific file
     */
    private function fixFile($filePath, $fix) {
        if (!file_exists($filePath)) {
            $this->errors[] = "File not found: {$filePath}";
            return;
        }
        
        echo "Fixing: {$filePath}\n";
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Apply the fix
        $content = preg_replace($fix['pattern'], $fix['replacement'], $content);
        
        if ($content !== $originalContent) {
            // Create backup
            $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
            file_put_contents($backupPath, $originalContent);
            
            // Write fixed content
            file_put_contents($filePath, $content);
            
            $this->fixedFiles[] = [
                'file' => $filePath,
                'backup' => $backupPath,
                'line' => $fix['line']
            ];
            
            echo "  ✓ Fixed SQL injection vulnerability on line {$fix['line']}\n";
            echo "  ✓ Backup created: {$backupPath}\n";
        } else {
            echo "  ⚠ No changes made (pattern not found)\n";
        }
        
        echo "\n";
    }
    
    /**
     * Generate a report of all fixes
     */
    private function generateReport() {
        echo "=== SQL Injection Fix Report ===\n\n";
        
        if (empty($this->fixedFiles) && empty($this->errors)) {
            echo "No vulnerabilities found or fixed.\n";
            return;
        }
        
        if (!empty($this->fixedFiles)) {
            echo "Fixed Files:\n";
            foreach ($this->fixedFiles as $fix) {
                echo "  - {$fix['file']} (line {$fix['line']})\n";
                echo "    Backup: {$fix['backup']}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->errors)) {
            echo "Errors:\n";
            foreach ($this->errors as $error) {
                echo "  - {$error}\n";
            }
            echo "\n";
        }
        
        echo "Total files fixed: " . count($this->fixedFiles) . "\n";
        echo "Total errors: " . count($this->errors) . "\n";
    }
    
    /**
     * Create a comprehensive security audit report
     */
    public function generateSecurityAuditReport() {
        $report = "# Security Audit Report\n\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        $report .= "## SQL Injection Vulnerabilities Fixed\n\n";
        
        if (!empty($this->fixedFiles)) {
            $report .= "The following files were fixed:\n\n";
            foreach ($this->fixedFiles as $fix) {
                $report .= "- `{$fix['file']}` (line {$fix['line']})\n";
            }
        } else {
            $report .= "No SQL injection vulnerabilities were found.\n";
        }
        
        $report .= "\n## Recommendations\n\n";
        $report .= "1. **Use Prepared Statements**: Always use prepared statements for database queries\n";
        $report .= "2. **Input Validation**: Validate and sanitize all user inputs\n";
        $report .= "3. **Parameter Binding**: Use parameter binding instead of string concatenation\n";
        $report .= "4. **Regular Audits**: Conduct regular security audits\n";
        $report .= "5. **Code Review**: Implement mandatory code review for database operations\n\n";
        
        $report .= "## Security Best Practices\n\n";
        $report .= "- Use the centralized Database class for all database operations\n";
        $report .= "- Implement proper error handling without exposing sensitive information\n";
        $report .= "- Use environment variables for all configuration\n";
        $report .= "- Implement proper logging for security events\n";
        $report .= "- Regular security testing and penetration testing\n\n";
        
        file_put_contents('security_audit_report.md', $report);
        echo "Security audit report generated: security_audit_report.md\n";
    }
}

// Run the fixer
if (php_sapi_name() === 'cli') {
    $fixer = new SQLInjectionFixer();
    $fixer->fixAllVulnerabilities();
    $fixer->generateSecurityAuditReport();
} else {
    echo "This script should be run from the command line.\n";
}
?>