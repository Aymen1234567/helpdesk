<?php
/**
 * Dashboard tuteur
 * Affiche tous les tickets avec filtre par statut
 */

require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Vérification que l'utilisateur est un tuteur
requireTuteur();

// Récupération du filtre de statut
$statut_filter = $_GET['statut'] ?? '';
$statuts_valides = ['', 'Ouvert', 'En cours', 'Résolu'];
if (!in_array($statut_filter, $statuts_valides)) {
    $statut_filter = '';
}

// Récupération des tickets
$tickets = getAllTickets($pdo, $statut_filter ?: null);

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
    <title>Dashboard Tuteur - Mini Helpdesk de cours</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Mini Helpdesk de cours</div>
                <div class="user-info">
                    <span>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?> (Tuteur)</span>
                    <a href="logout.php" class="logout-btn">Déconnexion</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <nav>
            <ul>
                <li><a href="dashboard_tuteur.php" class="active">Tous les tickets</a></li>
            </ul>
        </nav>

        <main>
            <h1>Tous les tickets</h1>
            
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

            <!-- Filtre par statut -->
            <div style="margin-bottom: 2rem;">
                <form method="GET" action="dashboard_tuteur.php" style="display: flex; gap: 1rem; align-items: center;">
                    <label for="statut">Filtrer par statut :</label>
                    <select name="statut" id="statut">
                        <option value="">Tous les statuts</option>
                        <option value="Ouvert" <?php echo $statut_filter === 'Ouvert' ? 'selected' : ''; ?>>Ouvert</option>
                        <option value="En cours" <?php echo $statut_filter === 'En cours' ? 'selected' : ''; ?>>En cours</option>
                        <option value="Résolu" <?php echo $statut_filter === 'Résolu' ? 'selected' : ''; ?>>Résolu</option>
                    </select>
                    <button type="submit" class="btn btn-secondary">Filtrer</button>
                    <?php if ($statut_filter): ?>
                        <a href="dashboard_tuteur.php" class="btn btn-secondary">Réinitialiser</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <h3>Aucun ticket trouvé</h3>
                    <p><?php echo $statut_filter ? 'Aucun ticket avec ce statut.' : 'Aucun ticket dans le système.'; ?></p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Étudiant</th>
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
                                    <td><?php echo htmlspecialchars($ticket['username']); ?></td>
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
