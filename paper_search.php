<?php
// 論文查詢功能（AJAX回傳table）
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    exit('未登入');
}
include('db.php');

$keyword = $_POST['search_keyword'] ?? '';
$keyword = "%$keyword%";
$stmt = $mysqli->prepare("SELECT * FROM Paper WHERE Paper_Author LIKE ? OR Paper_Title LIKE ? OR Paper_Category LIKE ? OR Paper_JournalName LIKE ? OR Paper_ConferenceName LIKE ? OR Paper_BookTitle LIKE ? OR Paper_Publisher LIKE ?");
$stmt->bind_param("sssssss", $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo '<div>查無資料</div>';
    exit();
}
echo '<table><tr><th>ID</th><th>教師編號</th><th>作者</th><th>標題</th><th>類型</th><th>操作</th></tr>';
while($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['Paper_ID']) . '</td>';
    echo '<td>' . htmlspecialchars($row['Prof_ID']) . '</td>';
    echo '<td>' . htmlspecialchars($row['Paper_Author']) . '</td>';
    echo '<td>' . htmlspecialchars($row['Paper_Title']) . '</td>';
    echo '<td>' . htmlspecialchars($row['Paper_Category']) . '</td>';
    echo '<td>';
    echo '<a href="paper_edit.php?id=' . urlencode($row['Paper_ID']) . '">修改</a> ';
    echo '<a class="delete-link" href="paper_delete.php?id=' . urlencode($row['Paper_ID']) . '" onclick="return confirm(\'確定要刪除嗎？\');">刪除</a>';
    echo '</td>';
    echo '</tr>';
}
echo '</table>';
