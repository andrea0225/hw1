<?php
require "dbconfig.php";
session_start();
if (isset($_SESSION["logged_user_id"])) {
} else {
    header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MHW3</title>
    <link rel="stylesheet" href="../css/mhw3.css">
    <!--Utilizzata per le icone della pagina-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body style="overflow: hidden;">
    <div class="pay-status-container">
        <img src="../img/success-ok.png" alt="" srcset="">
        <h3>Pagamento effettuato con successo</h3>
        <h4><a href="index.html">Torna allo store</a></h4>
    </div>
</body>

</html>