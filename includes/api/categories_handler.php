<?php
require "../template/dbconfig.php";
session_start();
if (!isset($_SESSION["logged_user_id"])) {
    header("Location: login.php");
}

if (isset($_GET["action"])) {
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    switch($_GET["action"]){
        case "sync":
            sync_categoris_from_api($conn);
            break;
        case "get_categories":
            if(isset($_GET["id_category"])){
                $id_category = mysqli_real_escape_string($conn, $_GET["id_category"]);
                get_categories($conn, $id_category);
            }
            break;
        case "add_categories":
            if(isset($_GET["nome"])){
                $nome = mysqli_real_escape_string($conn, $_GET["nome"]);
                add_categories($conn, $nome);
            }
            break;
        case "remove_categories":
            if(isset($_GET["id_category"])){
                $id_category = mysqli_real_escape_string($conn, $_GET["id_category"]);
                remove_categories($conn, $id_category);
            }
            break;
        case "list_categories":
            list_categories($conn);
            break;
        default:
            break;
    }
}

function sync_categoris_from_api($conn){
    $url = "https://fakestoreapi.com/products/categories";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $categories = curl_exec($ch);
    if (curl_errno($ch)) {
        returnJsonResERROR(curl_error($ch));
        //echo json_encode('Errore:' . curl_error($ch));
    }else {
        $categories = json_decode($categories);
        $sql_multi_cat= "";
        foreach($categories as $category){
            $nome = mysqli_real_escape_string($conn, $category);
            $sql_multi_cat .= "INSERT IGNORE INTO categories (nome) VALUES ('".$nome."');";
        }
        if(mysqli_multi_query($conn, $sql_multi_cat)){
            returnJsonResOK("Sync successful");
        }else{
            returnJsonResOK(mysqli_error($conn));
        }
    }
    curl_close($ch);
    mysqli_close($conn);
}

function list_categories($conn)
{
    $sql = "SELECT * FROM categories";
    if($res = mysqli_query($conn, $sql)){
        if(mysqli_num_rows($res) > 0){
            $rows = [];
            while($row = mysqli_fetch_assoc($res)){
                $row["id"] = (int)$row["id"];
                $rows[] = $row;
            }
            returnJsonResOK($rows);
        }else{
            returnJsonResERROR("No data available");
        }
    }else{
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function get_categories($conn ,$id_category)
{
    $sql = "SELECT * FROM categories WHERE id = ".$id_category;
    if($res = mysqli_query($conn, $sql)){
        if(mysqli_num_rows($res) > 0){
            $rows = mysqli_fetch_assoc($res);
            returnJsonResOK($rows);
        }else{
            //code for server error
            returnJsonResERROR("No data available");
        }
    }else{
        //code for server error
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}
function add_categories($conn, $nome)
{   
    $sql = "INSERT INTO categories (id, nome) VALUES ('".$nome."')";
    if($res = mysqli_query($conn, $sql)){
        returnJsonResOK("Categories added name: ".$nome);
    }else{
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function remove_categories($conn, $id_category)
{
    $sql = "DELETE FROM categories WHERE id = '".$id_category."'";
    if($res = mysqli_query($conn, $sql)){
        returnJsonResOK("Categories removed id_category: ".$id_category);
    }else{
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function returnJsonResOK($data){
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}

function returnJsonResERROR($data){
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}