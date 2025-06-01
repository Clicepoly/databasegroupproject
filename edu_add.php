<?php
// 新增學歷資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $EduBG_University = $_POST['EduBG_University'] ?? '';
    $EduBG_Department = $_POST['EduBG_Department'] ?? '';
    $EduBG_Degree = $_POST['EduBG_Degree'] ?? '';

    if (empty($Prof_ID) || empty($EduBG_University) || empty($EduBG_Department) || empty($EduBG_Degree)) {
        echo "所有欄位都是必填的！";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO EducationalBackground (Prof_ID, EduBG_University, EduBG_Department, EduBG_Degree) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("ssss", $Prof_ID, $EduBG_University, $EduBG_Department, $EduBG_Degree);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=edu");
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
    <title>新增學歷</title>
</head>
<body>
    <h1>新增學歷</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>學校：<input type="text" name="EduBG_University" required></label><br>
        <label>系所：<input type="text" name="EduBG_Department" required></label><br>
        <label>學位：<input type="text" name="EduBG_Degree" required></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
