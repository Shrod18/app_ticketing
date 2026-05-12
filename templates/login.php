<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système de Ticketing</title>
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
                <a href="<?php echo BASE_URL; ?>index.php?page=register">Inscription</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div style="max-width: 400px; margin: 50px auto;">
                <div class="card">
                    <div class="card-header">
                        <h2>Connexion</h2>
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

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="card-body">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <p style="font-size: 12px; color: #7f8c8d; margin-bottom: 20px;">
                            Comptes de test disponibles :<br>
                            alice.dupont@company.com | bob.martin@company.com<br>
                            Mot de passe : password123
                        </p>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Se Connecter</button>
                    </form>

                    <div class="card-footer" style="text-align: center;">
                        <p style="margin: 0;">Pas encore inscrit ? 
                            <a href="<?php echo BASE_URL; ?>index.php?page=register" style="color: #3498db;">Créer un compte</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
