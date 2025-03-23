<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $typeRessource = $_POST['typeRessource'];
    $competences = $_POST['competences'];
    $disponibilite = $_POST['disponibilite'];
    $coutHoraire = $_POST['coutHoraire'];

    $sql = "INSERT INTO Ressources (Nom, TypeRessource, Compétences, Disponibilité, CoûtHoraire) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $typeRessource, $competences, $disponibilite, $coutHoraire]);

    header("Location: ../ressources.php"); // Redirige vers la page des ressources
}
?>