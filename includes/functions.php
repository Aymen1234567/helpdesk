<?php
/**
 * Fonctions utilitaires pour l'application Helpdesk
 */

/**
 * Formate une date en français
 * @param string $date Date au format MySQL
 * @return string Date formatée
 */
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Retourne le badge CSS pour un statut
 * @param string $statut Statut du ticket
 * @return string Classe CSS
 */
function getStatutBadge($statut) {
    switch ($statut) {
        case 'Ouvert':
            return 'badge-ouvert';
        case 'En cours':
            return 'badge-encours';
        case 'Résolu':
            return 'badge-resolu';
        default:
            return 'badge-default';
    }
}

/**
 * Retourne le badge CSS pour une priorité
 * @param string $priorite Priorité du ticket
 * @return string Classe CSS
 */
function getPrioriteBadge($priorite) {
    switch ($priorite) {
        case 'Basse':
            return 'badge-basse';
        case 'Moyenne':
            return 'badge-moyenne';
        case 'Haute':
            return 'badge-haute';
        default:
            return 'badge-default';
    }
}

/**
 * Nettoie une chaîne de caractères pour l'affichage
 * @param string $string Chaîne à nettoyer
 * @return string Chaîne nettoyée
 */
function clean($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

/**
 * Valide un formulaire de ticket
 * @param array $data Données du formulaire
 * @return array Erreurs trouvées
 */
function validateTicket($data) {
    $errors = [];
    
    if (empty($data['titre'])) {
        $errors['titre'] = 'Le titre est obligatoire';
    } elseif (strlen($data['titre']) > 255) {
        $errors['titre'] = 'Le titre ne doit pas dépasser 255 caractères';
    }
    
    if (empty($data['description'])) {
        $errors['description'] = 'La description est obligatoire';
    } elseif (strlen($data['description']) < 10) {
        $errors['description'] = 'La description doit contenir au moins 10 caractères';
    }
    
    if (empty($data['categorie']) || !in_array($data['categorie'], ['Cours', 'TD', 'TP'])) {
        $errors['categorie'] = 'La catégorie est invalide';
    }
    
    if (empty($data['priorite']) || !in_array($data['priorite'], ['Basse', 'Moyenne', 'Haute'])) {
        $errors['priorite'] = 'La priorité est invalide';
    }
    
    return $errors;
}

/**
 * Valide un formulaire de commentaire
 * @param string $message Message du commentaire
 * @return array Erreurs trouvées
 */
function validateCommentaire($message) {
    $errors = [];
    
    if (empty($message)) {
        $errors['message'] = 'Le message est obligatoire';
    } elseif (strlen($message) < 3) {
        $errors['message'] = 'Le message doit contenir au moins 3 caractères';
    }
    
    return $errors;
}

/**
 * Récupère les tickets d'un étudiant
 * @param PDO $pdo Connexion à la base de données
 * @param int $user_id ID de l'utilisateur
 * @return array Liste des tickets
 */
function getTicketsByUser(PDO $pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT t.*, u.username 
        FROM tickets t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.user_id = ? 
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Récupère tous les tickets (pour le tuteur)
 * @param PDO $pdo Connexion à la base de données
 * @param string|null $statut Filtre par statut (optionnel)
 * @return array Liste des tickets
 */
function getAllTickets(PDO $pdo, $statut = null) {
    $sql = "
        SELECT t.*, u.username 
        FROM tickets t 
        JOIN users u ON t.user_id = u.id
    ";
    $params = [];
    
    if ($statut) {
        $sql .= " WHERE t.statut = ?";
        $params[] = $statut;
    }
    
    $sql .= " ORDER BY t.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Récupère un ticket avec ses détails
 * @param PDO $pdo Connexion à la base de données
 * @param int $ticket_id ID du ticket
 * @return array|false Détails du ticket
 */
function getTicket(PDO $pdo, $ticket_id) {
    $stmt = $pdo->prepare("
        SELECT t.*, u.username 
        FROM tickets t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.id = ?
    ");
    $stmt->execute([$ticket_id]);
    return $stmt->fetch();
}

/**
 * Récupère les commentaires d'un ticket
 * @param PDO $pdo Connexion à la base de données
 * @param int $ticket_id ID du ticket
 * @return array Liste des commentaires
 */
function getCommentaires(PDO $pdo, $ticket_id) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, u.role 
        FROM commentaires c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.ticket_id = ? 
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$ticket_id]);
    return $stmt->fetchAll();
}

/**
 * Crée un nouveau ticket
 * @param PDO $pdo Connexion à la base de données
 * @param array $data Données du ticket
 * @param int $user_id ID de l'utilisateur
 * @return int|false ID du ticket créé ou false en cas d'erreur
 */
function createTicket(PDO $pdo, $data, $user_id) {
    $stmt = $pdo->prepare("
        INSERT INTO tickets (user_id, titre, description, categorie, priorite) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $result = $stmt->execute([
        $user_id,
        $data['titre'],
        $data['description'],
        $data['categorie'],
        $data['priorite']
    ]);
    
    return $result ? $pdo->lastInsertId() : false;
}

/**
 * Ajoute un commentaire à un ticket
 * @param PDO $pdo Connexion à la base de données
 * @param int $ticket_id ID du ticket
 * @param int $user_id ID de l'utilisateur
 * @param string $message Message du commentaire
 * @return bool Succès de l'opération
 */
function addCommentaire(PDO $pdo, $ticket_id, $user_id, $message) {
    $stmt = $pdo->prepare("
        INSERT INTO commentaires (ticket_id, user_id, message) 
        VALUES (?, ?, ?)
    ");
    return $stmt->execute([$ticket_id, $user_id, $message]);
}

/**
 * Met à jour le statut d'un ticket
 * @param PDO $pdo Connexion à la base de données
 * @param int $ticket_id ID du ticket
 * @param string $statut Nouveau statut
 * @return bool Succès de l'opération
 */
function updateTicketStatus(PDO $pdo, $ticket_id, $statut) {
    $stmt = $pdo->prepare("UPDATE tickets SET statut = ? WHERE id = ?");
    return $stmt->execute([$statut, $ticket_id]);
}

/**
 * Valide un formulaire d'inscription
 * @param array $data Données du formulaire
 * @return array Erreurs trouvées
 */
function validateRegistration($data) {
    $errors = [];
    
    if (empty($data['username'])) {
        $errors['username'] = 'Le nom d\'utilisateur est obligatoire';
    } elseif (strlen($data['username']) < 3) {
        $errors['username'] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères';
    } elseif (strlen($data['username']) > 50) {
        $errors['username'] = 'Le nom d\'utilisateur ne doit pas dépasser 50 caractères';
    }
    
    if (empty($data['password'])) {
        $errors['password'] = 'Le mot de passe est obligatoire';
    } elseif (strlen($data['password']) < 8) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
    }
    
    if (empty($data['password_confirm'])) {
        $errors['password_confirm'] = 'La confirmation du mot de passe est obligatoire';
    } elseif ($data['password'] !== $data['password_confirm']) {
        $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';
    }
    
    if (empty($data['role'])) {
        $errors['role'] = 'La catégorie est obligatoire';
    } elseif (!in_array($data['role'], ['etudiant', 'tuteur'])) {
        $errors['role'] = 'La catégorie est invalide';
    }
    
    return $errors;
}

/**
 * Vérifie si un nom d'utilisateur existe déjà
 * @param PDO $pdo Connexion à la base de données
 * @param string $username Nom d'utilisateur à vérifier
 * @return bool True si l'utilisateur existe
 */
function userExists(PDO $pdo, $username) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch() !== false;
}

/**
 * Crée un nouvel utilisateur
 * @param PDO $pdo Connexion à la base de données
 * @param array $data Données de l'utilisateur
 * @return bool Succès de l'opération
 */
function createUser(PDO $pdo, $data) {
    // Vérifier si l'utilisateur existe déjà
    if (userExists($pdo, $data['username'])) {
        return false;
    }
    
    // Hasher le mot de passe
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Insérer le nouvel utilisateur
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password, role) 
        VALUES (?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['username'],
        $hashed_password,
        $data['role']
    ]);
}
