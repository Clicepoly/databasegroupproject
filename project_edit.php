<?php
// 修改計畫資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Project_ID = $_POST['Project_ID'] ?? '';
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Project_Name = $_POST['Project_Name'] ?? '';
    $Project_Duration = $_POST['Project_Duration'] ?? '';
    $Project_Type = $_POST['Project_Type'] ?? '';
    $Project_TakenPosition = $_POST['Project_TakenPosition'] ?? '';

    if (empty($Project_ID) || empty($Prof_ID) || empty($Project_Name) || empty($Project_Duration) || empty($Project_Type) || empty($Project_TakenPosition)) {
        $error = "所有欄位都是必填的！";
    } else {
        $stmt = $mysqli->prepare("UPDATE Project SET Prof_ID=?, Project_Name=?, Project_Duration=?, Project_Type=?, Project_TakenPosition=? WHERE Project_ID=?");
        $stmt->bind_param("sssssi", $Prof_ID, $Project_Name, $Project_Duration, $Project_Type, $Project_TakenPosition, $Project_ID);
        if ($stmt->execute()) {
            $success = "修改成功！";
        } else {
            $error = "修改失敗！";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $Project_ID = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM Project WHERE Project_ID = ?");
    $stmt->bind_param("i", $Project_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        $error = "找不到該計畫";
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $error = "未指定計畫編號";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>修改計畫資料</title>
    <style>
        body { font-family: "Microsoft JhengHei", Arial, sans-serif; background: #f8f9fa; }
        .edit-form {
            background: #fff; padding: 20px; margin: 40px auto; width: 350px;
            border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        label { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="date"] {
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
    <form class="edit-form" action="project_edit.php" method="post" onsubmit="return true;">
        <h2>修改計畫資料</h2>
        <?php if ($success): ?>
            <script>showResultAndRedirect('修改成功！', true, 'dashboard.php?tab=project');</script>
        <?php elseif ($error): ?>
            <script>showResultAndRedirect('<?= $error ?>', false);</script>
        <?php elseif (isset($row)): ?>
            <input type="hidden" name="Project_ID" value="<?= htmlspecialchars($row['Project_ID']) ?>">
            <label>教師編號：<input type="text" name="Prof_ID" value="<?= htmlspecialchars($row['Prof_ID']) ?>" required></label>
            <label>計畫名稱：<input type="text" name="Project_Name" value="<?= htmlspecialchars($row['Project_Name']) ?>" required></label>
            <label>計畫期間：<input type="text" name="Project_Duration" value="<?= htmlspecialchars($row['Project_Duration']) ?>" required></label>
            <label>計畫類型：
                <select name="Project_Type" required>
                    <option value="國科會" <?= $row['Project_Type']==='國科會'?'selected':'' ?>>國科會</option>
                    <option value="產學合作" <?= $row['Project_Type']==='產學合作'?'selected':'' ?>>產學合作</option>
                </select>
            </label>
            <label>擔任職務：<input type="text" name="Project_TakenPosition" value="<?= htmlspecialchars($row['Project_TakenPosition']) ?>" required></label>
            <button type="submit">儲存修改</button>
            <a href="dashboard.php?tab=project" class="back-link">取消</a>
        <?php endif; ?>
    </form>
</body>
</html>
