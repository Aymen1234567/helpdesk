<?php
/**
 * Dashboard étudiant
 * Affiche les tickets de l'étudiant connecté
 */

require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Vérification que l'utilisateur est un étudiant
requireEtudiant();

// Récupération des tickets de l'étudiant
$tickets = getTicketsByUser($pdo, $_SESSION['user_id']);

// Statistiques
$total_tickets = count($tickets);
$ouverts = 0;
$encours = 0;
$resolus = 0;

foreach ($tickets as $ticket) {
    switch ($ticket['statut']) {
        case 'Ouvert':
            $ouverts++;
            break;
        case 'En cours':
            $encours++;
            break;
        case 'Résolu':
            $resolus++;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant - Mini Helpdesk de cours</title>
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
                <li><a href="dashboard_etudiant.php" class="active">Mes tickets</a></li>
                <li><a href="create_ticket.php">Créer un ticket</a></li>
            </ul>
        </nav>

        <main>
            <h1>Mes tickets</h1>
            
            <!-- Statistiques -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="background-color: #f9fafb; padding: 1rem; border-radius: 4px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #3b82f6;"><?php echo $total_tickets; ?></div>
                    <div style="color: #6b7280;">Total des tickets</div>
                </div>
                <div style="background-color: #dbeafe; padding: 1rem; border-radius: 4px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #1e40af;"><?php echo $ouverts; ?></div>
                    <div style="color: #6b7280;">Tickets ouverts</div>
                </div>
                <div style="background-color: #fed7aa; padding: 1rem; border-radius: 4px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #c2410c;"><?php echo $encours; ?></div>
                    <div style="color: #6b7280;">En cours</div>
                </div>
                <div style="background-color: #d1fae5; padding: 1rem; border-radius: 4px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #065f46;"><?php echo $resolus; ?></div>
                    <div style="color: #6b7280;">Résolus</div>
                </div>
            </div>

            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <h3>Vous n'avez pas encore de ticket</h3>
                    <p>Cliquez sur "Créer un ticket" pour poser votre première question.</p>
                    <a href="create_ticket.php" class="btn btn-primary">Créer un ticket</a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td>#<?php echo $ticket['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ticket['titre']); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['categorie']); ?></td>
                                    <td>
                                        <span class="badge <?php echo getPrioriteBadge($ticket['priorite']); ?>">
                                            <?php echo htmlspecialchars($ticket['priorite']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo getStatutBadge($ticket['statut']); ?>">
                                            <?php echo htmlspecialchars($ticket['statut']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($ticket['created_at']); ?></td>
                                    <td>
                                        <a href="ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-primary">
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
