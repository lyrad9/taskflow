<form action="php/creer-ressource.php" method="POST">
    <label for="nom">Nom de la Ressource :</label>
    <input type="text" id="nom" name="nom" required>

    <label for="typeRessource">Type de Ressource :</label>
    <select id="typeRessource" name="typeRessource">
        <option value="Humaine">Humaine</option>
        <option value="Matérielle">Matérielle</option>
    </select>

    <label for="competences">Compétences :</label>
    <textarea id="competences" name="competences"></textarea>

    <label for="disponibilite">Disponibilité :</label>
    <input type="text" id="disponibilite" name="disponibilite" required>

    <label for="coutHoraire">Coût Horaire :</label>
    <input type="number" id="coutHoraire" name="coutHoraire" step="0.01" required>

    <button type="submit">Ajouter la Ressource</button>
</form>

<?php
include 'includes/db.php';

$sql = "SELECT * FROM Ressources";
$stmt = $pdo->query($sql);
$ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Type</th>
            <th>Compétences</th>
            <th>Disponibilité</th>
            <th>Coût Horaire</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ressources as $ressource) : ?>
            <tr>
                <td><?= $ressource['RessourceID'] ?></td>
                <td><?= $ressource['Nom'] ?></td>
                <td><?= $ressource['TypeRessource'] ?></td>
                <td><?= $ressource['Compétences'] ?></td>
                <td><?= $ressource['Disponibilité'] ?></td>
                <td><?= $ressource['CoûtHoraire'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>