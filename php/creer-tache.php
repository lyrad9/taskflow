<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomTache = $_POST['nomTache'];
    $description = $_POST['description'];
    $projetID = $_POST['projetID'];
    $priorite = $_POST['priorite'];

    $sql = "INSERT INTO Tâches (NomTâche, Description, ProjetID, Priorité) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nomTache, $description, $projetID, $priorite]);

    header("Location: ../taches.php"); // Redirige vers la page des tâches
}
?>