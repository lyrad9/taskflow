 <!-- traitement php pour creer un projet    -->
    
    <?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomProjet = $_POST['nomProjet'];
    $description = $_POST['description'];
    $typeProjet = $_POST['typeProjet'];
    $budgetPrevu = $_POST['budgetPrevu'];
    $dateDebutPrevue = $_POST['dateDebutPrevue'];
    $dateFinPrevue = $_POST['dateFinPrevue'];

    $sql = "INSERT INTO Projets (NomProjet, Description, TypeProjet, BudgetPrévu, DateDébutPrévue, DateFinPrévue) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nomProjet, $description, $typeProjet, $budgetPrevu, $dateDebutPrevue, $dateFinPrevue]);

    header("Location: ../projets.php"); // Redirige vers la page des projets
}
?>