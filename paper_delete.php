<?php
// 論文刪除功能
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM Paper WHERE Paper_ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=paper");
        exit();
    } else {
        echo "刪除失敗！";
    }
    $stmt->close();
} else {
    echo "缺少論文ID！";
}
