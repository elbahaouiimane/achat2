<?php
// Configuration de la connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "achatapp";

// Créez une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Lire les données JSON envoyées
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data) {
    // Extraire les informations du formulaire
    $fournisseur = $conn->real_escape_string($data['fournisseur']);
    $dateAchat = $conn->real_escape_string($data['dateAchat']);
    $totalGlobal = $conn->real_escape_string($data['totalGlobal']);

    // Insérer les données de la réception dans la base de données
    $sql = "INSERT INTO receptions (fournisseur, date_achat, total_global) VALUES ('$fournisseur', '$dateAchat', '$totalGlobal')";

    if ($conn->query($sql) === TRUE) {
        $receptionId = $conn->insert_id; // Récupérer l'ID de la réception insérée

        // Insérer les articles
        foreach ($data['data'] as $item) {
            $numarticle = $conn->real_escape_string($item['numarticle']);
            $description = $conn->real_escape_string($item['description']);
            $quantity = $conn->real_escape_string($item['quantity']);
            $price = $conn->real_escape_string($item['price']);
            $whs = $conn->real_escape_string($item['whs']);
            $total = $conn->real_escape_string($item['total']);

            $sql = "INSERT INTO reception_items (reception_id, numarticle, description, quantity, price, whs, total)
                    VALUES ('$receptionId', '$numarticle', '$description', '$quantity', '$price', '$whs', '$total')";

            if (!$conn->query($sql)) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'insertion des articles.']);
                $conn->close();
                exit();
            }
        }

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'insertion de la réception.']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Données non valides.']);
}
?>