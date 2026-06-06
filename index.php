<?php
/**
 * Page d'accueil
 * Redirige l'utilisateur selon son rôle vers le dashboard approprié
 */

require_once 'includes/auth.php';

// Si non connecté, rediriger vers la page de connexion
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Redirection selon le rôle
if (isTuteur()) {
    header('Location: dashboard_tuteur.php');
} else {
    header('Location: dashboard_etudiant.php');
}
exit;
?>
