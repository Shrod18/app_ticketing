<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Incident - Système de Ticketing</title>
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
            <div style="max-width: 700px; margin: 0 auto;">
                <div class="page-header" style="margin-bottom: 20px;">
                    <h1>Créer un Incident</h1>
                    <a href="<?php echo BASE_URL; ?>index.php?page=client_dashboard" class="btn btn-secondary">← Retour</a>
                </div>

                <?php if (!empty($result['errors'])): ?>
                    <div class="alert alert-danger">
                        <strong>Erreurs :</strong>
                        <ul style="margin-top: 10px; margin-left: 20px;">
                            <?php foreach ($result['errors'] as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <form id="incident-form" method="POST" enctype="multipart/form-data" onsubmit="return validateFiles()">
                        <div class="form-group">
                            <label for="titre">Titre de l'incident *</label>
                            <input type="text" id="titre" name="titre" required placeholder="Décrivez brièvement le problème"
                                   value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="description">Description détaillée *</label>
                            <textarea id="description" name="description" required placeholder="Fournissez une description complète du problème"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="criticite">Niveau de criticité *</label>
                            <select id="criticite" name="criticite" required>
                                <option value="normale" <?php echo ($_POST['criticite'] ?? 'normale') === 'normale' ? 'selected' : ''; ?>>Normale</option>
                                <option value="basse" <?php echo ($_POST['criticite'] ?? '') === 'basse' ? 'selected' : ''; ?>>Basse</option>
                                <option value="haute" <?php echo ($_POST['criticite'] ?? '') === 'haute' ? 'selected' : ''; ?>>Haute</option>
                                <option value="critique" <?php echo ($_POST['criticite'] ?? '') === 'critique' ? 'selected' : ''; ?>>Critique</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="files">Fichiers joints (optionnel)</label>
                            <input type="file" id="files" name="files[]" multiple accept="application/pdf,image/jpeg,image/png,image/gif,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            <small style="color: #7f8c8d; display: block; margin-top: 8px;">
                                Vous pouvez sélectionner plusieurs fichiers, ajouter un par un et supprimer avant envoi.<br>
                                Formats acceptés : PDF, Images, Texte, Word, Excel.<br>
                                Taille maximale par fichier : 5 MB.
                            </small>
                            <div id="selected-file-list" class="file-preview-list" style="margin-top: 12px;"></div>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-success">✓ Créer l'incident</button>
                            <a href="<?php echo BASE_URL; ?>index.php?page=client_dashboard" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="<?php echo BASE_URL; ?>js/main.js"></script>
</body>
</html>
