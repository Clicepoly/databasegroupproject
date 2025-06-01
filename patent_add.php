<?php
// 新增專利資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Patent_Type = $_POST['Patent_Type'] ?? '';
    $Patent_Name = $_POST['Patent_Name'] ?? '';
    $Patent_Term = $_POST['Patent_Term'] ?? '';


    if (empty($Patent_Name)) {
        echo "name";
        exit();
    }
    if (empty($Prof_ID) || empty($Patent_Type) || empty($Patent_Name) || empty($Patent_Term)) {
        echo "所有欄位都是必填的！";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO Patent (Prof_ID, Patent_Type, Patent_Name, Patent_Term) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("ssss", $Prof_ID, $Patent_Type, $Patent_Name, $Patent_Term);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=patent");
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
    <title>新增專利</title>
</head>
<body>
    <h1>新增專利</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>專利類型：<input type="text" name="Patent_Type" required></label><br>
        <label>專利名稱/內容：<input type="text" name="Patent_Name" required></label><br>
        <label>專利時間：<input type="text" name="Patent_Term" required></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
