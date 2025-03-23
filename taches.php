<!-- <form action="php/creer-tache.php" method="POST">
    <label for="nomTache">Nom de la Tâche :</label>
    <input type="text" id="nomTache" name="nomTache" required>

    <label for="description">Description :</label>
    <textarea id="description" name="description"></textarea>

    <label for="projetID">Projet :</label>
    <select id="projetID" name="projetID">
        <?php foreach ($projets as $projet) : ?>
            <option value="<?= $projet['ProjetID'] ?>"><?= $projet['NomProjet'] ?></option>
        <?php endforeach; ?>
    </select>

    <label for="priorite">Priorité :</label>
    <select id="priorite" name="priorite">
        <option value="Haute">Haute</option>
        <option value="Moyenne">Moyenne</option>
        <option value="Basse">Basse</option>
    </select>

    <button type="submit">Créer la Tâche</button>
</form> -->
<?php

include 'includes/db.php';

// Récupérer la liste des projets pour le formulaire
$sqlProjets = "SELECT * FROM Projets";
$stmtProjets = $pdo->query($sqlProjets);
$projets = $stmtProjets->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des tâches
$sqlTaches = "SELECT Tâches.*, Projets.NomProjet 
              FROM Tâches 
              INNER JOIN Projets ON Tâches.ProjetID = Projets.ProjetID";
$stmtTaches = $pdo->query($sqlTaches);
$taches = $stmtTaches->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Créer une Tâche</h2>
<form action="php/creer-tache.php" method="POST">
    <label for="nomTache">Nom de la Tâche :</label>
    <input type="text" id="nomTache" name="nomTache" required>

    <label for="description">Description :</label>
    <textarea id="description" name="description"></textarea>

    <label for="projetID">Projet :</label>
    <select id="projetID" name="projetID">
        <?php foreach ($projets as $projet) : ?>
            <option value="<?= $projet['ProjetID'] ?>"><?= $projet['NomProjet'] ?></option>
        <?php endforeach; ?>
    </select>

    <label for="priorite">Priorité :</label>
    <select id="priorite" name="priorite">
        <option value="Haute">Haute</option>
        <option value="Moyenne">Moyenne</option>
        <option value="Basse">Basse</option>
    </select>

    <button type="submit">Créer la Tâche</button>
</form>

<h2>Liste des Tâches</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Projet</th>
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
                <td><?= $tache['NomProjet'] ?></td>
                <td><?= $tache['Priorité'] ?></td>
                <td><?= $tache['Statut'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>