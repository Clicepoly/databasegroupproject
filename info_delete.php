<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
?>
<?php
include('db.php');

$id = $_GET['id'] ?? '';
$success = '';
$error = '';

// 刪除教師時一併刪除圖片
if ($id) {
    $stmt_img = $mysqli->prepare("SELECT Prof_Image FROM teachers WHERE Prof_ID = ?");
    $stmt_img->bind_param("s", $id);
    $stmt_img->execute();
    $stmt_img->bind_result($oldImg);
    $stmt_img->fetch();
    $stmt_img->close();
    if ($oldImg && file_exists($oldImg)) unlink($oldImg);
    $stmt = $mysqli->prepare("DELETE FROM teachers WHERE Prof_ID = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        $success = "刪除成功！";
    } else {
        $error = "刪除失敗！";
    }
    $stmt->close();
} else {
    $error = "未指定教師編號";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>刪除教師資料</title>
    <style>
        body { font-family: "Microsoft JhengHei", Arial, sans-serif; background: #f8f9fa; }
        .msg-success { color: #28a745; margin: 40px auto; width: 350px; }
        .msg-error { color: #dc3545; margin: 40px auto; width: 350px; }
        .back-link { display: block; margin: 20px auto; width: 350px; }
    </style>
</head>
<body>
    <?php if ($success): ?>
        <div class="msg-success"><?= $success ?></div>
        <a href="dashboard.php" class="back-link">回首頁</a>
    <?php else: ?>
        <div class="msg-error"><?= $error ?></div>
        <a href="dashboard.php" class="back-link">回首頁</a>
    <?php endif; ?>
</body>
</html>