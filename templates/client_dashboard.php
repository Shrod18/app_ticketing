<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Incidents - Système de Ticketing</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>index.php">🎫 Ticketing</a>
            </div>
            <nav>
                <span class="user-info">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (Client)</span>
                <form method="GET" action="<?php echo BASE_URL; ?>index.php" style="display: inline;">
                    <input type="hidden" name="page" value="logout">
                    <button type="submit" class="logout-btn">Déconnexion</button>
                </form>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h1>Mes Incidents</h1>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>index.php?page=create_incident" class="btn btn-primary">✚ Créer un Incident</a>
                </div>
            </div>

            <?php if (empty($incidents)): ?>
                <div class="card">
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <h3>Aucun incident pour le moment</h3>
                        <p>Vous n'avez pas encore signalé d'incident.</p>
                        <a href="<?php echo BASE_URL; ?>index.php?page=create_incident" class="btn btn-primary">Signaler un incident</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <table>
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Criticité</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($incidents as $incident): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($incident['titre']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge criticite-<?php echo $incident['criticite']; ?>">
                                            <?php echo ucfirst($incident['criticite']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = match($incident['statut']) {
                                            'ouvert' => 'badge-info',
                                            'assigné' => 'badge-warning',
                                            'clôturé' => 'badge-success',
                                            default => 'badge-secondary'
                                        };
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($incident['statut']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="table-muted">
                                            <?php echo date('d/m/Y à H:i', strtotime($incident['date_creation'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>index.php?page=incident_detail&id=<?php echo $incident['id']; ?>" class="btn btn-small btn-primary">Détails</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
