<form action="php/creer-projet.php" method="POST">
    <label for="nomProjet">Nom du Projet :</label>
    <input type="text" id="nomProjet" name="nomProjet" required>

    <label for="description">Description :</label>
    <textarea id="description" name="description"></textarea>

    <label for="typeProjet">Type de Projet :</label>
    <select id="typeProjet" name="typeProjet">
        <option value="Construction">Construction</option>
        <option value="Logiciel">Logiciel</option>
    </select>

    <label for="budgetPrevu">Budget Prévu :</label>
    <input type="number" id="budgetPrevu" name="budgetPrevu" step="0.01" required>

    <label for="dateDebutPrevue">Date de Début Prévue :</label>
    <input type="date" id="dateDebutPrevue" name="dateDebutPrevue" required>

    <label for="dateFinPrevue">Date de Fin Prévue :</label>
    <input type="date" id="dateFinPrevue" name="dateFinPrevue" required>

    <button type="submit">Créer le Projet</button>
</form>
<!-- afficher les projets -->
<?php
include 'includes/db.php';

$sql = "SELECT * FROM Projets";
$stmt = $pdo->query($sql);
$projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Type</th>
            <th>Budget</th>
            <th>Dates</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($projets as $projet) : ?>
            <tr>
                <td><?= $projet['ProjetID'] ?></td>
                <td>
    <a href="projet_details.php?id=<?= $projet['ProjetID'] ?>" class="btn btn-primary btn-sm">
        <?= $projet['NomProjet'] ?> <i class="fas fa-arrow-right"></i>
    </a>
</td>
                <td><?= $projet['Description'] ?></td>
                <td><?= $projet['TypeProjet'] ?></td>
                <td><?= $projet['BudgetPrévu'] ?></td>
                <td><?= $projet['DateDébutPrévue'] ?> - <?= $projet['DateFinPrévue'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>