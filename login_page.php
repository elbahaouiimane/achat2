<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Préparez la requête SQL
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Utilisateur trouvé
        $_SESSION['username'] = $user;
        header("Location: receptionm.php");
        exit();
    } else {
        // Utilisateur non trouvé
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>login_page</title>
    <link rel="stylesheet" type="text/css" href="login_style.css">
</head>

<body>
  
<img src="image/logol.png" alt="Logo" class="logo">
   <form method="post" action="login_page.php">
    <h2>Connexion</h2>
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required>
     
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
     
         <br>
        <button type="submit">Se connecter</button>
        <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
       
    </form>
</body>
</html>