<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
?>
<?php
include('db.php');

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Prof_Name = $_POST['Prof_Name'] ?? '';
    $Prof_title = $_POST['Prof_title'] ?? '';
    $Prof_EmailAddress = $_POST['Prof_EmailAddress'] ?? '';
    $Prof_ExtensionNumber = $_POST['Prof_ExtensionNumber'] ?? '';
    $Prof_ResearchFields = $_POST['Prof_ResearchFields'] ?? '';
    $Prof_Image = null;
    if (isset($_FILES['Prof_Image']) && $_FILES['Prof_Image']['error'] === UPLOAD_ERR_OK) {
        $imgTmp = $_FILES['Prof_Image']['tmp_name'];
        $imgName = basename($_FILES['Prof_Image']['name']);
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowExt = ['jpg','jpeg','png','gif','webp'];
        if (in_array($imgExt, $allowExt)) {
            $saveName = 'uploads/prof_' . $Prof_ID . '_' . time() . '.' . $imgExt;
            if (!is_dir('uploads')) mkdir('uploads');
            move_uploaded_file($imgTmp, $saveName);
            $Prof_Image = $saveName;
        }
    }
    // 處理刪除圖片
    if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
        $stmt_img = $mysqli->prepare("SELECT Prof_Image FROM teachers WHERE Prof_ID = ?");
        $stmt_img->bind_param("s", $Prof_ID);
        $stmt_img->execute();
        $stmt_img->bind_result($oldImg);
        $stmt_img->fetch();
        $stmt_img->close();
        if ($oldImg && file_exists($oldImg)) unlink($oldImg);
        $Prof_Image = '';
    }
    // 欄位檢查
    if (empty($Prof_ID) || empty($Prof_Name) || empty($Prof_title) || empty($Prof_EmailAddress) || empty($Prof_ExtensionNumber)) {
        $error = "所有欄位都是必填的！";
    } else {
        // 取得舊圖片
        $stmt_img = $mysqli->prepare("SELECT Prof_Image FROM teachers WHERE Prof_ID = ?");
        $stmt_img->bind_param("s", $Prof_ID);
        $stmt_img->execute();
        $stmt_img->bind_result($oldImg);
        $stmt_img->fetch();
        $stmt_img->close();
        // 若有新圖片上傳則更新，否則維持舊圖
        if ($Prof_Image === null) $Prof_Image = $oldImg;
        $stmt = $mysqli->prepare("UPDATE teachers SET Prof_Name=?, Prof_title=?, Prof_EmailAddress=?, Prof_ExtensionNumber=?, Prof_ResearchFields=?, Prof_Image=? WHERE Prof_ID=?");
        $stmt->bind_param("sssssss", $Prof_Name, $Prof_title, $Prof_EmailAddress, $Prof_ExtensionNumber, $Prof_ResearchFields, $Prof_Image, $Prof_ID);
        if ($stmt->execute()) {
            $success = "修改成功！";
        } else {
            $error = "修改失敗！";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $Prof_ID = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM teachers WHERE Prof_ID = ?");
    $stmt->bind_param("s", $Prof_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        $error = "找不到該教師";
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $error = "未指定教師編號";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>修改教師資料</title>
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
</head>
<body>
    <form class="edit-form" action="info_edit.php" method="post" enctype="multipart/form-data">
        <h2>修改教師資料</h2>
        <?php if ($success): ?>
            <div class="msg-success"><?= $success ?></div>
            <a href="dashboard.php" class="back-link">回首頁</a>
        <?php elseif ($error): ?>
            <div class="msg-error"><?= $error ?></div>
            <a href="dashboard.php" class="back-link">回首頁</a>
        <?php elseif (isset($row)): ?>
            <input type="hidden" name="Prof_ID" value="<?= htmlspecialchars($row['Prof_ID']) ?>">
            <label>姓名：
                <input type="text" name="Prof_Name" value="<?= htmlspecialchars($row['Prof_Name']) ?>" required>
            </label>
            <label>職稱：
                <input type="text" name="Prof_title" value="<?= htmlspecialchars($row['Prof_title']) ?>" required>
            </label>
            <label>電子郵件：
                <input type="email" name="Prof_EmailAddress" value="<?= htmlspecialchars($row['Prof_EmailAddress']) ?>" required>
            </label>
            <label>電話分機：
                <input type="text" name="Prof_ExtensionNumber" value="<?= htmlspecialchars($row['Prof_ExtensionNumber']) ?>" required>
            </label>
            <label>研究領域：
                <input type="text" name="Prof_ResearchFields" value="<?= htmlspecialchars($row['Prof_ResearchFields']) ?>" placeholder="可輸入多個，以逗號分隔">
            </label>
            <label>大頭照：
                <?php if (!empty($row['Prof_Image'])): ?>
                    <img src="<?= htmlspecialchars($row['Prof_Image']) ?>" alt="大頭照" style="max-width:100px;display:block;margin-bottom:6px;">
                    <input type="checkbox" name="delete_image" value="1"> 刪除現有圖片<br>
                <?php endif; ?>
                <input type="file" name="Prof_Image" accept="image/*">
            </label>
            <button type="submit">儲存修改</button>
            <a href="dashboard.php" class="back-link">取消</a>
        <?php endif; ?>
    </form>
</body>
</html>