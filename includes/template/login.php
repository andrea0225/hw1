<?php
require "dbconfig.php";
/** session handler */
session_start();
if (isset($_SESSION["logged_user_id"])) {
    header("Location: home.php");
}
if (isset($_POST["login"])) {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        $conn = mysqli_connect($dbconfig["host"], $dbconfig["user"], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $sql = "SELECT * FROM users WHERE username = '" . $username . "' OR email = '".$username."'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            if (password_verify($_POST["password"], $row["password"])) {
                foreach($row as $key => $value){
                    $_SESSION["logged_user_".$key] = $value; 
                }
                header("Location: home.php");
            } else {
                $error = "Username o password errati!";
            }
        } else {
            $error = "Username o password errati!";
        }
    } else {
        $error = "Compilare tutti i campi";
    }
}
?>


<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="../css/login.css">
    <title>Login</title>
</head>

<body>
    <nav>
        <a href="index.html"><img src="../img/ebay_logo.png"></a>
    </nav>
    <div class="login_container">
        <h2>Ciao</h2>
        <h5>Accedi a ebay o <a href="signin.php">crea un account</a></h5>
        <?php
        if (isset($error))
            echo ('<p id="error-msg"><span class="material-symbols-outlined">error</span>' . $error . '</p>');
        ?>
        <form action="" method="post">
            <div class="input-group">
                <label for="username_input">Email o nome utente</label>
                <input type="text" name="username" id="username_input" placeholder="Email o nome utente">
            </div>
            <div class="input-group">
                <label for="password_input">password</label>
                <input type="password" name="password" id="password_input" placeholder="Inserisci la password">
            </div>
            <button type="submit" name="login" value="1">Continua</button>
        </form>
    </div>
    <footer>
        <p>Copyright © 1995-2024 eBay Inc. Tutti i diritti riservati. Accessibilità, Accordo per gli utenti, Privacy, Condizioni di utilizzo dei servizi di pagamento, Cookie e AdChoice</p>
    </footer>
    <script src="../js/loginsignin.js"></script>
</body>

</html>