<?php
// 新增教材與作品資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $TeachMat_Author = $_POST['TeachMat_Author'] ?? '';
    $TeachMat_Name = $_POST['TeachMat_Name'] ?? '';
    $TeachMat_Publisher = $_POST['TeachMat_Publisher'] ?? '';

    if (empty($Prof_ID) || empty($TeachMat_Author) || empty($TeachMat_Name) || empty($TeachMat_Publisher)) {
        echo "所有欄位都是必填的！";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO TeachingMaterials (Prof_ID, TeachMat_Author, TeachMat_Name, TeachMat_Publisher) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("ssss", $Prof_ID, $TeachMat_Author, $TeachMat_Name, $TeachMat_Publisher);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=teachmat");
        exit();
    } else {
        echo "新增失敗！";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>新增教材與作品</title>
</head>
<body>
    <h1>新增教材與作品</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>作者：<input type="text" name="TeachMat_Author" required></label><br>
        <label>教材/作品名稱：<input type="text" name="TeachMat_Name" required></label><br>
        <label>出版社/發表單位：<input type="text" name="TeachMat_Publisher" required></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
