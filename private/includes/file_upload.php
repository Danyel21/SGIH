<?php

class FileUploadManager {
    private $uploadDir;
    private $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xls', 'xlsx'];
    private $maxFileSize = 10 * 1024 * 1024; // 10MB

    public function __construct($baseDir = null) {
        if ($baseDir === null) {
            $baseDir = __DIR__ . '/../uploads/documentos';
        }
        $this->uploadDir = $baseDir;
        $this->ensureUploadDir();
    }

    private function ensureUploadDir() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function uploadFile($file, $id_equipamento) {
        // Validações
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Ficheiro não enviado corretamente");
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new Exception("Ficheiro excede o tamanho máximo de 10MB");
        }

        // Obter extensão
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            throw new Exception("Tipo de ficheiro não permitido: $ext");
        }

        // Gerar nome único
        $fileName = $id_equipamento . '_' . time() . '_' . md5(random_bytes(16)) . '.' . $ext;
        $filePath = $this->uploadDir . '/' . $fileName;

        // Mover ficheiro
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception("Erro ao salvar ficheiro");
        }

        return [
            'fileName' => $fileName,
            'filePath' => $filePath,
            'extension' => $ext,
            'size' => $file['size'],
            'originalName' => $file['name']
        ];
    }

    public function ensureEquipmentFolders($codigo_interno, array $subfolders = []) {
        $base = rtrim($this->uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $codigo_interno;
        if (!is_dir($base)) {
            mkdir($base, 0755, true);
        }
        $created = [];
        foreach ($subfolders as $sub) {
            $dir = $base . DIRECTORY_SEPARATOR . $sub;
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $created[$sub] = $dir;
        }
        return $created;
    }

    public function uploadFileToDir($file, $destDir) {
        // Validations
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Ficheiro não enviado corretamente");
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new Exception("Ficheiro excede o tamanho máximo de 10MB");
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            throw new Exception("Tipo de ficheiro não permitido: $ext");
        }

        if (!is_dir($destDir)) mkdir($destDir, 0755, true);

        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $filePath = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception("Erro ao salvar ficheiro");
        }

        // return relative path from uploadDir
        $relative = ltrim(str_replace($this->uploadDir, '', $filePath), DIRECTORY_SEPARATOR);

        return [
            'fileName' => $relative,
            'filePath' => $filePath,
            'extension' => $ext,
            'size' => $file['size'],
            'originalName' => $file['name']
        ];
    }

    public function deleteFile($fileName) {
        $filePath = $this->uploadDir . '/' . $fileName;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    public function getUploadUrl($fileName) {
        return '/SGIH/hospital_inventory_php/private/uploads/documentos/' . ltrim($fileName, '/\\');
    }
}

?>
