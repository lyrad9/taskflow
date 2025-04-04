<?php

/**
 * Classe utilitaire pour gérer l'upload de fichiers
 */
class FileUploadHelper {
    
    /**
     * Dossier par défaut pour l'upload des fichiers
     */
    private static $defaultUploadDir = '/public/uploads/';
    
    /**
     * Extensions de fichiers autorisées par défaut
     */
    private static $defaultAllowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
    
    /**
     * Taille maximum de fichier par défaut (10MB)
     */
    private static $defaultMaxFileSize = 10485760; // 10MB en octets
    
    /**
     * Télécharge un fichier depuis un formulaire
     * 
     * @param array $file Tableau $_FILES['nom_du_champ']
     * @param string $uploadDir Dossier de destination (relatif à la racine du projet)
     * @param array $allowedExtensions Extensions autorisées
     * @param int $maxFileSize Taille maximum en octets
     * @return array|bool Informations sur le fichier uploadé ou false si erreur
     */
    public static function uploadFile($file, $uploadDir = null, $allowedExtensions = null, $maxFileSize = null) {
        // Vérification des paramètres
        if ($uploadDir === null) {
            $uploadDir = self::$defaultUploadDir;
        }
        
        if ($allowedExtensions === null) {
            $allowedExtensions = self::$defaultAllowedExtensions;
        }
        
        if ($maxFileSize === null) {
            $maxFileSize = self::$defaultMaxFileSize;
        }
        
        // Vérification du fichier
        if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Erreur lors du téléchargement du fichier'];
        }
        
        // Vérification de la taille du fichier
        if ($file['size'] > $maxFileSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux (max ' . self::formatFileSize($maxFileSize) . ')'];
        }
        
        // Vérification de l'extension
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        
        if (!in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'error' => 'Extension de fichier non autorisée'];
        }
        
        // Création du dossier de destination s'il n'existe pas
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'error' => 'Impossible de créer le dossier de destination'];
            }
        }
        
        // Génération d'un nom de fichier unique pour éviter les écrasements
        $filename = self::generateUniqueFilename($fileInfo['filename'], $extension);
        $destination = $uploadDir . '/' . $filename;
        
        // Téléchargement du fichier
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Erreur lors du déplacement du fichier'];
        }
        
        return [
            'success' => true,
            'original_name' => $file['name'],
            'filename' => $filename,
            'filepath' => $destination,
            'extension' => $extension,
            'filesize' => $file['size'],
            'mime_type' => $file['type']
        ];
    }
    
    /**
     * Télécharge plusieurs fichiers depuis un formulaire
     * 
     * @param array $files Tableau $_FILES['nom_du_champ'] avec attribut 'name' sous forme de tableau
     * @param string $uploadDir Dossier de destination
     * @param array $allowedExtensions Extensions autorisées
     * @param int $maxFileSize Taille maximum en octets
     * @return array Résultats des uploads
     */
    public static function uploadMultipleFiles($files, $uploadDir = null, $allowedExtensions = null, $maxFileSize = null) {
        $results = [];
        
        // Vérifier si c'est un tableau multidimensionnel classique comme $_FILES
        if (isset($files['name']) && is_array($files['name'])) {
            foreach ($files['name'] as $key => $value) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];
                
                $results[] = self::uploadFile($file, $uploadDir, $allowedExtensions, $maxFileSize);
            }
        } 
        // Si c'est un tableau d'éléments $_FILES individuels
        else {
            foreach ($files as $file) {
                $results[] = self::uploadFile($file, $uploadDir, $allowedExtensions, $maxFileSize);
            }
        }
        
        return $results;
    }
    
    /**
     * Supprime un fichier téléchargé
     * 
     * @param string $filepath Chemin du fichier à supprimer
     * @return bool Résultat de la suppression
     */
    public static function deleteFile($filepath) {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    /**
     * Génère un nom de fichier unique
     * 
     * @param string $baseFilename Nom du fichier original (sans extension)
     * @param string $extension Extension du fichier
     * @return string Nom de fichier unique
     */
    private static function generateUniqueFilename($baseFilename, $extension) {
        // Normaliser le nom pour enlever les caractères spéciaux et les accents
        $baseFilename = preg_replace('/[^a-zA-Z0-9]/', '_', $baseFilename);
        $baseFilename = strtolower($baseFilename);
        
        // Ajouter un timestamp et un hash aléatoire pour l'unicité
        $timestamp = time();
        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        
        return $baseFilename . '_' . $timestamp . '_' . $randomString . '.' . $extension;
    }
    
    /**
     * Formatte la taille d'un fichier en unités lisibles (KB, MB, etc.)
     * 
     * @param int $bytes Taille en octets
     * @return string Taille formatée
     */
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Obtient l'extension d'un fichier à partir de son nom
     * 
     * @param string $filename Nom du fichier
     * @return string Extension du fichier
     */
    public static function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
    
    /**
     * Vérifie si un fichier est une image
     * 
     * @param string $filepath Chemin du fichier
     * @return bool True si c'est une image
     */
    public static function isImage($filepath) {
        // Vérifier l'extension 
        $extension = self::getFileExtension($filepath);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        
        return in_array($extension, $imageExtensions);
    }
} 