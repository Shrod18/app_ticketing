<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Système de Ticketing</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>index.php">🎫 Ticketing</a>
            </div>
            <nav>
                <a href="<?php echo BASE_URL; ?>index.php?page=login">Connexion</a>
                <a href="<?php echo BASE_URL; ?>index.php?page=register">Inscription</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Système de Gestion d'Incidents</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 style="color: #2c3e50; margin-bottom: 20px;">Bienvenue 👋</h2>
                    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                        Bienvenue dans notre plateforme de gestion d'incidents. Cette application vous permet de:
                    </p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                        <div style="border-left: 4px solid #3498db; padding-left: 15px;">
                            <h3 style="color: #2c3e50; margin-bottom: 10px;">Pour les Clients</h3>
                            <ul style="list-style-position: inside; line-height: 1.8;">
                                <li>Créer un compte facilement</li>
                                <li>Signaler des incidents</li>
                                <li>Joindre des fichiers à vos incidents</li>
                                <li>Suivre l'état de vos incidents</li>
                                <li>Consulter l'historique de vos signalements</li>
                            </ul>
                        </div>

                        <div style="border-left: 4px solid #27ae60; padding-left: 15px;">
                            <h3 style="color: #2c3e50; margin-bottom: 10px;">Pour les Ingénieurs</h3>
                            <ul style="list-style-position: inside; line-height: 1.8;">
                                <li>Consulter tous les incidents</li>
                                <li>Assigner des tickets</li>
                                <li>Transférer des tickets à des collègues</li>
                                <li>Clôturer les incidents résolus</li>
                                <li>Suivre l'historique complet</li>
                            </ul>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 40px;">
                        <p style="margin-bottom: 20px; color: #7f8c8d;">Prêt à commencer ?</p>
                        <div class="btn-group" style="justify-content: center;">
                            <a href="<?php echo BASE_URL; ?>index.php?page=login" class="btn btn-primary">Se Connecter</a>
                            <a href="<?php echo BASE_URL; ?>index.php?page=register" class="btn btn-secondary">Créer un Compte</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
