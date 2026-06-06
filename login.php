<?php
/**
 * Page de connexion
 * Traitement du formulaire d'authentification
 */

require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Si déjà connecté, rediriger vers le dashboard approprié
if (isLoggedIn()) {
    if (isTuteur()) {
        header('Location: dashboard_tuteur.php');
    } else {
        header('Location: dashboard_etudiant.php');
    }
    exit;
}

// Traitement du formulaire de connexion
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($username)) {
        $errors['username'] = 'Le nom d\'utilisateur est obligatoire';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Le mot de passe est obligatoire';
    }
    
    // Si pas d'erreurs, tentative de connexion
    if (empty($errors)) {
        $user = authenticate($pdo, $username, $password);
        
        if ($user) {
            // Connexion réussie
            if ($user['role'] === 'tuteur') {
                header('Location: dashboard_tuteur.php');
            } else {
                header('Location: dashboard_etudiant.php');
            }
            exit;
        } else {
            $errors['login'] = 'Nom d\'utilisateur ou mot de passe incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mini Helpdesk de cours</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <main>
            <div class="form-container">
                <h1>Connexion</h1>
                <p>Bienvenue sur le Mini Helpdesk de cours</p>
                
                <?php if (!empty($errors['login'])): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($errors['login']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="login.php">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>" 
                               required>
                        <?php if (!empty($errors['username'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['username']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                        <?php if (!empty($errors['password'])): ?>
                            <div class="error"><?php echo htmlspecialchars($errors['password']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
                
                <div style="margin-top: 2rem; padding: 1rem; background-color: #f9fafb; border-radius: 4px;">
                    <h3>Comptes de test</h3>
                    <p><strong>Tuteur :</strong> tuteur / tuteur123</p>
                    <p><strong>Étudiant 1 :</strong> etudiant1 / etudiant123</p>
                    <p><strong>Étudiant 2 :</strong> etudiant2 / etudiant123</p>
                </div>
                
                <div style="margin-top: 1rem; text-align: center;">
                    <p>Pas encore de compte ? <a href="register.php" style="color: #3b82f6; text-decoration: none;">Créer un compte</a></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
