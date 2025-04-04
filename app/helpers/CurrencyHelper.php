<?php

/**
 * Classe utilitaire pour le formatage des montants et devises
 */
class CurrencyHelper {
    
    /**
     * Formatte un montant avec séparateur de milliers 
     * au format français (espace comme séparateur de milliers)
     * 
     * @param float $amount Le montant à formater
     * @param string $currency La devise à afficher (par défaut: FCFA)
     * @param int $decimals Le nombre de décimales à afficher
     * @return string Le montant formaté (ex: 80 000 FCFA)
     */
    public static function formatAmount($amount, $currency = 'FCFA', $decimals = 0) {
        if ($amount === null || $amount === '') {
            return '';
        }
        
        // Formattage du nombre avec des espaces comme séparateurs de milliers
        $formattedAmount = number_format($amount, $decimals, ',', ' ');
        
        return $formattedAmount . ' ' . $currency;
    }
    
    /**
     * Formatte un montant au format international 
     * avec le symbole de devise (€, $, etc.)
     * 
     * @param float $amount Le montant à formater
     * @param string $currency Le code de la devise (EUR, USD, etc.)
     * @param string $locale La locale à utiliser (fr_FR, en_US, etc.)
     * @return string Le montant formaté
     */
    public static function formatInternational($amount, $currency = 'EUR', $locale = 'fr_FR') {
        if ($amount === null || $amount === '') {
            return '';
        }
        
        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($amount, $currency);
    }
    
    /**
     * Convertit un montant d'une devise à une autre
     * 
     * @param float $amount Le montant à convertir
     * @param string $fromCurrency La devise source
     * @param string $toCurrency La devise cible
     * @param array $rates Tableau associatif des taux de change
     * @return float Le montant converti
     */
    public static function convert($amount, $fromCurrency, $toCurrency, $rates) {
        if (!isset($rates[$fromCurrency]) || !isset($rates[$toCurrency])) {
            return $amount; // Impossible de convertir
        }
        
        // Conversion via une devise pivot (généralement USD ou EUR)
        $inUSD = $amount / $rates[$fromCurrency];
        return $inUSD * $rates[$toCurrency];
    }
    
    /**
     * Formatte un budget avec code couleur selon le montant
     * 
     * @param float $amount Le montant à formater
     * @param float $threshold1 Premier seuil (montant faible)
     * @param float $threshold2 Deuxième seuil (montant moyen)
     * @return string HTML formaté avec classe de couleur
     */
    public static function formatColoredBudget($amount, $threshold1 = 10000, $threshold2 = 50000) {
        $formatted = self::formatAmount($amount);
        
        if ($amount < $threshold1) {
            return '<span class="text-success">' . $formatted . '</span>';
        } elseif ($amount < $threshold2) {
            return '<span class="text-warning">' . $formatted . '</span>';
        } else {
            return '<span class="text-danger">' . $formatted . '</span>';
        }
    }
    
    /**
     * Calcule le pourcentage d'un montant par rapport à un total
     * 
     * @param float $amount Le montant
     * @param float $total Le total
     * @param int $decimals Le nombre de décimales
     * @return string Le pourcentage formaté
     */
    public static function calculatePercentage($amount, $total, $decimals = 0) {
        if ($total == 0) {
            return '0%';
        }
        
        $percentage = ($amount / $total) * 100;
        return number_format($percentage, $decimals, ',', ' ') . '%';
    }
} 