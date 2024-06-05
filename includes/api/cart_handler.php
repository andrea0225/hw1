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
        case "get_item":
            if (isset($_GET["category"])) {
                $category = mysqli_real_escape_string($conn, $_GET["category"]);
                if (isset($_GET["id_user"])) {
                    $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
                } else {
                    $id_user = $_SESSION["logged_user_id"];
                }
                get_item_by_category($conn, $category, $id_user);
            } else {
                $id_cart = mysqli_real_escape_string($conn, $_GET["id_cart"]);
                get_item($conn, $id_cart);
            }
            break;
        case "add_item":
            if (isset($_GET["id_item"]) && isset($_GET["qty"])) {
                if (isset($_GET["id_user"])) {
                    $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
                } else {
                    $id_user = $_SESSION["logged_user_id"];
                }
                $id_item = mysqli_real_escape_string($conn, $_GET["id_item"]);
                $qty = mysqli_real_escape_string($conn, $_GET["qty"]);
                add_item($conn, $id_user, $id_item, $qty);
            }
            break;
        case "del_item":
            if (isset($_GET["id_item"])) {
                if (isset($_GET["id_user"])) {
                    $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
                } else {
                    $id_user = $_SESSION["logged_user_id"];
                }
                $id_item = mysqli_real_escape_string($conn, $_GET["id_item"]);
                remove_item($conn, $id_item, $id_user);
            }
            break;
        case "list_item":
            if (isset($_GET["id_user"])) {
                $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
            } else {
                $id_user = $_SESSION["logged_user_id"];
            }
            list_item($conn, $id_user);
            break;
        case "get_total":
            if (isset($_GET["id_user"])) {
                $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
            } else {
                $id_user = $_SESSION["logged_user_id"];
            }
            get_total($conn, $id_user);
            break;
        case "updateQty":
            if (isset($_GET["id_item"]) && isset($_GET["qty"])) {
                if (isset($_GET["id_user"])) {
                    $id_user = mysqli_real_escape_string($conn, $_GET["id_user"]);
                } else {
                    $id_user = $_SESSION["logged_user_id"];
                }
                $id_item = mysqli_real_escape_string($conn, $_GET["id_item"]);
                $qty = mysqli_real_escape_string($conn, $_GET["qty"]);
                update_qty($conn, $id_user, $id_item, $qty);
            }
            break;
        default:
            returnJsonResOK("No action match");
            mysqli_close($conn);
            break;
    }
}

function get_item($conn, $id_cart)
{
    $sql = "SELECT * FROM cart WHERE id = '" . $id_cart . "'";
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            returnJsonResOK($row);
        } else {
            returnJsonResOK("no item fuond");
        }
    } else {
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function get_item_by_category($conn, $category, $id_user)
{
    $sql = "SELECT * FROM cart INNER JOIN items ON items.id = cart.id_items INNER JOIN categories ON categories.id = items.id_category WHERE categories.nome = '" . $category . "' AND cart.id_users = '" . $id_user . "'";
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $rows = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $row["id"] = (int)$row["id"];
                $row["qty"] = (int)$row["qty"];
                $row["id_user"] = (int)$row["id_user"];
                $row["id_items"] = (int)$row["id_items"];
                $rows[] = $row;
            }
            returnJsonResOK($rows);
        } else {
            returnJsonResERROR(mysqli_error($conn));
        }
    }else{
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function add_item($conn, $id_user, $id_item, $qty)
{
    try {
        $sql = "INSERT INTO cart (qty, id_users, id_items) VALUES (" . $qty . ", " . $id_user . ", " . $id_item . ") ON DUPLICATE KEY UPDATE qty=qty+".$qty;
        if ($res = mysqli_query($conn, $sql)) {
            returnJsonResOK("adding items successfull");
        } else {
            returnJsonResOK("error");
        }
    } catch (Exception $e) {
        returnJsonResERROR($e->getMessage());
    }
    mysqli_close($conn);
}

function update_qty($conn, $id_user, $id_item, $qty)
{
    $sql = "UPDATE cart SET qty=qty+'" . $qty . "' WHERE id_users = " . $id_user . " AND id_items=" . $id_item;
    if ($res = mysqli_query($conn, $sql)) {
        returnJsonResOK("qty updated");
    } else {
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function remove_item($conn, $id_item, $id_user)
{
    $sql = "DELETE FROM cart WHERE id_items = " . $id_item ." AND id_users = ".$id_user;
    if ($res = mysqli_query($conn, $sql)) {
        returnJsonResOK("Record deleted id_item: ".$id_item);
    } else {
        returnJsonResERROR(mysqli_error($conn));
    }
}

function list_item($conn, $id_user)
{
    //$sql = "SELECT * FROM cart WHERE id_users = " . $id_user;
    $sql = "SELECT items.*, cart.qty FROM items INNER JOIN cart ON cart.id_items = items.id AND cart.id_users =".$id_user;
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $rows = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $row["id"] = (int)$row["id"];
                $row["id_category"] = (int)$row["id_category"];
                $row["price"] = (float)$row["price"];
                $row["qty"] = (int)$row["qty"];
                $rows[] = $row;
            }
            returnJsonResOK($rows);
        } else {
            returnJsonResERROR("0 rows affected");
        }
    } else {
        returnJsonResERROR(mysqli_error($conn));
    }
    mysqli_close($conn);
}

function get_total($conn, $id_user)
{
    $sql = "SELECT SUM(items.price) FROM cart INNER JOIN items ON items.id = cart.id_items WHERE cart.id_users = " . $id_user;
    if ($res = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $sum = mysqli_fetch_assoc($res);
            returnJsonResOK($sum);
        } else {
            returnJsonResOK("0 rows affected");
        }
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
