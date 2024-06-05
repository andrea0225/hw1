<?php
require "../template/dbconfig.php";
session_start();
if (!isset($_SESSION["logged_user_id"])) {
    header("Location: login.php");
}
/** inserire funzioni per la gestione degli item selezione dal db locale e update degli item tramite api magari 
 * chiamare la funzione che esegue l'update degli item ogni qualvolta si esegua il login */

if (isset($_GET["action"])) {
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    switch ($_GET["action"]) {
        case "sync":
            sync_api_db_items($conn);
            break;
        case "list_items":
            list_items($conn);
            break;
        case "get_item":
            if (isset($_GET["category"])) {
                if (isset($_GET["id_user"])) {
                    $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
                } else {
                    $id_user = $_SESSION["logged_user_id"];
                }
                $category = mysqli_real_escape_string($conn, $_GET["category"]);
                get_item_by_category($conn, $category, $id_user);
            } else {
                if(isset($_GET["id_item"])){
                    $item_id = mysqli_real_escape_string($conn, $_GET["id_item"]);
                    get_item($conn, $item_id);
                }
            }
            break;
        case "add_item":
            if(isset($_GET["category"]) && isset($_GET["title"]) && isset($_GET["price"]) && isset($_GET["description"]) && isset($_GET["image_path"])){
                $category = mysqli_real_escape_string($conn, $_GET["category"]);
                $title = mysqli_real_escape_string($conn, $_GET["title"]);
                $price = mysqli_real_escape_string($conn, $_GET["price"]);
                $description = mysqli_real_escape_string($conn, $_GET["description"]);
                $image_path = mysqli_real_escape_string($conn, $_GET["image_path"]);
                add_item($conn, $category, $title, $price, $description, $image_path);
            }
            break;
        case "del_item":
            if(isset($_GET["item_id"])){
                $item_id = mysqli_real_escape_string($conn, $_GET["item_id"]);
                del_item($conn, $item_id);
            }
            break;
        case "search":
            if(isset($_GET["filter"])){
                $filter = mysqli_real_escape_string($conn, $_GET["filter"]); 
                $id_user = $_SESSION["logged_user_id"];
                search($conn, $filter, $id_user);
            }
            break;
        default:
            returnJsonResOK("No action match");
            mysqli_close($conn);
            break;
    }
}

function sync_api_db_items($conn)
{
    $url = "https://fakestoreapi.com/products";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $items = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode('Errore:' . curl_error($ch));
    } else {
        $items = json_decode($items);
        $sql_multi_items = "";
        foreach ($items as $item) {
            $category = mysqli_real_escape_string($conn, $item->category);
            $sql_category = "SELECT * FROM categories WHERE nome = '" . $category . "'";
            if ($res_cat = mysqli_query($conn, $sql_category)) {
                if (mysqli_num_rows($res_cat) > 0) {
                    $row = mysqli_fetch_assoc($res_cat);
                    $id_category = $row["id"];
                } else {
                    $sql_category = "INSERT INTO categories (nome) VALUES ('" . $category . "')";
                    if (mysqli_query($conn, $sql_category)) {
                        $id_category = mysqli_insert_id($conn);
                    } else {
                        echo json_encode(mysqli_error($conn));
                        exit;
                    }
                }
            } else {
                echo json_encode(mysqli_error($conn));
                exit;
            }
            $title = mysqli_real_escape_string($conn, $item->title);
            $price = mysqli_real_escape_string($conn, $item->price);
            $description = mysqli_real_escape_string($conn, $item->description);
            $image = mysqli_real_escape_string($conn, $item->image);
            $sql_multi_items .= "INSERT IGNORE INTO items VALUES (" . $item->id . ", " . $id_category . ", '" . $title . "', " . $price . ", '" . $description . "', '" . $image . "');";
        }
        if (mysqli_multi_query($conn, $sql_multi_items)) {
            returnJsonResOK("Sync successful");
        } else {
            returnJsonResERROR(mysqli_error($conn));
        }
    }
    curl_close($ch);
    mysqli_close($conn);
}

function search($conn, $filter ,$id_user)
{
    $filter = str_replace(" ", "%", $filter);
    $sql = "SELECT items.*, favourites.id AS preferito FROM items INNER JOIN categories ON categories.id = items.id_category LEFT JOIN favourites ON favourites.id_item = items.id AND favourites.id_user = '".$id_user."' WHERE title LIKE '%".$filter."%' OR description LIKE '%".$filter."%' OR image_path LIKE '%".$filter."%' OR title LIKE '%".$filter."%'";
    if ($res = mysqli_query($conn, $sql)) {
        if(mysqli_num_rows($res) > 0){
            $rows = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $row["id"] = (int)$row["id"];
                $row["id_category"] = (int)$row["id_category"];
                $row["price"] = (float)$row["price"];
                $rows[] = $row;
            }
            returnJsonResOK($rows);
        }else{
            returnJsonResERROR("no items found");
        }
    } else {
        //code for server error
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function list_items($conn)
{
    $sql = "SELECT * FROM items";
    if ($res = mysqli_query($conn, $sql)) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $row["id"] = (int)$row["id"];
            $row["id_category"] = (int)$row["id_category"];
            $row["price"] = (float)$row["price"];
            $rows[] = $row;
        }
        returnJsonResOK($rows);
    } else {
        //code for server error
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function get_item($conn, $item_id)
{
    $sql = "SELECT * FROM items WHERE id='" . $item_id . "'";
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $row["id"] = (int)$row["id"];
            $row["id_category"] = (int)$row["id_category"];
            $row["price"] = (float)$row["price"];
            returnJsonResOK($row);
        } else {
            returnJsonResERROR("no item fuond");
        }
    } else {
        //code for server error
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function get_item_by_category($conn, $item_category, $id_user)
{
    $sql = "SELECT items.*, favourites.id AS preferito FROM items INNER JOIN categories ON categories.id = items.id_category LEFT JOIN favourites ON favourites.id_item = items.id AND favourites.id_user = '".$id_user."' WHERE categories.nome = '" . $item_category . "'";
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $rows = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $row["id"] = (int)$row["id"];
                $row["id_category"] = (int)$row["id_category"];
                $row["price"] = (float)$row["price"];
                $rows[] = $row;
            }
            returnJsonResOK($rows);
        } else {
            returnJsonResERROR("no items found");
        }
    } else {
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function add_item($conn, $category, $title, $price, $description, $image_path)
{
    $sql = "SELECT * FROM categories WHERE nome = '" . $category . "'";
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $sql = "INSERT INTO items VALUES ('" . $row["id"] . "', '" . $category . "', '" . $title . "', '" . $price . "', '" . $description . "', '" . $image_path . "')";
            if (mysqli_query($conn, $sql)) {
                http_response_code(202);
            } else {
                returnJsonResERROR(mysqli_error($conn));
            }
        } else {
            returnJsonResERROR(mysqli_error($conn));
        }
    } else {
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function del_item($conn, $id_item)
{
    $sql = "DELETE FROM items WHERE id = '" . $id_item . "'";
    if ($res = mysqli_query($conn, $sql)) {
        returnJsonResOK("Item deleted id_item: ".$id_item);
    } else {
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
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