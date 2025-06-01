<?php
// 修改學歷資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $EduBG_ID = $_POST['EduBG_ID'] ?? '';
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $EduBG_University = $_POST['EduBG_University'] ?? '';
    $EduBG_Department = $_POST['EduBG_Department'] ?? '';
    $EduBG_Degree = $_POST['EduBG_Degree'] ?? '';

    if (empty($EduBG_ID) || empty($Prof_ID) || empty($EduBG_University) || empty($EduBG_Department) || empty($EduBG_Degree)) {
        $error = "所有欄位都是必填的！";
    } else {
        $stmt = $mysqli->prepare("UPDATE EducationalBackground SET Prof_ID=?, EduBG_University=?, EduBG_Department=?, EduBG_Degree=? WHERE EduBG_ID=?");
        $stmt->bind_param("ssssi", $Prof_ID, $EduBG_University, $EduBG_Department, $EduBG_Degree, $EduBG_ID);
        if ($stmt->execute()) {
            $success = "修改成功！";
        } else {
            $error = "修改失敗！";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $EduBG_ID = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM EducationalBackground WHERE EduBG_ID = ?");
    $stmt->bind_param("i", $EduBG_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        $error = "找不到該學歷";
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $error = "未指定學歷編號";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>修改學歷資料</title>
    <style>
        body { font-family: "Microsoft JhengHei", Arial, sans-serif; background: #f8f9fa; }
        .edit-form {
            background: #fff; padding: 20px; margin: 40px auto; width: 350px;
            border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        label { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="email"], input[type="date"] {
            width: 95%; padding: 6px 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px;
        }
        select { width: 100%; padding: 6px 8px; border-radius: 4px; border: 1px solid #ccc; margin-top: 4px; }
        button {
            margin-top: 10px; padding: 8px 20px; background: #007bff; color: #fff;
            border: none; border-radius: 4px; cursor: pointer; font-size: 1em;
        }
        button:hover { background: #0056b3; }
        .msg-success { color: #28a745; margin-bottom: 12px; }
        .msg-error { color: #dc3545; margin-bottom: 12px; }
        .back-link { margin-left: 10px; }
    </style>
    <script>
    function showResultAndRedirect(msg, isSuccess, redirectUrl) {
        alert(msg);
        if (isSuccess && redirectUrl) {
            window.location.href = redirectUrl;
        }
    }
    </script>
</head>
<body>
    <form class="edit-form" action="edu_edit.php" method="post" onsubmit="return true;">
        <h2>修改學歷資料</h2>
        <?php if ($success): ?>
            <script>showResultAndRedirect('修改成功！', true, 'dashboard.php?tab=edu');</script>
        <?php elseif ($error): ?>
            <script>showResultAndRedirect('<?= $error ?>', false);</script>
        <?php elseif (isset($row)): ?>
            <input type="hidden" name="EduBG_ID" value="<?= htmlspecialchars($row['EduBG_ID']) ?>">
            <label>教師編號：<input type="text" name="Prof_ID" value="<?= htmlspecialchars($row['Prof_ID']) ?>" required></label>
            <label>學校：<input type="text" name="EduBG_University" value="<?= htmlspecialchars($row['EduBG_University']) ?>" required></label>
            <label>系所：<input type="text" name="EduBG_Department" value="<?= htmlspecialchars($row['EduBG_Department']) ?>" required></label>
            <label>學位：<input type="text" name="EduBG_Degree" value="<?= htmlspecialchars($row['EduBG_Degree']) ?>" required></label>
            <button type="submit">儲存修改</button>
            <a href="dashboard.php?tab=edu" class="back-link">取消</a>
        <?php endif; ?>
    </form>
</body>
</html>
