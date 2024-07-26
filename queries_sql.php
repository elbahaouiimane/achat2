<?php
$serverName = "SRV"; // Replace with your SQL Server name or IP address
$connectionOptions = array(
    "Database" => "S", // Replace with your database name
    "Uid" => "sa", // Replace with your SQL Server username
    "PWD" => "b" // Replace with your SQL Server password
);

try {
    // Création de la connexion PDO
    $conn = new PDO("sqlsrv:Server=$serverName;Database={$connectionOptions['Database']}", $connectionOptions['Uid'], $connectionOptions['PWD']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Exécution de la requête SQL
    $sql = "SELECT CardName FROM OPDN ORDER BY CardName";
    $stmt = $conn->query($sql);

    // Récupération des résultats
    $fournisseurs = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fournisseurs[] = $row['CardName'];
    }
    $sqlItems = "SELECT ItemCode FROM PDN1 ORDER BY ItemCode";
    $stmtItems = $conn->query($sqlItems);

    // Récupération des résultats pour les articles
    $numarticles = [];
    while ($row = $stmtItems->fetch(PDO::FETCH_ASSOC)) {
        $numarticles[] = $row['ItemCode'];
    }
    
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>




