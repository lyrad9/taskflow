<?php
// Active l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure la connexion à la base de données
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feuilles de Temps</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Feuilles de Temps</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="projets.php">Projets</a></li>
                <li><a href="taches.php">Tâches</a></li>
                <li><a href="ressources.php">Ressources</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Ajouter une Feuille de Temps</h2>
        <form action="php/creer-feuille-temps.php" method="POST">
            <label for="tacheID">Tâche :</label>
            <select id="tacheID" name="tacheID" required>
                <?php
                // Récupérer la liste des tâches
                $sql = "SELECT * FROM Tâches";
                $stmt = $pdo->query($sql);
                $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($taches as $tache) : ?>
                    <option value="<?= $tache['TâcheID'] ?>"><?= $tache['NomTâche'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="ressourceID">Ressource :</label>
            <select id="ressourceID" name="ressourceID" required>
                <?php
                // Récupérer la liste des ressources
                $sql = "SELECT * FROM Ressources";
                $stmt = $pdo->query($sql);
                $ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($ressources as $ressource) : ?>
                    <option value="<?= $ressource['RessourceID'] ?>"><?= $ressource['Nom'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="date">Date :</label>
            <input type="date" id="date" name="date" required>

            <label for="heuresTravaillées">Heures Travaillées :</label>
            <input type="number" id="heuresTravaillées" name="heuresTravaillées" step="0.01" required>

            <label for="commentaires">Commentaires :</label>
            <textarea id="commentaires" name="commentaires"></textarea>

            <button type="submit">Enregistrer</button>
        </form>

        <h2>Liste des Feuilles de Temps</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tâche</th>
                    <th>Ressource</th>
                    <th>Date</th>
                    <th>Heures</th>
                    <th>Commentaires</th>
                    <th>Création</th>
                    <th>Modification</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Récupérer les feuilles de temps avec les noms des tâches et ressources
                $sql = "SELECT FeuillesDeTemps.*, Tâches.NomTâche, Ressources.Nom AS NomRessource
                        FROM feuillesdetemps
                        INNER JOIN Tâches ON FeuillesDeTemps.TâcheID = Tâches.TâcheID
                        INNER JOIN Ressources ON FeuillesDeTemps.RessourceID = Ressources.RessourceID";
                $stmt = $pdo->query($sql);
                $feuilles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($feuilles as $feuille) : ?>
                    <tr>
                        <td><?= $feuille['FeuilleTempsID'] ?></td>
                        <td><?= $feuille['NomTâche'] ?></td>
                        <td><?= $feuille['NomRessource'] ?></td>
                        <td><?= $feuille['Date'] ?></td>
                        <td><?= $feuille['HeuresTravaillées'] ?></td>
                        <td><?= $feuille['Commentaires'] ?></td>
                        <td><?= $feuille['DateCréation'] ?></td>
                        <td><?= $feuille['DateModification'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>&copy; 2023 Gestion de Projet</p>
    </footer>
</body>
</html>