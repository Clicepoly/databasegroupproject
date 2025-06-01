<?php
// 新增獎項資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Award_Advisee = $_POST['Award_Advisee'] ?? '';
    $Award_ProjectName = $_POST['Award_ProjectName'] ?? '';
    $Award_CompName_Position = $_POST['Award_CompName_Position'] ?? '';
    $Award_Date = $_POST['Award_Date'] ?? '';
    $Award_organizer = $_POST['Award_organizer'] ?? '';

    if (empty($Prof_ID) || empty($Award_Advisee) || empty($Award_ProjectName) || empty($Award_CompName_Position) || empty($Award_Date) || empty($Award_organizer)) {
        echo "所有欄位都是必填的！";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO Award (Prof_ID, Award_Advisee, Award_ProjectName, Award_CompName_Position, Award_Date, Award_organizer) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("ssssss", $Prof_ID, $Award_Advisee, $Award_ProjectName, $Award_CompName_Position, $Award_Date, $Award_organizer);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=award");
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
    <title>新增獎項</title>
</head>
<body>
    <h1>新增獎項</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>學生姓名：<input type="text" name="Award_Advisee" required></label><br>
        <label>作品/計畫名稱：<input type="text" name="Award_ProjectName" required></label><br>
        <label>競賽名稱與名次：<input type="text" name="Award_CompName_Position" required></label><br>
        <label>得獎日期：<input type="date" name="Award_Date" required></label><br>
        <label>主辦單位：<input type="text" name="Award_organizer" required></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
