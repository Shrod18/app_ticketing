<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Système de Ticketing</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>index.php">🎫 Ticketing</a>
            </div>
            <nav>
                <span class="user-info">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (Ingénieur)</span>
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
                    ✓ <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    ✗ <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Demandes de transfert en attente -->
            <?php if (!empty($pending_transfers)): ?>
                <div class="card" style="border-left: 4px solid #f39c12; background-color: #fef8f0;">
                    <div class="card-header">
                        <h2>📩 Demandes de Transfert en Attente (<?php echo count($pending_transfers); ?>)</h2>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; gap: 15px;">
                            <?php foreach ($pending_transfers as $transfer): ?>
                                <div style="padding: 15px; background: white; border-radius: 4px; border-left: 3px solid #f39c12;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 20px; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 250px;">
                                            <h4 style="margin: 0 0 8px 0; color: #2c3e50;">
                                                <?php echo htmlspecialchars($transfer['incident_titre']); ?>
                                            </h4>
                                            <p style="margin: 5px 0; color: #7f8c8d; font-size: 13px;">
                                                De: <strong><?php echo htmlspecialchars($transfer['from_nom'] . ' ' . $transfer['from_prenom']); ?></strong> | 
                                                Client: <strong><?php echo htmlspecialchars($transfer['client_nom'] . ' ' . $transfer['client_prenom']); ?></strong>
                                            </p>
                                            <?php if (!empty($transfer['comment'])): ?>
                                                <div style="margin-top: 12px; padding: 12px; background: #fcf8e3; border: 1px solid #f5e1a4; border-radius: 4px; color: #856404;">
                                                    <strong>Message :</strong>
                                                    <p style="margin: 8px 0 0 0; white-space: pre-wrap; color: #5d533d;"> <?php echo htmlspecialchars(trim($transfer['comment'])); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                            <form method="POST" action="<?php echo BASE_URL; ?>index.php?page=accept_transfer" style="display: inline;">
                                                <input type="hidden" name="transfer_id" value="<?php echo $transfer['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-small">✓ Accepter</button>
                                            </form>
                                            <form method="POST" action="<?php echo BASE_URL; ?>index.php?page=reject_transfer" style="display: inline;">
                                                <input type="hidden" name="transfer_id" value="<?php echo $transfer['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-small">✗ Refuser</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h1>Tous les Incidents</h1>
            </div>

            <!-- Filtres -->
            <div class="card" style="background: #f8f9fa;">
                <div class="card-body">
                    <form id="engineer-filter-form" method="GET" action="<?php echo BASE_URL; ?>index.php?page=engineer_dashboard" class="filters">
                        <input type="hidden" name="page" value="engineer_dashboard">
                        
                        <input type="text" id="engineer-search" name="search" placeholder="Rechercher un incident..." 
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 8px; margin: 0;">
                                <input type="checkbox" id="my-incidents" name="my_incidents" value="1" 
                                       <?php echo (isset($_GET['my_incidents']) && $_GET['my_incidents'] === '1') ? 'checked' : ''; ?>>
                                Mes incidents
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 8px; margin: 0;">
                                <input type="checkbox" id="open-only" name="open_only" value="1" 
                                       <?php echo (isset($_GET['open_only']) && $_GET['open_only'] === '1') ? 'checked' : ''; ?>>
                                Non fermés
                            </label>
                        </div>

                        <a href="<?php echo BASE_URL; ?>index.php?page=engineer_dashboard" class="btn btn-secondary">Réinitialiser</a>
                    </form>
                </div>
            </div>

            <?php if (empty($incidents)): ?>
                <div class="card">
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <h3>Aucun incident trouvé</h3>
                        <p>Aucun incident ne correspond à vos critères.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <table id="engineer-incident-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Client</th>
                                <th>Criticité</th>
                                <th>Statut</th>
                                <th>Assigné à</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($incidents as $incident): ?>
                                <tr data-status="<?php echo $incident['statut']; ?>" data-assigned-to-current="<?php echo ($incident['assigned_to'] == $_SESSION['user_id']) ? '1' : '0'; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($incident['titre']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="table-muted">
                                            <?php echo htmlspecialchars($incident['client_nom'] . ' ' . $incident['client_prenom']); ?>
                                        </span>
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
                                            <?php echo $incident['engineer_nom'] ? htmlspecialchars($incident['engineer_prenom'] . ' ' . $incident['engineer_nom']) : 'Non assigné'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="table-muted">
                                            <?php echo date('d/m/Y', strtotime($incident['date_creation'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>index.php?page=engineer_incident_detail&id=<?php echo $incident['id']; ?>" class="btn btn-small btn-primary">Voir</a>
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
    <script src="<?php echo BASE_URL; ?>js/main.js"></script>
</body>
</html>
