<?php
include 'includes/db.php';

// Vérifier si l'ID du projet est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: projets.php"); // Redirige si l'ID n'est pas fourni
    exit();
}

$projetID = $_GET['id'];

// Récupérer les détails du projet
$sqlProjet = "SELECT * FROM Projets WHERE ProjetID = ?";
$stmtProjet = $pdo->prepare($sqlProjet);
$stmtProjet->execute([$projetID]);
$projet = $stmtProjet->fetch(PDO::FETCH_ASSOC);

if (!$projet) {
    echo "Projet non trouvé.";
    exit();
}

// Récupérer les tâches du projet
$sqlTaches = "SELECT * FROM Tâches WHERE ProjetID = ?";
$stmtTaches = $pdo->prepare($sqlTaches);
$stmtTaches->execute([$projetID]);
$taches = $stmtTaches->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Projet : <?= $projet['NomProjet'] ?></h1>
<p>Description : <?= $projet['Description'] ?></p>
<p>Budget : <?= $projet['BudgetPrévu'] ?></p>
<p>Dates : <?= $projet['DateDébutPrévue'] ?> - <?= $projet['DateFinPrévue'] ?></p>

<h2>Tâches du Projet</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Priorité</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($taches as $tache) : ?>
            <tr>
                <td><?= $tache['TâcheID'] ?></td>
                <td><?= $tache['NomTâche'] ?></td>
                <td><?= $tache['Description'] ?></td>
                <td><?= $tache['Priorité'] ?></td>
                <td><?= $tache['Statut'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="projets.php">Retour à la liste des projets</a>