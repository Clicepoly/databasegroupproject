<?php
// 新增演講資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Speech_Name = $_POST['Speech_Name'] ?? '';
    $Speech_Audience = $_POST['Speech_Audience'] ?? '';
    $Speech_Date = $_POST['Speech_Date'] ?? '';

    if (empty($Prof_ID) || empty($Speech_Name) || empty($Speech_Audience) || empty($Speech_Date)) {
        echo "所有欄位都是必填的！";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO Speech (Prof_ID, Speech_Name, Speech_Audience, Speech_Date) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("ssss", $Prof_ID, $Speech_Name, $Speech_Audience, $Speech_Date);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=speech");
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
    <title>新增演講</title>
</head>
<body>
    <h1>新增演講</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>演講名稱：<input type="text" name="Speech_Name" required></label><br>
        <label>對象/場合：<input type="text" name="Speech_Audience" required></label><br>
        <label>日期：<input type="date" name="Speech_Date" required></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
