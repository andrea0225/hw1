<?php
require "../template/dbconfig.php";
session_start();
if (!isset($_SESSION["logged_user_id"])) {
    header("Location: login.php");
}

if (isset($_GET["action"])) {
    //$conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    switch ($_GET["action"]) {
        case "get_logged_user":
            get_logged_user($_SESSION["logged_user_id"]);
            break;
        default:
            returnJsonResOK("No action match");
            break;
    }
}

function get_logged_user($id_user)
{
    returnJsonResOK($id_user);
}


function returnJsonResOK($data)
{
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}

function returnJsonResERROR($data){
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}
