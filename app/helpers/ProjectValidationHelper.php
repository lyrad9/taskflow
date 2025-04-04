<?php

/**
 * Classe utilitaire pour valider les données d'un projet
 */
class ProjectValidationHelper {
    
    /**
     * Taille maximum totale des documents (7MB)
     */
    private static $maxTotalFileSize = 7340032; // 7MB en octets
    
    /**
     * Extensions autorisées pour les documents
     */
    private static $allowedExtensions = ['pdf'];
    
    /**
     * Valide les données d'un projet
     * 
     * @param array $data Données du projet
     * @param array $files Fichiers téléchargés (tableau $_FILES)
     * @return array Résultat de la validation avec erreurs éventuelles
     */
    public static function validateProject($data, $files = null) {
        $errors = [];
        
        // Validation des champs obligatoires
        if (empty($data['name'])) {
            $errors[] = "Le nom du projet est obligatoire";
        }
        
        if (empty($data['description'])) {
            $errors[] = "La description du projet est obligatoire";
        }
        
        if (empty($data['project_type'])) {
            $errors[] = "Le type de projet est obligatoire";
        }
        
        // Validation du budget
        if (empty($data['budget'])) {
            $errors[] = "Le budget du projet est obligatoire";
        } elseif (!is_numeric($data['budget'])) {
            $errors[] = "Le budget doit être un nombre";
        } elseif (floatval($data['budget']) <= 0) {
            $errors[] = "Le budget doit être supérieur à zéro";
        }
        
        // Validation des dates
        if (empty($data['scheduled_start_date'])) {
            $errors[] = "La date de début prévue est obligatoire";
        }
        
        if (empty($data['scheduled_end_date'])) {
            $errors[] = "La date de fin prévue est obligatoire";
        }
        
        // Vérification que la date de fin est postérieure à la date de début
        if (!empty($data['scheduled_start_date']) && !empty($data['scheduled_end_date'])) {
            $start_date = new DateTime($data['scheduled_start_date']);
            $end_date = new DateTime($data['scheduled_end_date']);
            
            if ($end_date < $start_date) {
                $errors[] = "La date de fin prévue doit être postérieure à la date de début prévue";
            }
        }
        
        // Validation du client
        if (isset($data['client_tab']) && $data['client_tab'] === 'new') {
            // Si un nouveau client est créé, vérifier les champs obligatoires
            if (empty($data['client_first_name'])) {
                $errors[] = "Le prénom du client est obligatoire";
            }
            
            if (empty($data['client_last_name'])) {
                $errors[] = "Le nom du client est obligatoire";
            }
            
            if (empty($data['client_city'])) {
                $errors[] = "La ville du client est obligatoire";
            }
            
            if (empty($data['client_phone_number'])) {
                $errors[] = "Le numéro de téléphone du client est obligatoire";
            }
        } else {
            // Si un client existant est sélectionné, vérifier l'ID
            if (empty($data['client_id'])) {
                $errors[] = "Vous devez sélectionner un client existant";
            }
        }
        
      /*   // Validation de l'équipe
        if (empty($data['team_id'])) {
            $errors[] = "Vous devez sélectionner une équipe";
        }
         */
        // Validation des documents
        if ($files && isset($files['documents'])) {
            // Vérifier qu'au moins un document est fourni
            if (!isset($files['documents']['name'][0]) || empty($files['documents']['name'][0])) {
                $errors[] = "Vous devez fournir au moins un document";
            } else {
                // Vérifier le nombre maximum de documents (3)
                if (count($files['documents']['name']) > 3) {
                    $errors[] = "Vous ne pouvez pas télécharger plus de 3 documents";
                }
                
                // Vérifier la taille totale
                $totalSize = 0;
                for ($i = 0; $i < count($files['documents']['name']); $i++) {
                    $totalSize += $files['documents']['size'][$i];
                    
                    // Vérifier l'extension
                    $extension = strtolower(pathinfo($files['documents']['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($extension, self::$allowedExtensions)) {
                        $errors[] = "Seuls les fichiers PDF sont autorisés";
                        break;
                    }
                }
                
                // Vérifier la taille totale
                if ($totalSize > self::$maxTotalFileSize) {
                    $errors[] = "La taille totale des documents ne doit pas dépasser 7 Mo";
                }
            }
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Valide les données d'un client
     * 
     * @param array $data Données du client
     * @return array Résultat de la validation avec erreurs éventuelles
     */
    public static function validateClient($data) {
        $errors = [];
        
        // Validation des champs obligatoires
        if (empty($data['first_name'])) {
            $errors[] = "Le prénom du client est obligatoire";
        }
        
        if (empty($data['last_name'])) {
            $errors[] = "Le nom du client est obligatoire";
        }
        
        if (empty($data['city'])) {
            $errors[] = "La ville du client est obligatoire";
        }
        
        if (empty($data['phone_number'])) {
            $errors[] = "Le numéro de téléphone du client est obligatoire";
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }
} 