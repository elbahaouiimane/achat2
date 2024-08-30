<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "achatapp";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer l'ID de réception depuis l'URL
$receptionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Requête SQL pour récupérer les détails de la réception
$sql = "SELECT r.id AS reception_id, r.fournisseur, r.date_achat, r.total_global,
               i.numarticle, i.description, i.quantity, i.price, i.total
        FROM receptions r
        LEFT JOIN reception_items i ON r.id = i.reception_id
        WHERE r.id = $receptionId";

$result = $conn->query($sql);

$facture = array();
$totalGlobal = 0;

if ($result->num_rows > 0) {
    // Stocker les résultats dans un tableau
    while($row = $result->fetch_assoc()) {
        $facture[] = $row;
        $totalGlobal = $row['total_global']; // On récupère le total global
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .details {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .details p {
            margin: 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }

        .print-button {
            display: inline-block;
            padding: 8px 16px;
            margin-top: 20px;
            background-color: #28a745; /* Vert */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #218838; /* Vert plus foncé */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Facture d'Achat</h1>
    </div>
    <div class="details">
        <div>
            <p><strong>ID Réception :</strong> <?php echo htmlspecialchars($receptionId); ?></p>
            <p><strong>Fournisseur :</strong> <?php echo htmlspecialchars($facture[0]['fournisseur']); ?></p>
            <p><strong>Date d'Achat :</strong> <?php echo htmlspecialchars($facture[0]['date_achat']); ?></p>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Numéro d'Article</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Total Brut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facture as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['numarticle']); ?></td>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td><?php echo htmlspecialchars($item['total']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="total">
        <p><strong>Total Global :</strong> <?php echo htmlspecialchars($totalGlobal); ?></p>
    </div>
    <button class="print-button" onclick="window.print()">Imprimer</button>
</div>
</body>
</html>
