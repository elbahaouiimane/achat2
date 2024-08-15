<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: login_page.php");
    exit();
}

// Inclure le fichier de requêtes SQL
$serverName = "SRV-SAP10"; // Remplacez par le nom de votre serveur SQL ou l'adresse IP
$connectionOptions = array(
    "Database" => "SAP", // Remplacez par le nom de votre base de données
    "Uid" => "sa", // Remplacez par votre nom d'utilisateur SQL Server
    "PWD" => "bpsmaroc" // Remplacez par votre mot de passe SQL Server
);

try {
    // Création de la connexion PDO
    $conn = new PDO("sqlsrv:Server=$serverName;Database={$connectionOptions['Database']}", $connectionOptions['Uid'], $connectionOptions['PWD']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des fournisseurs
    $sql = "SELECT CardName FROM OPDN ORDER BY CardName";
    $stmt = $conn->query($sql);
    $fournisseurs = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fournisseurs[] = $row['CardName'];
    }

    // Récupération des numéros d'articles, descriptions et prix associés
    $sqlItems = "SELECT ItemCode, Dscription, Price FROM PDN1 ORDER BY ItemCode";
    $stmtItems = $conn->query($sqlItems);
    $articlesData = [];
    while ($row = $stmtItems->fetch(PDO::FETCH_ASSOC)) {
        $articlesData[$row['ItemCode']] = [
            'description' => $row['Dscription'],
            'price' => $row['Price']
        ];
    }

    // Récupération des codes de magasins
    $sqlw = "SELECT WhsCode FROM PDN1 ORDER BY WhsCode";
    $stmtw = $conn->query($sqlw);
    $whscode = [];
    while ($row = $stmtw->fetch(PDO::FETCH_ASSOC)) {
        $whscode[] = $row['WhsCode'];
    }

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réception de marchandises</title>
    <link rel="stylesheet" href="accstyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script>
        // Convertir les données PHP en tableaux JavaScript
        const articlesData = <?php echo json_encode($articlesData); ?>;
        const whs = <?php echo json_encode($whscode); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            populateArticleSelectors(); // Peupler les sélecteurs d'articles au chargement de la page
            populateWHSSelectors(); // Peupler les sélecteurs de magasins au chargement de la page
        });

        function populateArticleSelectors() {
            // Fonction pour peupler tous les sélecteurs d'articles présents sur la page
            const selects = document.querySelectorAll('select[name="numarticle"]');
            selects.forEach(select => {
                Object.keys(articlesData).forEach(article => {
                    const option = document.createElement('option');
                    option.value = article;
                    option.textContent = article;
                    select.appendChild(option);
                });
                select.addEventListener('change', function() {
                    updateRow(this);
                });
            });
        }

        function populateWHSSelectors() {
            // Fonction pour peupler tous les sélecteurs de magasins présents sur la page
            const selects = document.querySelectorAll('select[name="whs"]');
            selects.forEach(select => {
                whs.forEach(whscode => {
                    const option = document.createElement('option');
                    option.value = whscode;
                    option.textContent = whscode;
                    select.appendChild(option);
                });
            });
        }

        function updateRow(select) {
            // Fonction pour mettre à jour la ligne en fonction du numéro d'article sélectionné
            const selectedArticle = select.value;
            const row = select.closest('tr'); // Trouver la ligne correspondante
            const descriptionCell = row.querySelector('td[name="description"]');
            const priceCell = row.querySelector('td[name="price"]');
            const quantityInput = row.querySelector('input.quantity-input');
            const totalCell = row.querySelector('td[contenteditable]');

            // Vider les cellules de description et de prix
            descriptionCell.textContent = '';
            priceCell.textContent = '';

            // Ajouter la description et le prix correspondants si l'article existe
            if (articlesData[selectedArticle]) {
                const description = articlesData[selectedArticle].description;
                const price = articlesData[selectedArticle].price;

                // Mettre à jour la cellule de description
                descriptionCell.textContent = description;

                // Mettre à jour le prix unitaire
                priceCell.textContent = price;
                
                // Mettre à jour le total brut si la quantité est déjà définie
                updateTotal(row);
            }

            // Écouter les changements dans la quantité
            quantityInput.addEventListener('input', () => updateTotal(row));
        }

        function updateTotal(row) {
            // Fonction pour calculer le total brut
            const priceCell = row.querySelector('td[name="price"]');
            const quantityInput = row.querySelector('input.quantity-input');
            const totalCell = row.querySelector('td[contenteditable]');
            
            const price = parseFloat(priceCell.textContent) || 0;
            const quantity = parseInt(quantityInput.value, 10) || 0;

            // Calculer le total brut
            const total = price * quantity;

            // Mettre à jour la cellule du total brut
            totalCell.textContent = total.toFixed(2);
            
            // Mettre à jour le total global
            updateGlobalTotal();
        }

        function updateGlobalTotal() {
            // Calculer le total global
            let totalGlobal = 0;
            const totalCells = document.querySelectorAll('#articleTable td[contenteditable]');
            totalCells.forEach(cell => {
                const total = parseFloat(cell.textContent) || 0;
                totalGlobal += total;
            });

            // Afficher le total global dans l'élément d'entrée
            document.getElementById('total').value = totalGlobal.toFixed(2);
        }

        function addNewRow() {
            const table = document.getElementById('articleTable');
            const newRow = table.insertRow(); // Créer une nouvelle ligne

            // Créer et ajouter les cellules de la nouvelle ligne
            for (let i = 0; i < 6; i++) { // Il y a 6 colonnes avant les actions
                const cell = newRow.insertCell(i);
                if (i === 0) {
                    // Cellule pour le sélecteur d'article
                    const select = document.createElement('select');
                    select.name = 'numarticle';
                    cell.appendChild(select);
                } else if (i === 1) {
                    // Cellule pour la description (texte direct)
                    cell.setAttribute('name', 'description');
                } else if (i === 2) {
                    // Cellule pour la quantité avec un champ de saisie
                    cell.innerHTML = '<input type="number" value="1" min="1" class="quantity-input" />';
                } else if (i === 3) {
                    // Cellule pour le prix unitaire
                    cell.setAttribute('name', 'price');
                } else if (i === 4) {
                    // Cellule pour le magasin (avec sélecteur)
                    const select = document.createElement('select');
                    select.name = 'whs';
                    cell.appendChild(select);
                } else if (i === 5) {
                    // Cellule pour le total brut (modifiable)
                    cell.contentEditable = true;
                }
            }

            // Ajouter la cellule pour les boutons d'action (dernière cellule)
            const actionCell = newRow.insertCell(6); // 6 est l'index de la colonne des actions
            actionCell.innerHTML = `
                <button class="btn-modifier" onclick="alert('Modifier cet article')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-supprimer" onclick="deleteRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            `;

            // Peupler les sélecteurs d'articles, et de magasins de la nouvelle ligne
            populateArticleSelectorsForNewRow(newRow);
            populateWHSSelectorsForNewRow(newRow);
        }

        function populateArticleSelectorsForNewRow(row) {
            // Fonction pour peupler le sélecteur d'articles d'une nouvelle ligne
            const select = row.querySelector('select[name="numarticle"]');
            Object.keys(articlesData).forEach(article => {
                const option = document.createElement('option');
                option.value = article;
                option.textContent = article;
                select.appendChild(option);
            });
            select.addEventListener('change', function() {
                updateRow(this);
            });
        }

        function populateWHSSelectorsForNewRow(row) {
            // Fonction pour peupler le sélecteur de magasins d'une nouvelle ligne
            const select = row.querySelector('select[name="whs"]');
            whs.forEach(whscode => {
                const option = document.createElement('option');
                option.value = whscode;
                option.textContent = whscode;
                select.appendChild(option);
            });
        }

        function validatePurchase() {
            // Afficher le message avec le total
            const totalGlobal = document.getElementById('total').value;
            alert('Achat validé avec un total de ' + totalGlobal + ' MAD.');
        }

        function deleteRow(button) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
            // Recalculer le total global après suppression de la ligne
            updateGlobalTotal();
        }
    </script>
</head>
<body>
<header>
    <div class="nav-container">
        <img src="image/logol.png" alt="Logo" class="logo">
        <nav>
            <ul>
                <li><a href="receptionm.php">Réception de Marchandises</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
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
    <thead>
        <tr>
            <th>Numéro d'article</th>
            <th>Description d'article</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Magasin</th>
            <th>Total brut (DI)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <select name="numarticle">
                    <?php foreach ($articlesData as $numarticle => $data): ?>
                        <option value="<?php echo htmlspecialchars($numarticle); ?>">
                            <?php echo htmlspecialchars($numarticle); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td name="description">
                <?php foreach ($articlesData as $numarticle => $data): ?>
                    <span style="display:none;"><?php echo htmlspecialchars($data['description']); ?></span>
                <?php endforeach; ?>
            </td>
            <td>
                <input type="number" value="1" min="1" class="quantity-input" />
            </td>
            <td name="price"></td>
            <td>
                <select name="whs">
                    <?php foreach ($whscode as $whscodes): ?>
                        <option value="<?php echo htmlspecialchars($whscodes); ?>">
                            <?php echo htmlspecialchars($whscodes); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td contentEditable="true"></td>
            <td>
                <button class="btn-modifier" onclick="alert('Modifier cet article')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-supprimer" onclick="deleteRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    </tbody>
</table>

<!-- Réajustement pour garder la même disposition -->
<div class="total-container">
    <h1>Total : <input type="text" id="total" readonly /></h1>
    <button class="btn-valider" onclick="validatePurchase()">Valider l'achat</button>
</div>

</body>
</html>