<?php
/**
 * Page de détail d'un ticket
 * Affiche le ticket, les commentaires et permet d'ajouter des commentaires
 */

require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Vérification que l'utilisateur est connecté
requireLogin();

// Récupération et validation de l'ID du ticket
$ticket_id = $_GET['id'] ?? '';
if (!is_numeric($ticket_id) || $ticket_id <= 0) {
    header('Location: ' . (isTuteur() ? 'dashboard_tuteur.php' : 'dashboard_etudiant.php'));
    exit;
}

$ticket_id = (int)$ticket_id;

// Récupération du ticket
$ticket = getTicket($pdo, $ticket_id);
if (!$ticket) {
    header('Location: ' . (isTuteur() ? 'dashboard_tuteur.php' : 'dashboard_etudiant.php'));
    exit;
}

// Vérification des droits d'accès
if (isEtudiant() && $ticket['user_id'] != $_SESSION['user_id']) {
    header('Location: dashboard_etudiant.php');
    exit;
}

// Récupération des commentaires
$commentaires = getCommentaires($pdo, $ticket_id);

// Traitement de l'ajout de commentaire
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = clean($_POST['message'] ?? '');
    
    // Validation du message
    $errors = validateCommentaire($message);
    
    // Si pas d'erreurs, ajout du commentaire
    if (empty($errors)) {
        $result = addCommentaire($pdo, $ticket_id, $_SESSION['user_id'], $message);
        
        if ($result) {
            $success = 'Votre commentaire a été ajouté avec succès !';
            // Rafraîchir la page pour voir le nouveau commentaire
            header('Location: ticket.php?id=' . $ticket_id);
            exit;
        } else {
            $errors['general'] = 'Une erreur est survenue lors de l\'ajout du commentaire.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo $ticket_id; ?> - Mini Helpdesk de cours</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Mini Helpdesk de cours</div>
                <div class="user-info">
                    <span>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                        (<?php echo isTuteur() ? 'Tuteur' : 'Étudiant'; ?>)</span>
                    <a href="logout.php" class="logout-btn">Déconnexion</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <nav>
            <ul>
                <li><a href="<?php echo isTuteur() ? 'dashboard_tuteur.php' : 'dashboard_etudiant.php'; ?>">
                    <?php echo isTuteur() ? 'Tous les tickets' : 'Mes tickets'; ?>
                </a></li>
                <?php if (isEtudiant()): ?>
                    <li><a href="create_ticket.php">Créer un ticket</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <main>
            <h1>Ticket #<?php echo $ticket_id; ?> - <?php echo htmlspecialchars($ticket['titre']); ?></h1>
            
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Informations du ticket -->
            <div class="ticket-header">
                <div class="ticket-meta">
                    <span><strong>Étudiant :</strong> <?php echo htmlspecialchars($ticket['username']); ?></span>
                    <span><strong>Catégorie :</strong> <?php echo htmlspecialchars($ticket['categorie']); ?></span>
                    <span><strong>Priorité :</strong> 
                        <span class="badge <?php echo getPrioriteBadge($ticket['priorite']); ?>">
                            <?php echo htmlspecialchars($ticket['priorite']); ?>
                        </span>
                    </span>
                    <span><strong>Statut :</strong> 
                        <span class="badge <?php echo getStatutBadge($ticket['statut']); ?>">
                            <?php echo htmlspecialchars($ticket['statut']); ?>
                        </span>
                    </span>
                    <span><strong>Date :</strong> <?php echo formatDate($ticket['created_at']); ?></span>
                </div>
            </div>
            
            <!-- Description du ticket -->
            <div class="ticket-description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
            </div>
            
            <!-- Mise à jour du statut (réservé au tuteur) -->
            <?php if (isTuteur()): ?>
                <div class="status-form">
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                        <label for="statut">Mettre à jour le statut :</label>
                        <select name="statut" id="statut">
                            <option value="Ouvert" <?php echo $ticket['statut'] === 'Ouvert' ? 'selected' : ''; ?>>Ouvert</option>
                            <option value="En cours" <?php echo $ticket['statut'] === 'En cours' ? 'selected' : ''; ?>>En cours</option>
                            <option value="Résolu" <?php echo $ticket['statut'] === 'Résolu' ? 'selected' : ''; ?>>Résolu</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Mettre à jour</button>
                    </form>
                </div>
            <?php endif; ?>
            
            <!-- Section des commentaires -->
            <div class="comments-section">
                <h2>Commentaires (<?php echo count($commentaires); ?>)</h2>
                
                <?php if (empty($commentaires)): ?>
                    <p style="color: #6b7280; font-style: italic;">Aucun commentaire pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($commentaires as $commentaire): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <div>
                                    <span class="comment-author"><?php echo htmlspecialchars($commentaire['username']); ?></span>
                                    <span class="comment-role"><?php echo htmlspecialchars($commentaire['role']); ?></span>
                                </div>
                                <span><?php echo formatDate($commentaire['created_at']); ?></span>
                            </div>
                            <div class="comment-message">
                                <?php echo nl2br(htmlspecialchars($commentaire['message'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Formulaire d'ajout de commentaire -->
                <div class="comment-form">
                    <h3>Ajouter un commentaire</h3>
                    <form method="post" action="ticket.php?id=<?php echo $ticket_id; ?>">
                        <div class="form-group">
                            <label for="message">Votre message</label>
                            <textarea id="message" name="message" rows="4" required></textarea>
                            <?php if (!empty($errors['message'])): ?>
                                <div class="error"><?php echo htmlspecialchars($errors['message']); ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter le commentaire</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
