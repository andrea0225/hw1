<?php
require "dbconfig.php";
session_start();
if (isset($_POST["signin"])) {
    if (
        !empty($_POST["nome"]) && !empty($_POST["cognome"]) && !empty($_POST["username"]) && !empty($_POST["cellulare"]) && !empty($_POST["email"]) && !empty($_POST["password"]) &&
        !empty($_POST["pwdconfirm"])
    ) {
        $error = array();
        $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $query = "SELECT id FROM users WHERE username = '$username'";
        $res = mysqli_query($conn, $query);
        if (mysqli_num_rows($res) > 0) {
            $error[] = "Username in uso";
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $error[] = "Email non valida";
        } else {
            $email = mysqli_real_escape_string($conn, strtolower($_POST['email']));
            $query = "SELECT email FROM users WHERE email = '$email'";
            $res = mysqli_query($conn, $query);
            if (mysqli_num_rows($res) > 0) {
                $error[] = "Email già utilizzata";
            }
        }

        if(!preg_match("^(([+])39)?((3[1-9][0-9]))(\d{7})$^", $_POST['cellulare'])){
            $error[] = "Cellulare non valido es. +393925570499";
        }else{
            $cellulare = mysqli_real_escape_string($conn, $_POST['cellulare']);
            $query = "SELECT id FROM users WHERE cellulare = '$cellulare'";
            $res = mysqli_query($conn, $query);
            if (mysqli_num_rows($res) > 0) {
                $error[] = "Cellulare in uso";
            }
        }
        

        if (strlen($_POST["password"]) < 8) {
            $error[] = "Caratteri password insufficienti min:8";
        } 

        if (strcmp($_POST["password"], $_POST["pwdconfirm"]) != 0) {
            $error[] = "Le password non coincidono";
        }

        if (count($error) == 0) {
            $nome = mysqli_real_escape_string($conn, $_POST['nome']);
            $cognome = mysqli_real_escape_string($conn, $_POST['cognome']);
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $cellulare = mysqli_real_escape_string($conn, $_POST['cellulare']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $password = password_hash($password, PASSWORD_BCRYPT);

            $query = "INSERT INTO users(nome, cognome, email, cellulare, username, password, ragione_sociale) VALUES('$nome', '$cognome', '$email', '$cellulare', '$username', '$password', '$ragione_sociale')";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION["_username"] = $_POST["username"];
                $_SESSION["user_id"] = mysqli_insert_id($conn);
                mysqli_close($conn);
                header("Location: home.php");
                exit;
            } else {
                $error[] = "Errore di connessione al Database";
            }
        }
        mysqli_close($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="../css/signin.css">
    <title>Signin</title>
</head>

<body>
    <nav>
        <a href="index.html"><img src="../img/ebay_logo.png"></a>
        <p>Hai Già un account? <a href="login.php">Accedi</a></p>
    </nav>
    <div class="signin_container">
        <section class="left">
            <img src="../img/signin.png" alt="">
        </section>
        <section class="right">
            <div class="form_container">
                <h1>Crea un account</h1>
                <?php
                if (isset($error) && count($error) > 0) foreach($error as $e) echo ('<p id="error-msg"><span class="material-symbols-outlined">error</span>' . $e . '</p>');
                ?>
                <form action="" method="post">
                    <div class="row">
                        <div class="input-group">
                            <label for="input_nome">Nome</label>
                            <input type="text" name="nome" id="input_nome" placeholder="Nome" required>
                        </div>
                        <div class="input-group">
                            <label for="input_cognome">Cognome</label>
                            <input type="text" name="cognome" id="input_cognome" placeholder="Cognome" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="input_username">Username</label>
                        <input type="text" name="username" id="input_username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <label for="input_cellulare">Cellulare</label>
                        <input type="text" name="cellulare" id="input_cellulare" placeholder="Cellulare" required>
                    </div>
                    <div class="input-group">
                        <label for="input_email">Email</label>
                        <input type="email" name="email" id="input_email" placeholder="Email" required>
                    </div>
                    <div class="input-group">
                        <label for="input_password">Password</label>
                        <input type="password" name="password" id="input_password" placeholder="Password" required>
                    </div>
                    <div class="input-group">
                        <label for="input_confermapwd">Conferma Password</label>
                        <input type="password" name="pwdconfirm" id="input_confermapwd" placeholder="Conferma Password" required>
                    </div>
                    <p id="disclaimer">
                        Ti invieremo regolarmente delle email con offerte relative ai nostri servizi. Puoi annullare l'iscrizione in qualsiasi momento.
                        Selezionando Crea un account privato, accetti il nostro Accordo per gli utenti e confermi di aver letto la nostra Informativa Privacy.
                    </p>
                    <button type="submit" name="signin" value="1">Crea account</button>
                </form>
            </div>
        </section>
    </div>
    <footer>
        <p>Copyright © 1995-2024 eBay Inc. Tutti i diritti riservati. Accessibilità, Accordo per gli utenti, Privacy, Condizioni di utilizzo dei servizi di pagamento, Cookie e AdChoice</p>
    </footer>
    <script src="../js/loginsignin.js"></script>
</body>

</html>