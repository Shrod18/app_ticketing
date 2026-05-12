<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'Incident - Système de Ticketing</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>index.php">🎫 Ticketing</a>
            </div>
            <nav>
                <span class="user-info">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <form method="GET" action="<?php echo BASE_URL; ?>index.php" style="display: inline;">
                    <input type="hidden" name="page" value="logout">
                    <button type="submit" class="logout-btn">Déconnexion</button>
                </form>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div style="max-width: 900px; margin: 0 auto;">
                <div class="page-header" style="margin-bottom: 20px;">
                    <h1><?php echo htmlspecialchars($incident['titre']); ?></h1>
                    <a href="<?php echo BASE_URL; ?>index.php?page=client_dashboard" class="btn btn-secondary">← Retour</a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h2 style="margin: 0;">Informations de l'Incident</h2>
                            <div>
                                <span class="badge criticite-<?php echo $incident['criticite']; ?>">
                                    Criticité: <?php echo ucfirst($incident['criticite']); ?>
                                </span>
                                <?php
                                $badge_class = match($incident['statut']) {
                                    'ouvert' => 'badge-info',
                                    'assigné' => 'badge-warning',
                                    'clôturé' => 'badge-success',
                                    default => 'badge-secondary'
                                };
                                ?>
                                <span class="badge <?php echo $badge_class; ?>" style="margin-left: 10px;">
                                    Statut: <?php echo ucfirst($incident['statut']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                            <div>
                                <strong style="color: #7f8c8d;">Identifiant</strong>
                                <p style="margin: 5px 0; color: #2c3e50;">#<?php echo $incident['id']; ?></p>
                            </div>
                            <div>
                                <strong style="color: #7f8c8d;">Date de création</strong>
                                <p style="margin: 5px 0; color: #2c3e50;"><?php echo date('d/m/Y à H:i', strtotime($incident['date_creation'])); ?></p>
                            </div>
                            <?php if ($incident['assigned_to']): ?>
                                <div>
                                    <strong style="color: #7f8c8d;">Assigné à</strong>
                                    <p style="margin: 5px 0; color: #2c3e50;"><?php echo htmlspecialchars($incident['engineer_prenom'] . ' ' . $incident['engineer_nom']); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($incident['date_cloture']): ?>
                                <div>
                                    <strong style="color: #7f8c8d;">Date de clôture</strong>
                                    <p style="margin: 5px 0; color: #2c3e50;"><?php echo date('d/m/Y à H:i', strtotime($incident['date_cloture'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Description</h2>
                    </div>
                    <div class="card-body">
                        <p style="line-height: 1.6; white-space: pre-wrap;"><?php echo htmlspecialchars(trim($incident['description'])); ?></p>
                    </div>
                </div>

                <?php if ($incident['statut'] === 'clôturé' && !empty($incident['progress_note'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h2>Avancement du traitement</h2>
                        </div>
                        <div class="card-body">
                            <p style="line-height: 1.6; white-space: pre-wrap; color: #2c3e50;"><?php echo nl2br(htmlspecialchars(trim($incident['progress_note']))); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($incident['files'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h2>Fichiers joints</h2>
                        </div>
                        <div class="card-body">
                            <ul class="file-list">
                                <?php foreach ($incident['files'] as $file): ?>
                                    <li class="file-item">
                                        <div class="file-info">
                                            <div class="file-name">📄 <?php echo htmlspecialchars($file['nom']); ?></div>
                                            <div class="file-size">
                                                Taille: <?php echo round($file['size'] / 1024, 2); ?> KB | 
                                                Ajouté le <?php echo date('d/m/Y à H:i', strtotime($file['date_ajout'])); ?>
                                            </div>
                                        </div>
                                        <a href="<?php echo BASE_URL; ?>index.php?page=download_file&id=<?php echo $file['id']; ?>" class="btn btn-small btn-primary">Télécharger</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
