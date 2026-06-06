# Mini Helpdesk de cours

Une application web simple de gestion de tickets d'aide pour les étudiants, développée en PHP pur sans framework.

## Description

Le Mini Helpdesk de cours est une application qui permet aux étudiants de poser des questions sur leurs cours, TD et TP, et aux tuteurs d'y répondre. Les fonctionnalités incluent :

- **Authentification** avec deux rôles : étudiant et tuteur
- **Inscription** d'utilisateurs avec validation et rôle
- **Création de tickets** par les étudiants (titre, description, catégorie, priorité)
- **Gestion des statuts** des tickets (Ouvert, En cours, Résolu)
- **Commentaires** sur les tickets pour le dialogue entre étudiants et tuteurs
- **Dashboard** différent selon le rôle de l'utilisateur

## Choix techniques

- **PHP 8+** sans framework (code pur, vanilla)
- **MySQL** avec PDO et requêtes préparées
- **HTML5 + CSS3** basique (pas de framework CSS)
- **JavaScript vanilla** minimal
- **Sessions PHP** natives pour l'authentification
- **Architecture MVC simple** avec séparation traitement/affichage

## Structure des fichiers

```
/helpdesk
├── index.php              → redirection selon rôle
├── login.php              → formulaire de connexion
├── register.php           → formulaire d'inscription
├── logout.php             → destruction session
├── dashboard_etudiant.php → dashboard étudiant
├── dashboard_tuteur.php   → dashboard tuteur
├── create_ticket.php      → création de ticket
├── ticket.php             → détail ticket + commentaires
├── update_status.php      → mise à jour statut (tuteur)
├── test_auth.php          → test d'authentification
├── test_register.php      → test d'inscription
├── config/
│   └── db.php             → connexion PDO
├── includes/
│   ├── auth.php           → authentification
│   └── functions.php      → fonctions utilitaires
├── sql/
│   └── schema.sql         → schéma BDD + données test
├── css/
│   └── style.css          → feuille de style
├── .gitignore            → fichiers ignores par Git
└── README.md
```

## Base de données

L'application utilise 3 tables MySQL :

- **users** : id, username, password (hashé), role, created_at
- **tickets** : id, user_id, titre, description, categorie, priorite, statut, created_at
- **commentaires** : id, ticket_id, user_id, message, created_at

## Installation

### Prérequis

- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, ou PHP built-in server)

### Étapes d'installation

1. **Cloner ou télécharger** les fichiers du projet

2. **Importer la base de données** :
   ```bash
   mysql -u root -p helpdesk < sql/schema.sql
   ```

3. **Configurer la connexion BDD** :
   Éditez `config/db.php` et modifiez les constantes si nécessaire :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'helpdesk');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Lancer l'application** :

   **Option 1 - PHP built-in server** :
   ```bash
   cd helpdesk
   php -S localhost:8000
   ```
   Puis accédez à `http://localhost:8000`

   **Option 2 - Apache/Nginx** :
   Placez les fichiers dans le répertoire web du serveur et configurez un VirtualHost si nécessaire.

## Comptes de test

Le script `sql/schema.sql` inclut des comptes de test :

| Rôle | Username | Password |
|------|----------|----------|
| Tuteur | tuteur | tuteur123 |
| Étudiant | etudiant1 | etudiant123 |
| Étudiant | etudiant2 | etudiant123 |

## Fonctionnalités

### Rôle Étudiant

- ✅ Créer un compte avec validation
- ✅ Voir uniquement ses propres tickets
- ✅ Créer de nouveaux tickets
- ✅ Consulter le détail de ses tickets
- ✅ Ajouter des commentaires sur ses tickets
- ❌ Accès interdit aux tickets des autres étudiants
### Rôle Tuteur

- ✅ Voir tous les tickets du système
- ✅ Filtrer les tickets par statut
- ✅ Consulter le détail de n'importe quel ticket
- ✅ Mettre à jour le statut des tickets
- ✅ Ajouter des commentaires sur tous les tickets

### Sécurité

- ✅ Hashage des mots de passe avec `password_hash()`
- ✅ Requêtes SQL préparées avec PDO
- ✅ Échappement des sorties HTML avec `htmlspecialchars()`
- ✅ Vérification des droits d'accès par rôle et propriété
- ✅ Validation des entrées utilisateur
- ✅ Sessions sécurisées

## Design et interface

L'interface utilise un design simple et épuré :

- **Palette de couleurs** : blanc, gris clair (#f5f5f5), bleu (#3b82f6)
- **Typographie** : system-ui/Arial
- **Badges colorés** pour les statuts et priorités
- **Tableaux propres** avec alternance de couleurs
- **Design responsive** pour mobile et desktop

## Développement

### Code commenté en français

Tous les fichiers PHP sont commentés en français pour faciliter la compréhension et la maintenance.

### Séparation traitement/affichage

Chaque fichier PHP suit la structure :
```php
<?php
// Traitement PHP (en haut)
// ...
?>
<!DOCTYPE html>
<!-- HTML (en bas) -->
<html>
...
</html>
```

### Fonctions utilitaires

Le fichier `includes/functions.php` contient des fonctions réutilisables :
- Validation de formulaires (inscription et tickets)
- Création et authentification d'utilisateurs
- Formatage de dates et badges
- Gestion des tickets et commentaires
- Nettoyage des entrées

## Tests

### Fichiers de test

L'application inclut des fichiers de test pour diagnostiquer rapidement les problèmes :

- **test_auth.php** : Test de l'authentification et de la connexion BDD
- **test_register.php** : Test de la création et validation d'utilisateurs

Ces fichiers sont exclus du dépôt Git via `.gitignore` et servent uniquement au développement.

### Tests manuels

Pour tester l'application :

1. **Test d'inscription** : Créez un nouveau compte étudiant ou tuteur
2. **Test de connexion** : Connectez-vous avec les comptes de test fournis
3. **Test de création** : Créez des tickets en tant qu'étudiant
4. **Test de gestion** : Testez la gestion des statuts en tant que tuteur
5. **Test de sécurité** : Vérifiez les droits d'accès (étudiant ne peut pas voir les tickets des autres)

### Tests automatisés

Pour exécuter les tests de diagnostic :

```bash
# Test d'authentification
wget -qO- http://localhost:8000/test_auth.php

# Test d'inscription
wget -qO- http://localhost:8000/test_register.php
```

## Améliorations possibles

- Notifications par email
- Système de tags/catégories avancé
- Export des tickets en PDF
- Historique des modifications
- Système de fichiers attachés
- API REST
- Tests unitaires
- Internationalisation (i18n)

## Licence

Ce projet est distribué sous licence MIT.
