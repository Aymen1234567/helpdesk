<?php
/**
 * Page de mise à jour du statut d'un ticket
 * Réservée aux tuteurs
 */

require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Vérification que l'utilisateur est un tuteur
requireTuteur();

// Traitement de la mise à jour du statut
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'] ?? '';
    $statut = $_POST['statut'] ?? '';
    
    // Validation des données
    $errors = [];
    
    if (!is_numeric($ticket_id) || $ticket_id <= 0) {
        $errors['ticket_id'] = 'ID de ticket invalide';
    }
    
    $statuts_valides = ['Ouvert', 'En cours', 'Résolu'];
    if (!in_array($statut, $statuts_valides)) {
        $errors['statut'] = 'Statut invalide';
    }
    
    // Si pas d'erreurs, mise à jour du statut
    if (empty($errors)) {
        $ticket_id = (int)$ticket_id;
        
        // Vérification que le ticket existe
        $ticket = getTicket($pdo, $ticket_id);
        if (!$ticket) {
            $errors['general'] = 'Ce ticket n\'existe pas.';
        } else {
            // Mise à jour du statut
            $result = updateTicketStatus($pdo, $ticket_id, $statut);
            
            if ($result) {
                // Redirection vers la page du ticket avec message de succès
                header('Location: ticket.php?id=' . $ticket_id);
                exit;
            } else {
                $errors['general'] = 'Une erreur est survenue lors de la mise à jour du statut.';
            }
        }
    }
    
    // En cas d'erreur, rediriger vers le dashboard avec un message d'erreur
    // Dans une vraie application, on pourrait utiliser des sessions pour transmettre l'erreur
    header('Location: dashboard_tuteur.php');
    exit;
} else {
    // Si la méthode n'est pas POST, rediriger vers le dashboard
    header('Location: dashboard_tuteur.php');
    exit;
}
?>
