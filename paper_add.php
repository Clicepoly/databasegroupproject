<?php
// 新增論文資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');


/*
	#	名稱	類型	編碼與排序	屬性	空值(Null)	預設值	備註	額外資訊	動作
	1	Paper_ID 主鍵	int(11)			否	無		AUTO_INCREMENT	修改 修改	刪除 刪除	
	2	Prof_ID 索引	varchar(50)	utf8mb4_general_ci		否	無			修改 修改	刪除 刪除	
	3	Paper_Author	varchar(200)	utf8mb4_general_ci		否	無			修改 修改	刪除 刪除	
	4	Paper_Title	varchar(200)	utf8mb4_general_ci		否	無			修改 修改	刪除 刪除	
	5	Paper_Category	varchar(10)	utf8mb4_general_ci		否	無			修改 修改	刪除 刪除	
	6	Paper_JournalName	varchar(200)	utf8mb4_general_ci		是	NULL			修改 修改	刪除 刪除	
	7	Paper_ConferenceName	varchar(200)	utf8mb4_general_ci		是	NULL			修改 修改	刪除 刪除	
	8	Paper_BookTitle	varchar(200)	utf8mb4_general_ci		是	NULL			修改 修改	刪除 刪除	
	9	Paper_BookType	varchar(200)	utf8mb4_general_ci		是	NULL			修改 修改	刪除 刪除	
	10	Paper_Publisher	varchar(200)	utf8mb4_general_ci		否	無			修改 修改	刪除 刪除	
	11	Paper_ConferenceLocation	varchar(200)	utf8mb4_general_ci		是	NULL			修改 修改	刪除 刪除	
	12	Paper_PublishDate	varchar(50)	utf8mb4_general_ci		否	無			修改 修改	刪除 刪除	
	13	Paper_Indexing	varchar(200)	utf8mb4_general_ci		是	NULL			修改 修改	刪除 刪除	

*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Prof_ID = $_POST['Prof_ID'] ?? '';
    $Paper_Author = $_POST['Paper_Author'] ?? '';
    $Paper_Title = $_POST['Paper_Title'] ?? '';
    $Paper_Category = $_POST['Paper_Category'] ?? '';
    $Paper_JournalName = $_POST['Paper_JournalName'] ?? '';
    $Paper_ConferenceName = $_POST['Paper_ConferenceName'] ?? '';
    $Paper_BookTitle = $_POST['Paper_BookTitle'] ?? '';
    $Paper_BookType = $_POST['Paper_BookType'] ?? '';
    $Paper_Publisher = $_POST['Paper_Publisher'] ?? '';
    $Paper_ConferenceLocation = $_POST['Paper_ConferenceLocation'] ?? '';
    $Paper_PublishDate = $_POST['Paper_PublishDate'] ?? '';
    $Paper_Indexing = $_POST['Paper_Indexing'] ?? '';

    if (empty($Prof_ID)) {
        echo "所有欄位都是必填的！";
        exit();
    }
    // depends on Paper_Category, some fields may be empty
    if (empty($Paper_Author) || empty($Paper_Title) || empty($Paper_Category)) {
        echo "所有欄位都是必填的！";
        exit();
    }
    if ($Paper_Category === '期刊') {
        if (empty($Paper_JournalName) || empty($Paper_PublishDate)) {
            echo "期刊類型的論文必須填寫期刊名稱和發表日期！";
            exit();
        }
    } elseif ($Paper_Category === '會議') {
        if (empty($Paper_ConferenceName) || empty($Paper_ConferenceLocation) || empty($Paper_PublishDate)) {
            echo "會議類型的論文必須填寫會議名稱、地點和發表日期！";
            exit();
        }
    } elseif ($Paper_Category === '書籍') {
        if (empty($Paper_BookTitle) || empty($Paper_BookType) || empty($Paper_PublishDate)) {
            echo "書籍類型的論文必須填寫書名、類型和發表日期！";
            exit();
        }
    }
    $stmt = $mysqli->prepare("INSERT INTO Paper (
    Prof_ID, Paper_Author, Paper_Title, Paper_Category,
    Paper_JournalName, Paper_ConferenceName, Paper_BookTitle,
    Paper_BookType, Paper_Publisher, Paper_ConferenceLocation, Paper_PublishDate, Paper_Indexing) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
    }
    $stmt->bind_param("ssssssssssss", $Prof_ID, $Paper_Author, $Paper_Title, $Paper_Category,
        $Paper_JournalName, $Paper_ConferenceName, $Paper_BookTitle,
        $Paper_BookType, $Paper_Publisher, $Paper_ConferenceLocation, $Paper_PublishDate, $Paper_Indexing);

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=paper");
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
    <title>新增論文</title>
    <script>
    function updateFields() {
        var cat = document.getElementById('Paper_Category').value;
        document.getElementById('journal_fields').style.display = (cat === '期刊') ? '' : 'none';
        document.getElementById('conference_fields').style.display = (cat === '會議') ? '' : 'none';
        document.getElementById('book_fields').style.display = (cat === '專書') ? '' : 'none';
    }
    window.addEventListener('DOMContentLoaded', updateFields);
    </script>
</head>
<body>
    <h1>新增論文</h1>
    <form method="post">
        <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
        <label>作者（多位請以空格分隔）：<input type="text" name="Paper_Author" placeholder="作者1 作者2 ..." required></label><br>
        <label>論文標題：<input type="text" name="Paper_Title" required></label><br>
        <label>類型：
            <select name="Paper_Category" id="Paper_Category" onchange="updateFields()" required>
                <option value="期刊">期刊</option>
                <option value="會議">會議</option>
                <option value="專書">專書</option>
            </select>
        </label><br>
        <div id="journal_fields" style="display:none;">
            <label>期刊名稱：<input type="text" name="Paper_JournalName"></label><br>
        </div>
        <div id="conference_fields" style="display:none;">
            <label>會議名稱：<input type="text" name="Paper_ConferenceName"></label><br>
            <label>地點：<input type="text" name="Paper_ConferenceLocation"></label><br>
        </div>
        <div id="book_fields" style="display:none;">
            <label>書名：<input type="text" name="Paper_BookTitle"></label><br>
            <label>類型：
                <select name="Paper_BookType">
                    <option value="專書">專書</option>
                    <option value="技術報告">技術報告</option>
                </select>
            </label><br>
        </div>
        <label>出版社：<input type="text" name="Paper_Publisher"></label><br>
        <label>發表日期：<input type="date" name="Paper_PublishDate" required></label><br>
        <label>收錄索引（SCIE/EI，可空）：<input type="text" name="Paper_Indexing"></label><br>
        <button type="submit">新增</button>
    </form>
</body>
</html>
