<?php
// Active l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure la connexion à la base de données
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $tacheID = $_POST['tacheID'];
    $ressourceID = $_POST['ressourceID'];
    $date = $_POST['date'];
    $heuresTravaillées = $_POST['heuresTravaillées'];
    $commentaires = $_POST['commentaires'];

    // Valider les données (exemple simple)
    if (empty($tacheID) || empty($ressourceID) || empty($date) || empty($heuresTravaillées)) {
        die("Tous les champs sont obligatoires.");
    }

    // Insérer les données dans la table FeuillesDeTemps
    try {
        $sql = "INSERT INTO feuillesdetemps (TâcheID, RessourceID, Date, HeuresTravaillées, Commentaires) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tacheID, $ressourceID, $date, $heuresTravaillées, $commentaires]);

        // Rediriger vers la page des feuilles de temps
        header("Location: ../feuille-temps.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de l'insertion : " . $e->getMessage());
    }
}
?>