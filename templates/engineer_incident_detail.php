<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'Incident - Système de Ticketing</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <script src="<?php echo BASE_URL; ?>js/main.js"></script>
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

                <div class="page-header" style="margin-bottom: 20px;">
                    <h1><?php echo htmlspecialchars($incident['titre']); ?></h1>
                    <a href="<?php echo BASE_URL; ?>index.php?page=engineer_dashboard" class="btn btn-secondary">← Retour</a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                            <h2 style="margin: 0;">Informations</h2>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
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
                                <span class="badge <?php echo $badge_class; ?>">
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
                                <strong style="color: #7f8c8d;">Client</strong>
                                <p style="margin: 5px 0; color: #2c3e50;">
                                    <?php echo htmlspecialchars($incident['client_prenom'] . ' ' . $incident['client_nom']); ?><br>
                                    <small style="color: #95a5a6;"><?php echo htmlspecialchars($incident['client_email']); ?></small>
                                </p>
                            </div>
                            <div>
                                <strong style="color: #7f8c8d;">Date de création</strong>
                                <p style="margin: 5px 0; color: #2c3e50;"><?php echo date('d/m/Y à H:i', strtotime($incident['date_creation'])); ?></p>
                            </div>
                            <div>
                                <strong style="color: #7f8c8d;">Assigné à</strong>
                                <p style="margin: 5px 0; color: #2c3e50;">
                                    <?php echo $incident['assigned_to'] ? htmlspecialchars($incident['engineer_prenom'] . ' ' . $incident['engineer_nom']) : 'Non assigné'; ?>
                                </p>
                            </div>
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

                <div class="card">
                    <div class="card-header">
                        <h2>Suivi / Avancement</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($incident['assigned_to'] == $_SESSION['user_id'] && $incident['statut'] !== 'clôturé'): ?>
                            <form method="POST" action="<?php echo BASE_URL; ?>index.php?page=update_progress">
                                <input type="hidden" name="incident_id" value="<?php echo $incident['id']; ?>">
                                <div class="form-group">
                                    <label for="progress_note">Note de traitement</label>
                                    <textarea id="progress_note" name="progress_note" placeholder="Décrivez les actions en cours, les raisons du délai ou les étapes suivantes..." rows="6"><?php echo htmlspecialchars($incident['progress_note'] ?? ''); ?></textarea>
                                </div>
                                <div class="btn-group" style="justify-content: flex-end; margin-top: 10px;">
                                    <button type="submit" class="btn btn-primary">Enregistrer l'avancement</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <?php if (!empty($incident['progress_note'])): ?>
                                <p style="line-height: 1.6; white-space: pre-wrap; color: #2c3e50;"><?php echo nl2br(htmlspecialchars(trim($incident['progress_note']))); ?></p>
                            <?php else: ?>
                                <p style="color: #7f8c8d;">Aucun avancement enregistré.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions -->
                <?php if ($incident['statut'] !== 'clôturé'): ?>
                    <?php
                        $showActions = false;
                        if (!$incident['assigned_to'] || $incident['assigned_to'] == $_SESSION['user_id'] || $incident['has_pending_transfer']) {
                            $showActions = true;
                        }
                    ?>
                    <?php if ($showActions): ?>
                        <div class="card" style="border: 2px solid #3498db; background: #eaf2f9;">
                            <div class="card-header">
                                <h2>Actions disponibles</h2>
                            </div>
                            <div class="card-body">
                                <div class="btn-group" style="flex-wrap: wrap;">
                                    <!-- Assigner -->
                                    <?php if (!$incident['assigned_to']): ?>
                                        <form method="POST" action="<?php echo BASE_URL; ?>index.php?page=assign_incident" style="display: inline;">
                                            <input type="hidden" name="incident_id" value="<?php echo $incident['id']; ?>">
                                            <button type="submit" class="btn btn-primary">👤 M'assigner ce ticket</button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Clôturer -->
                                    <?php if ($incident['assigned_to'] == $_SESSION['user_id'] && $incident['statut'] !== 'clôturé'): ?>
                                        <?php if (!empty(trim($incident['progress_note'] ?? ''))): ?>
                                            <form id="close-incident-form" method="POST" action="<?php echo BASE_URL; ?>index.php?page=close_incident" style="display: none;">
                                                <input type="hidden" name="incident_id" value="<?php echo $incident['id']; ?>">
                                                <input type="hidden" name="progress_note" value="<?php echo htmlspecialchars($incident['progress_note'] ?? ''); ?>">
                                            </form>
                                            <button type="button" class="btn btn-success" onclick="openCloseModal()">✓ Clôturer l'incident</button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled>Tâche non terminée</button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- Demander un transfert -->
                                    <?php if ($incident['assigned_to'] == $_SESSION['user_id'] && !$incident['has_pending_transfer']): ?>
                                        <button type="button" class="btn btn-warning" onclick="openTransferModal()">↔️ Demander un transfert</button>
                                    <?php endif; ?>

                                    <?php if ($incident['has_pending_transfer']): ?>
                                        <div class="alert alert-warning" style="margin: 0; flex: 1;">
                                            ⚠️ Une demande de transfert est en attente pour ce ticket
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Fichiers joints -->
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

    <!-- Modal de transfert -->
    <div id="transferModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Demander un Transfert</h2>
                <button type="button" class="close-btn" onclick="closeTransferModal()">×</button>
            </div>

            <form method="POST" action="<?php echo BASE_URL; ?>index.php?page=request_transfer">
                <input type="hidden" name="incident_id" value="<?php echo $incident['id']; ?>">

                <div class="form-group">
                    <label for="to_engineer_id">Transférer vers *</label>
                    <select id="to_engineer_id" name="to_engineer_id" required>
                        <option value="">-- Sélectionner un ingénieur --</option>
                        <?php foreach ($other_engineers as $engineer): ?>
                            <option value="<?php echo $engineer['id']; ?>">
                                <?php echo htmlspecialchars($engineer['prenom'] . ' ' . $engineer['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment">Commentaire (optionnel)</label>
                    <textarea id="comment" name="comment" placeholder="Expliquez pourquoi vous transférez ce ticket..."></textarea>
                </div>

                <div class="btn-group" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeTransferModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmation de clôture -->
    <div id="closeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Clôturer l'incident</h2>
                <button type="button" class="close-btn" onclick="closeCloseModal()">×</button>
            </div>
            <p style="margin: 0; line-height: 1.6; color: #2c3e50;">Voulez-vous vraiment clôturer cet incident ? Cette action est définitive.</p>
            <div class="btn-group" style="justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeCloseModal()">Annuler</button>
                <button type="button" class="btn btn-success" onclick="submitCloseIncident()">Oui, clôturer</button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        function openTransferModal() {
            document.getElementById('transferModal').classList.add('active');
        }

        function closeTransferModal() {
            document.getElementById('transferModal').classList.remove('active');
        }

        function openCloseModal() {
            document.getElementById('closeModal').classList.add('active');
        }

        function closeCloseModal() {
            document.getElementById('closeModal').classList.remove('active');
        }

        function submitCloseIncident() {
            document.getElementById('close-incident-form').submit();
        }

        window.onclick = function(event) {
            const transferModal = document.getElementById('transferModal');
            const closeModal = document.getElementById('closeModal');
            if (event.target === transferModal) {
                transferModal.classList.remove('active');
            }
            if (event.target === closeModal) {
                closeModal.classList.remove('active');
            }
        }
    </script>
</body>
</html>
