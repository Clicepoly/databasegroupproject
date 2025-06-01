<?php
// 論文修改功能
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

$success = '';
$error = '';

if (!isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $error = "缺少論文ID！";
}
$id = $_GET['id'] ?? ($_POST['Paper_ID'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Paper_ID = $_POST['Paper_ID'] ?? '';
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Paper_Author = $_POST['Paper_Author'] ?? '';
    $Paper_Title = $_POST['Paper_Title'] ?? '';
    $Paper_Category = $_POST['Paper_Category'] ?? '';
    $Paper_JournalName = $_POST['Paper_JournalName'] ?? '';
    $Paper_ConferenceName = $_POST['Paper_ConferenceName'] ?? '';
    $Paper_BookTitle = $_POST['Paper_BookTitle'] ?? '';
    $Paper_BookType = $_POST['Paper_BookType'] ?? '';
    $Paper_ConferenceLocation = $_POST['Paper_ConferenceLocation'] ?? '';
    $Paper_PublishDate = $_POST['Paper_PublishDate'] ?? '';
    $Paper_Indexing = $_POST['Paper_Indexing'] ?? '';
    $Paper_Publisher = $_POST['Paper_Publisher'] ?? '';

    if (empty($Paper_ID) || empty($Prof_ID) || empty($Paper_Author) || empty($Paper_Title) || empty($Paper_Category)) {
        $error = "所有欄位都是必填的！";
    } else {
        $stmt = $mysqli->prepare("UPDATE Paper SET Prof_ID=?, Paper_Author=?, Paper_Title=?, Paper_Category=?, Paper_JournalName=?, Paper_ConferenceName=?, Paper_BookTitle=?, Paper_BookType=?, Paper_Publisher=?, Paper_ConferenceLocation=?, Paper_PublishDate=?, Paper_Indexing=? WHERE Paper_ID=?");
        $stmt->bind_param("ssssssssssssi", $Prof_ID, $Paper_Author, $Paper_Title, $Paper_Category, $Paper_JournalName, $Paper_ConferenceName, $Paper_BookTitle, $Paper_BookType, $Paper_Publisher, $Paper_ConferenceLocation, $Paper_PublishDate, $Paper_Indexing, $Paper_ID);
        if ($stmt->execute()) {
            $success = "修改成功！";
        } else {
            $error = "修改失敗！";
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !empty($id)) {
    $stmt = $mysqli->prepare("SELECT * FROM Paper WHERE Paper_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        $error = "找不到論文資料！";
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST成功後顯示剛剛送出的內容
    $row = [
        'Paper_ID' => $Paper_ID,
        'Prof_ID' => $Prof_ID,
        'Paper_Author' => $Paper_Author,
        'Paper_Title' => $Paper_Title,
        'Paper_Category' => $Paper_Category,
        'Paper_JournalName' => $Paper_JournalName,
        'Paper_ConferenceName' => $Paper_ConferenceName,
        'Paper_BookTitle' => $Paper_BookTitle,
        'Paper_BookType' => $Paper_BookType,
        'Paper_Publisher' => $Paper_Publisher,
        'Paper_ConferenceLocation' => $Paper_ConferenceLocation,
        'Paper_PublishDate' => $Paper_PublishDate,
        'Paper_Indexing' => $Paper_Indexing
    ];
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>修改論文</title>
    <style>
        body { font-family: "Microsoft JhengHei", Arial, sans-serif; background: #f8f9fa; }
        .edit-form {
            background: #fff; padding: 20px; margin: 40px auto; width: 400px;
            border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        label { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="date"] {
            width: 95%; padding: 6px 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px;
        }
        select { width: 98%; padding: 6px 8px; border-radius: 4px; border: 1px solid #ccc; }
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
    function updateFields() {
        var cat = document.getElementById('Paper_Category');
        if (!cat) return;
        var v = cat.value;
        document.getElementById('journal_fields').style.display = (v === '期刊') ? '' : 'none';
        document.getElementById('conference_fields').style.display = (v === '會議') ? '' : 'none';
        document.getElementById('book_fields').style.display = (v === '專書') ? '' : 'none';
    }
    window.addEventListener('DOMContentLoaded', updateFields);
    </script>
</head>
<body>
    <form class="edit-form" action="paper_edit.php" method="post" onsubmit="return true;">
        <h2>修改論文</h2>
        <?php if ($success): ?>
            <script>alert('修改成功！');window.location.href='dashboard.php?tab=paper';</script>
        <?php elseif ($error): ?>
            <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($row)): ?>
            <input type="hidden" name="Paper_ID" value="<?= htmlspecialchars($row['Paper_ID']) ?>">
            <label>教師編號：<input type="text" name="Prof_ID" value="<?= htmlspecialchars($row['Prof_ID']) ?>" required></label>
            <label>作者（多位請以空格分隔）：<input type="text" name="Paper_Author" value="<?= htmlspecialchars($row['Paper_Author']) ?>" required></label>
            <label>論文標題：<input type="text" name="Paper_Title" value="<?= htmlspecialchars($row['Paper_Title']) ?>" required></label>
            <label>類型：
                <select name="Paper_Category" id="Paper_Category" onchange="updateFields()" required>
                    <option value="期刊" <?= $row['Paper_Category']==='期刊'?'selected':'' ?>>期刊</option>
                    <option value="會議" <?= $row['Paper_Category']==='會議'?'selected':'' ?>>會議</option>
                    <option value="專書" <?= $row['Paper_Category']==='專書'?'selected':'' ?>>專書</option>
                </select>
            </label>
            <div id="journal_fields" style="display:none;">
                <label>期刊名稱：<input type="text" name="Paper_JournalName" value="<?= htmlspecialchars($row['Paper_JournalName']) ?>"></label>
            </div>
            <div id="conference_fields" style="display:none;">
                <label>會議名稱：<input type="text" name="Paper_ConferenceName" value="<?= htmlspecialchars($row['Paper_ConferenceName']) ?>"></label>
                <label>地點：<input type="text" name="Paper_ConferenceLocation" value="<?= htmlspecialchars($row['Paper_ConferenceLocation']) ?>"></label>
            </div>
            <div id="book_fields" style="display:none;">
                <label>書名：<input type="text" name="Paper_BookTitle" value="<?= htmlspecialchars($row['Paper_BookTitle']) ?>"></label>
                <label>類型：
                    <select name="Paper_BookType">
                        <option value="專書" <?= $row['Paper_BookType']==='專書'?'selected':'' ?>>專書</option>
                        <option value="技術報告" <?= $row['Paper_BookType']==='技術報告'?'selected':'' ?>>技術報告</option>
                    </select>
                </label>
            </div>
            <label>出版社：<input type="text" name="Paper_Publisher" value="<?= htmlspecialchars($row['Paper_Publisher']) ?>"></label>
            <label>發表日期：<input type="date" name="Paper_PublishDate" value="<?= htmlspecialchars($row['Paper_PublishDate']) ?>" required></label>
            <label>收錄索引（SCIE/EI，可空）：<input type="text" name="Paper_Indexing" value="<?= htmlspecialchars($row['Paper_Indexing']) ?>"></label>
            <button type="submit">儲存修改</button>
            <a href="dashboard.php?tab=paper" class="back-link">取消</a>
        <?php endif; ?>
    </form>
</body>
</html>
