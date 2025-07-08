<?php
class FileUploader {
    private $allowedTypes;
    private $maxFileSize;
    private $uploadDir;
    private $errors = [];

    public function __construct($uploadDir = '../document/', $maxFileSize = 5242880) { // 5MB default
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = $maxFileSize;
        
        // Define allowed file types with MIME types
        $this->allowedTypes = [
            'cv' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'photo' => ['image/jpeg', 'image/png', 'image/gif'],
            'certificate' => ['application/pdf', 'image/jpeg', 'image/png'],
            'licence' => ['application/pdf', 'image/jpeg', 'image/png'],
            'policereport' => ['application/pdf', 'image/jpeg', 'image/png'],
            'workpermit_card' => ['image/jpeg', 'image/png'],
            'passport' => ['application/pdf', 'image/jpeg', 'image/png'],
            'workpermit_document' => ['application/pdf', 'image/jpeg', 'image/png']
        ];
    }

    public function uploadFile($file, $empNo, $documentType) {
        // Validate input parameters
        if (!$this->validateInput($empNo, $documentType)) {
            return false;
        }

        // Validate file
        if (!$this->validateFile($file, $documentType)) {
            return false;
        }

        // Create secure filename
        $secureFilename = $this->generateSecureFilename($file, $empNo, $documentType);
        $uploadPath = $this->uploadDir . $secureFilename;

        // Ensure upload directory exists and is secure
        if (!$this->ensureUploadDirectory()) {
            return false;
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Set proper permissions
            chmod($uploadPath, 0644);
            
            return [
                'success' => true,
                'filename' => $secureFilename,
                'path' => $uploadPath,
                'original_name' => $file['name']
            ];
        } else {
            $this->errors[] = "Failed to move uploaded file";
            return false;
        }
    }

    private function validateInput($empNo, $documentType) {
        // Validate employee number
        if (empty($empNo) || !preg_match('/^[A-Za-z0-9]+$/', $empNo)) {
            $this->errors[] = "Invalid employee number";
            return false;
        }

        // Validate document type
        if (!array_key_exists($documentType, $this->allowedTypes)) {
            $this->errors[] = "Invalid document type";
            return false;
        }

        return true;
    }

    private function validateFile($file, $documentType) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = "File upload error: " . $file['error'];
            return false;
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $this->errors[] = "File size exceeds maximum allowed size";
            return false;
        }

        // Check file type using MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes[$documentType])) {
            $this->errors[] = "File type not allowed for this document type";
            return false;
        }

        // Additional security check - verify file is actually an uploaded file
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = "Invalid file upload";
            return false;
        }

        return true;
    }

    private function generateSecureFilename($file, $empNo, $documentType) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        
        return sprintf(
            '%s_%s_%s_%s.%s',
            $empNo,
            $documentType,
            $timestamp,
            $randomString,
            $extension
        );
    }

    private function ensureUploadDirectory() {
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                $this->errors[] = "Failed to create upload directory";
                return false;
            }
        }

        // Ensure directory is writable
        if (!is_writable($this->uploadDir)) {
            $this->errors[] = "Upload directory is not writable";
            return false;
        }

        return true;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function deleteFile($filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
?>