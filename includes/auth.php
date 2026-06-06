<?php
/**
 * Fonctions d'authentification et de vérification de session
 */

// Démarrage de la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est un étudiant
 * @return bool
 */
function isEtudiant() {
    return isLoggedIn() && $_SESSION['role'] === 'etudiant';
}

/**
 * Vérifie si l'utilisateur est un tuteur
 * @return bool
 */
function isTuteur() {
    return isLoggedIn() && $_SESSION['role'] === 'tuteur';
}

/**
 * Redirige vers la page de connexion si non connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirige si l'utilisateur n'est pas un étudiant
 */
function requireEtudiant() {
    requireLogin();
    if (!isEtudiant()) {
        header('Location: dashboard_tuteur.php');
        exit;
    }
}

/**
 * Redirige si l'utilisateur n'est pas un tuteur
 */
function requireTuteur() {
    requireLogin();
    if (!isTuteur()) {
        header('Location: dashboard_etudiant.php');
        exit;
    }
}

/**
 * Vérifie si un ticket appartient à l'étudiant connecté
 * @param PDO $pdo Connexion à la base de données
 * @param int $ticket_id ID du ticket
 * @return bool
 */
function isTicketOwner(PDO $pdo, $ticket_id) {
    if (!isEtudiant()) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT user_id FROM tickets WHERE id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();
    
    return $ticket && $ticket['user_id'] == $_SESSION['user_id'];
}

/**
 * Authentifie un utilisateur
 * @param PDO $pdo Connexion à la base de données
 * @param string $username Nom d'utilisateur
 * @param string $password Mot de passe
 * @return array|false
 */
function authenticate(PDO $pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Mot de passe correct, création de la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        return $user;
    }
    
    return false;
}

/**
 * Déconnecte l'utilisateur
 */
function logout() {
    // Destruction de toutes les variables de session
    $_SESSION = [];
    
    // Destruction du cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruction de la session
    session_destroy();
}
