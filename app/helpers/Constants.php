<?php

/**
 * Constantes pour l'application
 */
class Constants {
    
    /**
     * Constantes pour les statuts des projets
     */
    const PROJECT_STATUS = [
        'IN_PROGRESS' => 'In progress',
        'COMPLETED' => 'Completed',
        'CANCELLED' => 'Cancelled',        
        'DELAYED' => 'Delayed',
       
    ];
    
    /**
     * Constantes pour les statuts des tâches
     */
    const TASK_STATUS = [
        'TODO' => 'To do',
        'IN_PROGRESS' => 'In progress',
        'COMPLETED' => 'Completed',
        'DELAYED' => 'Delayed',
        'CANCELLED' => 'Cancelled'
    ];
    
    /**
     * Constantes pour les priorités des tâches
     */
    const TASK_PRIORITY = [
        'LOW' => 'low',
        'MEDIUM' => 'medium',
        'HIGH' => 'high',
        'IMMEDIATE' => 'immediate'
    ];
    
    /**
     * Constantes pour les types de projets
     */
    const PROJECT_TYPES = [
        'WEB_DEV' => 'Développement web',
        'MOBILE_DEV' => 'Développement mobile',
        'DESKTOP_DEV' => 'Développement desktop',
        'DESIGN' => 'Design',
        'MARKETING' => 'Marketing',
        'CONSULTING' => 'Consulting',
        'MAINTENANCE' => 'Maintenance',
        'TRAINING' => 'Formation',
        'OTHER' => 'Autre'
    ];
    
    /**
     * Constantes pour les rôles utilisateur
     */
    const USER_ROLES = [
        'SUPER_ADMIN' => 'SUPER_ADMIN',
        'ADMIN' => 'ADMIN',
        'USER' => 'USER'
    ];
    
    /**
     * Constantes pour les classes CSS des statuts de projet
     */
    const PROJECT_STATUS_CLASSES = [
        'In progress' => 'in-progress',
        'Completed' => 'completed',
        'Cancelled' => 'cancelled',       
        'Delayed' => 'delayed',
        
    ];
    
    /**
     * Constantes pour les classes CSS des statuts de tâche
     */
    const TASK_STATUS_CLASSES = [
        'To do' => 'to-do',
        'In progress' => 'in-progress',
        'Completed' => 'completed',
        'Delayed' => 'delayed',
        'Cancelled' => 'cancelled'
    ];
    
    /**
     * Constantes pour les classes CSS des priorités de tâche
     */
    const TASK_PRIORITY_CLASSES = [
        'low' => 'low',
        'medium' => 'medium',
        'high' => 'high',
        'immediate' => 'immediate'
    ];
    
    /**
     * Retourne la classe CSS correspondant au statut du projet
     * 
     * @param string $status Le statut du projet
     * @return string La classe CSS correspondante
     */
    public static function getProjectStatusClass($status) {
        return self::PROJECT_STATUS_CLASSES[$status] ?? 'bg-secondary';
    }
    
    /**
     * Retourne la classe CSS correspondant au statut de la tâche
     * 
     * @param string $status Le statut de la tâche
     * @return string La classe CSS correspondante
     */
    public static function getTaskStatusClass($status) {
        return self::TASK_STATUS_CLASSES[$status] ?? 'bg-secondary';
    }
    
    /**
     * Retourne la classe CSS correspondant à la priorité de la tâche
     * 
     * @param string $priority La priorité de la tâche
     * @return string La classe CSS correspondante
     */
    public static function getTaskPriorityClass($priority) {
        return self::TASK_PRIORITY_CLASSES[$priority] ?? 'bg-secondary';
    }
   
} 