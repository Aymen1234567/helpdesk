<?php
/**
 * Page de création de ticket
 * Réservée aux étudiants
 */

require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Vérification que l'utilisateur est un étudiant
requireEtudiant();

$errors = [];
$success = '';

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'titre' => clean($_POST['titre'] ?? ''),
        'description' => clean($_POST['description'] ?? ''),
        'categorie' => clean($_POST['categorie'] ?? ''),
        'priorite' => clean($_POST['priorite'] ?? '')
    ];
    
    // Validation des données
    $errors = validateTicket($data);
    
    // Si pas d'erreurs, création du ticket
    if (empty($errors)) {
        $ticket_id = createTicket($pdo, $data, $_SESSION['user_id']);
        
        if ($ticket_id) {
            $success = 'Votre ticket a été créé avec succès !';
            // Redirection vers le ticket créé
            header('Location: ticket.php?id=' . $ticket_id);
            exit;
        } else {
            $errors['general'] = 'Une erreur est survenue lors de la création du ticket.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un ticket - Mini Helpdesk de cours</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Mini Helpdesk de cours</div>
                <div class="user-info">
                    <span>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?> (Étudiant)</span>
                    <a href="logout.php" class="logout-btn">Déconnexion</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <nav>
            <ul>
                <li><a href="dashboard_etudiant.php">Mes tickets</a></li>
                <li><a href="create_ticket.php" class="active">Créer un ticket</a></li>
            </ul>
        </nav>

        <main>
            <h1>Créer un nouveau ticket</h1>
            <p>Décrivez votre problème ou votre question pour que le tuteur puisse vous aider.</p>
            
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="post" action="create_ticket.php">
                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" 
                               value="<?php echo htmlspecialchars($data['titre'] ?? ''); ?>" 
                               required>
                        <?php if (!empty($errors['titre'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['titre']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="categorie">Catégorie *</label>
                        <select id="categorie" name="categorie" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="Cours" <?php echo ($data['categorie'] ?? '') === 'Cours' ? 'selected' : ''; ?>>Cours</option>
                            <option value="TD" <?php echo ($data['categorie'] ?? '') === 'TD' ? 'selected' : ''; ?>>TD</option>
                            <option value="TP" <?php echo ($data['categorie'] ?? '') === 'TP' ? 'selected' : ''; ?>>TP</option>
                        </select>
                        <?php if (!empty($errors['categorie'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['categorie']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="priorite">Priorité *</label>
                        <select id="priorite" name="priorite" required>
                            <option value="">Sélectionnez une priorité</option>
                            <option value="Basse" <?php echo ($data['priorite'] ?? '') === 'Basse' ? 'selected' : ''; ?>>Basse</option>
                            <option value="Moyenne" <?php echo ($data['priorite'] ?? '') === 'Moyenne' ? 'selected' : ''; ?>>Moyenne</option>
                            <option value="Haute" <?php echo ($data['priorite'] ?? '') === 'Haute' ? 'selected' : ''; ?>>Haute</option>
                        </select>
                        <?php if (!empty($errors['priorite'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['priorite']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                        <small style="color: #6b7280;">
                            Décrivez en détail votre problème ou votre question. Plus vous serez précis, 
                            plus le tuteur pourra vous aider efficacement.
                        </small>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['description']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">Créer le ticket</button>
                        <a href="dashboard_etudiant.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
            
            <!-- Guide pour bien rédiger un ticket -->
            <div style="margin-top: 2rem; padding: 1rem; background-color: #f9fafb; border-radius: 4px;">
                <h3>Conseils pour bien rédiger votre ticket</h3>
                <ul style="margin-left: 1.5rem; color: #6b7280;">
                    <li>Soyez précis dans votre titre</li>
                    <li>Décrivez le contexte de votre problème</li>
                    <li>Indiquez ce que vous avez déjà essayé</li>
                    <li>Joignez les messages d'erreur si vous en avez</li>
                    <li>Choisissez la bonne priorité : Haute pour les urgences, Moyenne pour les questions courantes</li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
