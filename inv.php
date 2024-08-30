<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}

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

// Requête SQL pour récupérer l'historique des achats
$sql = "SELECT r.id AS reception_id, r.fournisseur, r.date_achat, r.total_global,
               i.numarticle, i.description, i.quantity, i.price, i.whs, i.total
        FROM receptions r
        LEFT JOIN reception_items i ON r.id = i.reception_id
        ORDER BY r.date_achat DESC, r.id DESC";

$result = $conn->query($sql);

$achats = array();

if ($result->num_rows > 0) {
    // Stocker les résultats dans un tableau
    while($row = $result->fetch_assoc()) {
        $achats[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Achats</title>
    <link rel="stylesheet" href="accstyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        table {
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            font-size: smaller;
        }

        th {
            background-color: #ffffff;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .logo {
            width: 8%;
        }

        h1 {
            font-size: smaller;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .btn-modifier, .btn-supprimer, .btn-valider {
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-modifier {
            background-color: transparent;
            color: #000000;
        }

        .btn-supprimer {
            background-color: transparent;
            color: rgb(0, 0, 0);
        }

        .btn-valider {
            background-color: rgb(64, 160, 35);
            color: white;
            border: none;
            padding: 8px 10px;
            margin-left: 2px;
        }

        .btn-ajouter {
            background-color: rgb(0, 0, 0);
            color: white;
            border: none;
            padding: 1px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-ajouter:hover {
            background-color: rgb(0, 0, 0);
        }

        .btn-valider:hover {
            background-color: rgb(47, 175, 47);
        }

        .quantity-input {
            width: 40px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
        }

        .quantity-input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        select {
            width: 100px;
            padding: 2px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 14px;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        select option {
            padding: 10px;
            border: none;
        }

        .total-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <div class="nav-container">
        <img src="image/logol.png" alt="Logo" class="logo">
        <nav>
            <ul>
                <li><a href="receptionm.php">Réception de Marchandises</a></li>
                <li><a href="inv.php">Historique des Achats</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </div>
    <div class="divider"></div>
</header>

<div class="container">
    <h1>Historique des Achats</h1>
    <table>
        <thead>
            <tr>
                <th>ID Réception</th>
                <th>Fournisseur</th>
                <th>Date d'Achat</th>
                <th>Total Global</th>
                <th>Numéro d'Article</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Magasin</th>
                <th>Total Brut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($achats as $achat): ?>
                <tr>
                    <td><?php echo htmlspecialchars($achat['reception_id']); ?></td>
                    <td><?php echo htmlspecialchars($achat['fournisseur']); ?></td>
                    <td><?php echo htmlspecialchars($achat['date_achat']); ?></td>
                    <td><?php echo htmlspecialchars($achat['total_global']); ?></td>
                    <td><?php echo htmlspecialchars($achat['numarticle']); ?></td>
                    <td><?php echo htmlspecialchars($achat['description']); ?></td>
                    <td><?php echo htmlspecialchars($achat['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($achat['price']); ?></td>
                    <td><?php echo htmlspecialchars($achat['whs']); ?></td>
                    <td><?php echo htmlspecialchars($achat['total']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
