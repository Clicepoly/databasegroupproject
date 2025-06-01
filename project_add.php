<?php
// 新增計畫資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Project_Name = $_POST['Project_Name'] ?? '';
    $Project_Duration = $_POST['Project_Duration'] ?? '';
    $Project_Type = $_POST['Project_Type'] ?? '';
    $Project_TakenPosition = $_POST['Project_TakenPosition'] ?? '';

    if (empty($Prof_ID) || empty($Project_Name) || empty($Project_Duration) || empty($Project_Type) || empty($Project_TakenPosition)) {
        echo "所有欄位都是必填的！";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO Project (Prof_ID, Project_Name, Project_Duration, Project_Type, Project_TakenPosition) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("sssss", $Prof_ID, $Project_Name, $Project_Duration, $Project_Type, $Project_TakenPosition);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=project");
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
    <title>新增計畫</title>
</head>
<body>
    <h1>新增計畫</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>計畫名稱：<input type="text" name="Project_Name" required></label><br>
        <label>計畫期間：<input type="text" name="Project_Duration" required></label><br>
        <label>計畫類型：
            <select name="Project_Type" required>
                <option value="">--請選擇--</option>
                <option value="國科會">國科會</option>
                <option value="產學合作">產學合作</option>
            </select>
        </label><br>
        <label>擔任職務：<input type="text" name="Project_TakenPosition" required></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
