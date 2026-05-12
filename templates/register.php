<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Système de Ticketing</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>index.php">🎫 Ticketing</a>
            </div>
            <nav>
                <a href="<?php echo BASE_URL; ?>index.php">Accueil</a>
                <a href="<?php echo BASE_URL; ?>index.php?page=login">Connexion</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div style="max-width: 500px; margin: 50px auto;">
                <div class="card">
                    <div class="card-header">
                        <h2>Créer un Compte Client</h2>
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

                    <form method="POST" class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" required value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe *</label>
                            <input type="password" id="password" name="password" required minlength="6">
                            <small style="color: #7f8c8d;">Minimum 6 caractères</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmez le mot de passe *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 15px;">Créer un Compte</button>
                    </form>

                    <div class="card-footer" style="text-align: center;">
                        <p style="margin: 0;">Déjà inscrit ? 
                            <a href="<?php echo BASE_URL; ?>index.php?page=login" style="color: #3498db;">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
