-- Base de données pour Mini Helpdesk de cours
-- Création des tables et insertion des données de test

-- Suppression des tables existantes si elles existent
DROP TABLE IF EXISTS commentaires;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS users;

-- Création de la table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('etudiant', 'tuteur') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    categorie ENUM('Cours', 'TD', 'TP') NOT NULL,
    priorite ENUM('Basse', 'Moyenne', 'Haute') NOT NULL,
    statut ENUM('Ouvert', 'En cours', 'Résolu') DEFAULT 'Ouvert',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table commentaires
CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion des données de test

-- Compte tuteur (login: tuteur / password: tuteur123)
INSERT INTO users (username, password, role) VALUES 
('tuteur', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tuteur');

-- Comptes étudiants (login: etudiant1 / password: etudiant123, etudiant2 / password: etudiant123)
INSERT INTO users (username, password, role) VALUES 
('etudiant1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('etudiant2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant');

-- Tickets de démonstration
INSERT INTO tickets (user_id, titre, description, categorie, priorite, statut) VALUES 
(2, 'Problème avec les tableaux en PHP', 'Je ne comprends pas comment utiliser les tableaux multidimensionnels en PHP. J''ai essayé plusieurs exemples mais j''obtiens toujours des erreurs.', 'Cours', 'Moyenne', 'Ouvert'),
(2, 'Erreur de connexion à la base de données', 'Lorsque j''essaie de me connecter à MySQL avec PDO, j''obtiens une erreur "Connection failed". Voici mon code...', 'TP', 'Haute', 'En cours'),
(3, 'Question sur les fonctions récursives', 'Pouvez-vous m''expliquer comment fonctionne la récursivité en PHP ? Je dois implémenter une fonction factorielle.', 'Cours', 'Basse', 'Résolu'),
(3, 'Problème avec le formulaire HTML', 'Mon formulaire ne soumet pas les données correctement. Les champs semblent vides côté PHP.', 'TD', 'Moyenne', 'Ouvert');

-- Commentaires de démonstration
INSERT INTO commentaires (ticket_id, user_id, message) VALUES 
(1, 1, 'Bonjour ! Peux-tu me montrer ton code pour que je puisse t''aider ? Les tableaux multidimensionnels peuvent être déroutants au début.'),
(1, 2, 'Voici mon code : $arr = array(array(1,2), array(3,4)); echo $arr[0][1];'),
(1, 1, 'Ton code est correct ! L''erreur vient probablement d''ailleurs. Vérifie que tu n''as pas de syntax error avant cette ligne.'),
(2, 1, 'As-tu vérifié tes identifiants de connexion MySQL ? Hôte, nom de base, utilisateur, mot de passe ?'),
(2, 2, 'Oui, tout semble correct. Voici le message d''erreur complet...'),
(3, 1, 'La récursivité c''est quand une fonction s''appelle elle-même. Pour la factorielle : function fact($n) { return $n <= 1 ? 1 : $n * fact($n-1); }'),
(3, 3, 'Merci beaucoup ! C''est beaucoup plus clair maintenant.'),
(4, 3, 'J''ai vérifié mon HTML et tout semble bon. Le problème doit venir du traitement PHP.');
