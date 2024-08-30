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
$sql = "SELECT r.id AS reception_id, r.fournisseur, r.date_achat, r.total_global
        FROM receptions r
        GROUP BY r.id
        ORDER BY r.date_achat DESC, r.id DESC";

$result = $conn->query($sql);

$achats = array();

if ($result->num_rows > 0) {
    // Stocker les résultats dans un tableau
    while($row = $result->fetch_assoc()) {
        $achats[$row['reception_id']] = $row;
    }
}

// Préparer les articles pour chaque ID de réception
$articles = array();
$sql_items = "SELECT i.reception_id, i.numarticle, i.description, i.quantity, i.price, i.total
              FROM reception_items i
              ORDER BY i.reception_id, i.numarticle";

$result_items = $conn->query($sql_items);

if ($result_items->num_rows > 0) {
    while ($row = $result_items->fetch_assoc()) {
        $articles[$row['reception_id']][] = $row;
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
        /* Styles existants ici... */

        /* Styles pour la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .print-button {
            display: inline-block;
            padding: 8px 16px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        .btn-valider {
            font-size: 14px; /* Rétrécir la taille du bouton */
            padding: 5px 10px; /* Rétrécir le bouton */
            background-color: #28a745;;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-valider:hover {
            background-color: #28a745;;
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
                <th>Facture</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($achats as $id => $achat): ?>
                <tr>
                    <td><?php echo htmlspecialchars($achat['reception_id']); ?></td>
                    <td><?php echo htmlspecialchars($achat['fournisseur']); ?></td>
                    <td><?php echo htmlspecialchars($achat['date_achat']); ?></td>
                    <td><?php echo htmlspecialchars($achat['total_global']); ?></td>
                    <td>
                        <button class="btn-valider" onclick="openModal(<?php echo htmlspecialchars($id); ?>)">Génerer</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Fenêtre modale pour la facture -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <iframe id="invoiceFrame" style="width:100%; height:600px; border:none;"></iframe>
        
    </div>
</div>

<script>
    function openModal(id) {
        var modal = document.getElementById('myModal');
        var iframe = document.getElementById('invoiceFrame');
        iframe.src = 'facture.php?id=' + id;
        modal.style.display = 'flex';
    }

    var span = document.getElementsByClassName("close")[0];
    span.onclick = function() {
        var modal = document.getElementById('myModal');
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('myModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    function printInvoice() {
        var iframe = document.getElementById('invoiceFrame');
        iframe.contentWindow.print();
    }
</script>
</body>
</html>
