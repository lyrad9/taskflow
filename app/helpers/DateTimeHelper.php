<?php

/**
 * Classe utilitaire pour le formatage des dates et heures
 */
class DateTimeHelper {
    
    /**
     * Formatte une date en français avec le jour de la semaine
     * 
     * @param string|DateTime $date La date à formater (format Y-m-d ou objet DateTime)
     * @return string La date formatée (ex: Lundi le 13 décembre 2023)
     */
    public static function formatFrenchDate($date) {
        if (!$date) {
            return '';
        }
        
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        
        $joursSemaine = [
            'Monday'    => 'Lundi',
            'Tuesday'   => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday'  => 'Jeudi',
            'Friday'    => 'Vendredi',
            'Saturday'  => 'Samedi',
            'Sunday'    => 'Dimanche'
        ];
        
        $mois = [
            '01' => 'janvier',
            '02' => 'février',
            '03' => 'mars',
            '04' => 'avril',
            '05' => 'mai',
            '06' => 'juin',
            '07' => 'juillet',
            '08' => 'août',
            '09' => 'septembre',
            '10' => 'octobre',
            '11' => 'novembre',
            '12' => 'décembre'
        ];
        
        $jourSemaine = $joursSemaine[$date->format('l')];
        $jour = $date->format('d');
        $jour = ltrim($jour, '0'); // Supprimer le zéro initial
        $moisNum = $date->format('m');
        $moisNom = $mois[$moisNum];
        $annee = $date->format('Y');
        
        return $jourSemaine . ' le ' . $jour . ' ' . $moisNom . ' ' . $annee;
    }
    
    /**
     * Formatte une date au format français court
     * 
     * @param string|DateTime $date La date à formater (format Y-m-d ou objet DateTime)
     * @return string La date formatée (ex: 13/12/2023)
     */
    public static function formatShortDate($date) {
        if (!$date) {
            return '';
        }
        
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        
        return $date->format('d/m/Y');
    }
    
    /**
     * Formatte une date avec heure au format français
     * 
     * @param string|DateTime $date La date à formater (format Y-m-d H:i:s ou objet DateTime)
     * @return string La date formatée (ex: 13/12/2023 à 14:30)
     */
    public static function formatDateTime($date) {
        if (!$date) {
            return '';
        }
        
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        
        return $date->format('d/m/Y') . ' à ' . $date->format('H:i');
    }
    
    /**
     * Formatte une différence de date en texte (il y a X jours)
     * 
     * @param string|DateTime $date La date à comparer avec aujourd'hui
     * @return string La différence en texte
     */
    public static function timeAgo($date) {
        if (!$date) {
            return '';
        }
        
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        
        $now = new DateTime();
        $diff = $now->getTimestamp() - $date->getTimestamp();
        
        if ($diff < 60) {
            return "À l'instant";
        } elseif ($diff < 3600) {
            $minutes = round($diff / 60);
            return "Il y a " . $minutes . " minute" . ($minutes > 1 ? 's' : '');
        } elseif ($diff < 86400) {
            $hours = round($diff / 3600);
            return "Il y a " . $hours . " heure" . ($hours > 1 ? 's' : '');
        } elseif ($diff < 2592000) {
            $days = round($diff / 86400);
            return "Il y a " . $days . " jour" . ($days > 1 ? 's' : '');
        } elseif ($diff < 31536000) {
            $months = round($diff / 2592000);
            return "Il y a " . $months . " mois";
        } else {
            $years = round($diff / 31536000);
            return "Il y a " . $years . " an" . ($years > 1 ? 's' : '');
        }
    }
    
    /**
     * Vérifie si une date est dans le futur
     * 
     * @param string|DateTime $date La date à vérifier
     * @return bool True si la date est dans le futur
     */
    public static function isFutureDate($date) {
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        
        $now = new DateTime();
        return $date > $now;
    }
    
    /**
     * Vérifie si une date de fin est postérieure à une date de début
     * 
     * @param string|DateTime $startDate La date de début
     * @param string|DateTime $endDate La date de fin
     * @return bool True si la date de fin est après la date de début
     */
    public static function isValidDateRange($startDate, $endDate) {
        if (is_string($startDate)) {
            $startDate = new DateTime($startDate);
        }
        
        if (is_string($endDate)) {
            $endDate = new DateTime($endDate);
        }
        
        return $endDate >= $startDate;
    }
} 