<?php
// 新增經歷資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Experience_type = $_POST['Experience_type'] ?? '';
    $Experience_position = $_POST['Experience_position'] ?? '';

    if (empty($Prof_ID) || empty($Experience_type) || empty($Experience_position)) {
        echo "所有欄位都是必填的！";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO Experience (Prof_ID, Experience_type, Experience_position) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("sss", $Prof_ID, $Experience_type, $Experience_position);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=exp");
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
    <title>新增經歷</title>
</head>
<body>
    <h1>新增經歷</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>經歷類型：<input type="text" name="Experience_type" required></label><br>
        <label>職稱/職位：<input type="text" name="Experience_position" required></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
