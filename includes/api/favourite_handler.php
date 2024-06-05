<?php
require "../template/dbconfig.php";
session_start();
if (!isset($_SESSION["logged_user_id"])) {
    header("Location: login.php");
}

if (isset($_GET["action"])) {
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    switch ($_GET["action"]) {
        case "get_favourite":
            if (isset($_GET["id_item"]) && isset($_GET["id_user"])) {
                $id_item = mysqli_real_escape_string($conn, $_GET["id_item"]);
                $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
                get_favourite($conn, $id_item, $id_user);
            }
            break;
        case "add_favourite":
            if (isset($_GET["id_item"])) {
                if (isset($_GET["id_user"])) {
                    $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
                } else {
                    $id_user = $_SESSION["logged_user_id"];
                }
                $id_item = mysqli_real_escape_string($conn, $_GET["id_item"]);
                add_favourite($conn, $id_item, $id_user);
            }
            break;
        case "del_favourite":
            if (isset($_GET["id_item"])) {
                $id_item = mysqli_real_escape_string($conn, $_GET["id_item"]);
                remove_favourite($conn, $id_item);
            }
            break;
        case "list_favourite":
            if (isset($_GET["id_user"])) {
                $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
            } else {
                $id_user = $_SESSION["logged_user_id"];
            }
            list_favourite_user($conn, $id_user);
            break;
        default:
            break;
    }
}

function list_favourite($conn)
{
    $sql = "SELECT * FROM favourites INNER JOIN items ON items.id = favourites.id_item INNER JOIN users ON users.id = favourites.id_user INNER JOIN categories ON categories.id = items.id_category";
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $rows = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
            }
            returnJsonResOK($rows);
        }
    } else {
        //code for server error
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function list_favourite_user($conn, $id_user)
{
    $sql = "SELECT items.*, favourites.id AS preferito FROM favourites INNER JOIN items ON items.id = favourites.id_item WHERE favourites.id_user = '" . $id_user . "'";
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
            returnJsonResERROR("no item found");
        }
    } else {
        //code for server error
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}
function get_favourite($conn, $id_favourite)
{
    $sql = "SELECT * FROM favourites INNER JOIN items ON items.id = favourites.id_item WHERE favourites.id_user = " . $id_favourite;
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $rows = mysqli_fetch_assoc($res);
            returnJsonResOK($rows);
        } else {
            //code for server error
            returnJsonResERROR(mysqli_error($conn));
        }
    } else {
        //code for server error
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}
function add_favourite($conn, $id_item, $id_user)
{
    $sql = "INSERT INTO favourites (id_item, id_user) VALUES ('" . $id_item . "', '" . $id_user . "')";
    try {
        if ($res = mysqli_query($conn, $sql)) {
            returnJsonResOK("Favourite add id_favourite: " . mysqli_insert_id($conn));
        } else {
            returnJsonResERROR(mysqli_error($conn));
        }
    } catch (Exception $e) {
        returnJsonResERROR($e->getMessage());
    }
    mysqli_close($conn);
}

function remove_favourite($conn, $id_item)
{
    $sql = "DELETE FROM favourites WHERE id_item = '" . $id_item . "'";
    if ($res = mysqli_query($conn, $sql)) {
        returnJsonResOK("Favourite deleted id_item: " . $id_item);
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

function returnJsonResERROR($data)
{
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}
