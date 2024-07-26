<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}

// Inclure le fichier de requêtes SQL
require_once 'queries_sql.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reception de marchandises</title>
    <link rel="stylesheet" href="accstyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script>
       function addNewRow() {
            const table = document.getElementById('articleTable');
            const newRow = table.insertRow();

            // Ajouter les cellules avec contenu éditable (total de 6 cellules)
            for (let i = 0; i < 6; i++) {
                const cell = newRow.insertCell(i);
                if (i === 2) {
                    // Cellule pour la quantité avec un champ de saisie
                    cell.innerHTML = '<input type="number" value="1" min="1" class="quantity-input" />';
                } else {
                    // Autres cellules éditables
                    cell.contentEditable = true;
                }
            }

            // Ajouter la cellule pour les boutons d'action (dernière cellule)
            const actionCell = newRow.insertCell(6);
            actionCell.innerHTML = `
                <button class="btn-modifier" onclick="alert('Modifier cet article')"><i class="bi bi-pencil"></i></button>
                <button class="btn-supprimer" onclick="deleteRow(this)"><i class="bi bi-trash"></i></button>
            `;
        }

        function validatePurchase() {
            alert('Achat validé avec un total.');
        }
    </script>
</head>
<body>
<header>
    <div class="nav-container">
        <img src="image/logol.png" alt="Logo" class="logo">
        <nav>
            <ul>
                <li><a href="receptionm.php">Reception de Marchandises</a></li>
                <li><a href="logout.php">Deconnexion</a></li>
            </ul>
        </nav>
    </div>
    <div class="divider"></div>
</header>

<h1>Nom Fournisseur : 
    <select name="fournisseur">
        <?php foreach ($fournisseurs as $fournisseur): ?>
            <option value="<?php echo htmlspecialchars($fournisseur); ?>">
                <?php echo htmlspecialchars($fournisseur); ?>
            </option>
        <?php endforeach; ?>
    </select>
</h1>
<h1>Date : <input type="date" id="date_achat" /></h1>

<button class="btn-ajouter" onclick="addNewRow()">+</button>
<table id="articleTable">
    <tr>
        <th>Numéro d'article</th>
        <th>Description d'article</th>
        <th>Quantité</th>
        <th>Prix unitaire</th>
        <th>Magasin</th>
        <th>Total brut (DI)</th>
        <th>Actions</th>
    </tr>
    <tr>
        <td contentEditable="true"><select name="numarticle">
        <?php foreach ($numarticles as $numarticle): ?>
            <option value="<?php echo htmlspecialchars($numarticle); ?>">
                <?php echo htmlspecialchars($numarticle); ?>
            </option>
        <?php endforeach; ?>
    </select></td>
        <td contentEditable="true"></td>
        <td contentEditable="true"> <input type="number" value="1" min="1" class="quantity-input" /></td>
        <td contentEditable="true"></td>
        <td contentEditable="true"></td>
        <td contentEditable="true"></td>
        <td>
            <button class="btn-modifier" onclick="alert('Modifier cet article')"> <i class="bi bi-pencil"></i></button>
            <button class="btn-supprimer" onclick="deleteRow(this)">  <i class="bi bi-trash"></i></button>
        </td>
    </tr>
</table>

<h1>Total : <input type="text" id="total" /></h1>
<button class="btn-valider" onclick="validatePurchase()">Valider l'achat</button>

</body>
</html>