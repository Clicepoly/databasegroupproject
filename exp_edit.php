<?php
// 修改經歷資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Experience_ID = $_POST['Experience_ID'] ?? '';
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Experience_type = $_POST['Experience_type'] ?? '';
    $Experience_position = $_POST['Experience_position'] ?? '';

    if (empty($Experience_ID) || empty($Prof_ID) || empty($Experience_type) || empty($Experience_position)) {
        $error = "所有欄位都是必填的！";
    } else {
        $stmt = $mysqli->prepare("UPDATE Experience SET Prof_ID=?, Experience_type=?, Experience_position=? WHERE Experience_ID=?");
        $stmt->bind_param("sssi", $Prof_ID, $Experience_type, $Experience_position, $Experience_ID);
        if ($stmt->execute()) {
            $success = "修改成功！";
        } else {
            $error = "修改失敗！";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $Experience_ID = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM Experience WHERE Experience_ID = ?");
    $stmt->bind_param("i", $Experience_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        $error = "找不到該經歷";
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $error = "未指定經歷編號";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>修改經歷資料</title>
    <style>
        body { font-family: "Microsoft JhengHei", Arial, sans-serif; background: #f8f9fa; }
        .edit-form {
            background: #fff; padding: 20px; margin: 40px auto; width: 350px;
            border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        label { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="email"] {
            width: 95%; padding: 6px 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px;
        }
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
    <form class="edit-form" action="exp_edit.php" method="post" onsubmit="return true;">
        <h2>修改經歷資料</h2>
        <?php if ($success): ?>
            <script>showResultAndRedirect('修改成功！', true, 'dashboard.php?tab=exp');</script>
        <?php elseif ($error): ?>
            <script>showResultAndRedirect('<?= $error ?>', false);</script>
        <?php elseif (isset($row)): ?>
            <input type="hidden" name="Experience_ID" value="<?= htmlspecialchars($row['Experience_ID']) ?>">
            <label>教師編號：<input type="text" name="Prof_ID" value="<?= htmlspecialchars($row['Prof_ID']) ?>" required></label>
            <label>經歷類型：<input type="text" name="Experience_type" value="<?= htmlspecialchars($row['Experience_type']) ?>" required></label>
            <label>職稱/職位：<input type="text" name="Experience_position" value="<?= htmlspecialchars($row['Experience_position']) ?>" required></label>
            <button type="submit">儲存修改</button>
            <a href="dashboard.php?tab=exp" class="back-link">取消</a>
        <?php endif; ?>
    </form>
</body>
</html>
