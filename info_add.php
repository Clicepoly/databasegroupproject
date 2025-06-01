<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
?>
<?php
include('db.php');

// 新增圖片上傳處理
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

    // Validate input data
    if (empty($Prof_ID) || empty($Prof_Name) || empty($Prof_title) || empty($Prof_EmailAddress)|| empty($Prof_ExtensionNumber)) {
        echo "<script>alert('所有欄位都是必填的！');history.back();</script>";
        exit();
    }
    // 主鍵重複檢查
    $check = $mysqli->prepare("SELECT Prof_ID FROM teachers WHERE Prof_ID = ?");
    $check->bind_param("s", $Prof_ID);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<script>alert('新增失敗：教師編號已存在！');history.back();</script>";
        $check->close();
        exit();
    }
    $check->close();

    // Prepare and execute the SQL INSERT statement
    $stmt = $mysqli->prepare("INSERT INTO teachers (Prof_ID, Prof_Name, Prof_title, Prof_EmailAddress, Prof_ExtensionNumber, Prof_ResearchFields, Prof_Image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("sssssss", $Prof_ID, $Prof_Name, $Prof_title, $Prof_EmailAddress, $Prof_ExtensionNumber, $Prof_ResearchFields, $Prof_Image);

    if ($stmt->error) {
        die("SQL execute failed: " . htmlspecialchars($stmt->error));
    }

    if ($stmt->execute()) {
        header("Location: dashboard.php");
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
    <title>新增教師</title>
</head>
<body>
    <h1>新增教師</h1>
    <form method="post" enctype="multipart/form-data">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>姓名：<input type="text" name="Prof_Name" required></label><br>
        <label>職稱：<input type="text" name="Prof_title" required></label><br>
        <label>電子郵件：<input type="email" name="Prof_EmailAddress" required></label><br>
        <label>電話分機：<input type="text" name="Prof_ExtensionNumber" required></label><br>
        <label>研究領域：<input type="text" name="Prof_ResearchFields" placeholder="可輸入多個，以逗號分隔"></label><br>
        <label>大頭照：<input type="file" name="Prof_Image" accept="image/*"></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>