<?php
// 修改教材與作品資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $TeachMat_ID = $_POST['TeachMat_ID'] ?? '';
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $TeachMat_Author = $_POST['TeachMat_Author'] ?? '';
    $TeachMat_Name = $_POST['TeachMat_Name'] ?? '';
    $TeachMat_Publisher = $_POST['TeachMat_Publisher'] ?? '';

    if (empty($TeachMat_ID) || empty($Prof_ID) || empty($TeachMat_Author) || empty($TeachMat_Name) || empty($TeachMat_Publisher)) {
        $error = "所有欄位都是必填的！";
    } else {
        $stmt = $mysqli->prepare("UPDATE TeachingMaterials SET Prof_ID=?, TeachMat_Author=?, TeachMat_Name=?, TeachMat_Publisher=? WHERE TeachMat_ID=?");
        $stmt->bind_param("ssssi", $Prof_ID, $TeachMat_Author, $TeachMat_Name, $TeachMat_Publisher, $TeachMat_ID);
        if ($stmt->execute()) {
            $success = "修改成功！";
        } else {
            $error = "修改失敗！";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $TeachMat_ID = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM TeachingMaterials WHERE TeachMat_ID = ?");
    $stmt->bind_param("i", $TeachMat_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        $error = "找不到該教材/作品";
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $error = "未指定教材/作品編號";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>修改教材與作品資料</title>
    <style>
        body { font-family: "Microsoft JhengHei", Arial, sans-serif; background: #f8f9fa; }
        .edit-form {
            background: #fff; padding: 20px; margin: 40px auto; width: 350px;
            border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        label { display: block; margin-bottom: 10px; }
        input[type="text"] {
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
    <form class="edit-form" action="teachmat_edit.php" method="post" onsubmit="return true;">
        <h2>修改教材與作品資料</h2>
        <?php if ($success): ?>
            <script>showResultAndRedirect('修改成功！', true, 'dashboard.php?tab=teachmat');</script>
        <?php elseif ($error): ?>
            <script>showResultAndRedirect('<?= $error ?>', false);</script>
        <?php elseif (isset($row)): ?>
            <input type="hidden" name="TeachMat_ID" value="<?= htmlspecialchars($row['TeachMat_ID']) ?>">
            <label>教師編號：<input type="text" name="Prof_ID" value="<?= htmlspecialchars($row['Prof_ID']) ?>" required></label>
            <label>作者：<input type="text" name="TeachMat_Author" value="<?= htmlspecialchars($row['TeachMat_Author']) ?>" required></label>
            <label>教材/作品名稱：<input type="text" name="TeachMat_Name" value="<?= htmlspecialchars($row['TeachMat_Name']) ?>" required></label>
            <label>出版社/發表單位：<input type="text" name="TeachMat_Publisher" value="<?= htmlspecialchars($row['TeachMat_Publisher']) ?>" required></label>
            <button type="submit">儲存修改</button>
            <a href="dashboard.php?tab=teachmat" class="back-link">取消</a>
        <?php endif; ?>
    </form>
</body>
</html>
