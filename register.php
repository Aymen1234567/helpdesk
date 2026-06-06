<?php
/**
 * Page d'inscription
 * Permet de créer un nouveau compte (étudiant ou tuteur)
 */

require_once 'config/db.php';
require_once 'includes/functions.php';

$errors = [];
$success = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => clean($_POST['username'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
        'role' => clean($_POST['role'] ?? '')
    ];
    
    // Validation des données
    $errors = validateRegistration($data);
    
    // Si pas d'erreurs, création du compte
    if (empty($errors)) {
        $result = createUser($pdo, $data);
        
        if ($result) {
            $success = 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.';
            // Redirection vers la page de connexion après 2 secondes
            header('Refresh: 2; url=login.php');
        } else {
            $errors['general'] = 'Une erreur est survenue lors de la création du compte.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Mini Helpdesk de cours</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Mini Helpdesk de cours</div>
                <div class="user-info">
                    <a href="login.php" class="btn btn-secondary">Se connecter</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <main>
            <div class="form-container">
                <h1>Créer un compte</h1>
                <p>Remplissez ce formulaire pour créer votre compte sur le Mini Helpdesk de cours.</p>
                
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
                
                <form method="post" action="register.php">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur *</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>" 
                               required>
                        <?php if (!empty($errors['username'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['username']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input type="password" id="password" name="password" required>
                        <?php if (!empty($errors['password'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['password']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe *</label>
                        <input type="password" id="password_confirm" name="password_confirm" required>
                        <?php if (!empty($errors['password_confirm'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['password_confirm']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Catégorie *</label>
                        <select id="role" name="role" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="etudiant" <?php echo ($data['role'] ?? '') === 'etudiant' ? 'selected' : ''; ?>>Étudiant</option>
                            <option value="tuteur" <?php echo ($data['role'] ?? '') === 'tuteur' ? 'selected' : ''; ?>>Tuteur</option>
                        </select>
                        <?php if (!empty($errors['role'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['role']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">Créer le compte</button>
                        <a href="login.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
                
                <!-- Guide pour créer un bon compte -->
                <div style="margin-top: 2rem; padding: 1rem; background-color: #f9fafb; border-radius: 4px;">
                    <h3>Conseils pour votre inscription</h3>
                    <ul style="margin-left: 1.5rem; color: #6b7280;">
                        <li>Choisissez un nom d'utilisateur unique et facile à retenir</li>
                        <li>Utilisez un mot de passe d'au moins 8 caractères</li>
                        <li>Étudiants : Vous pourrez créer des tickets d'aide</li>
                        <li>Tuteurs : Vous pourrez gérer tous les tickets et aider les étudiants</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
