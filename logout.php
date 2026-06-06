<?php
/**
 * Page de déconnexion
 * Détruit la session et redirige vers la page de connexion
 */

require_once 'includes/auth.php';

// Déconnexion de l'utilisateur
logout();

// Redirection vers la page de connexion
header('Location: login.php');
exit;
?>
