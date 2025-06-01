<?php
// 查詢演講資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    http_response_code(403);
    echo '未授權存取';
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search_keyword'])) {
    $keyword = '%' . $_POST['search_keyword'] . '%';
    $stmt = $mysqli->prepare("SELECT * FROM Speech WHERE Speech_Name LIKE ? OR Speech_Audience LIKE ?");
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    if (count($search_results) === 0) {
        echo '<div>查無資料</div>';
    } else {
        echo '<div style="overflow-x:auto;"><table style="margin-top:20px;width:100%;border-collapse:collapse;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.07);">';
        echo '<tr style="background:#007bff;color:#fff;"><th>演講ID</th><th>教師編號</th><th>演講名稱</th><th>對象/場合</th><th>日期</th><th>操作</th></tr>';
        foreach ($search_results as $row) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Speech_ID']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Prof_ID']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Speech_Name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Speech_Audience']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Speech_Date']) . '</td>';
            echo '<td><a href="speech_edit.php?id=' . $row['Speech_ID'] . '">編輯</a> | <a href="speech_delete.php?id=' . $row['Speech_ID'] . '" onclick="return confirm(\'確定要刪除嗎？\');">刪除</a></td>';
            echo '</tr>';
        }
        echo '</table></div>';
        // 不顯示回後台按鈕
    }
} else {
    echo '請輸入查詢關鍵字';
}
?>
