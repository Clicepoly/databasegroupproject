<?php
// 查詢獎項資料
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    http_response_code(403);
    echo '未授權存取';
    exit();
}
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search_keyword'])) {
    $keyword = '%' . $_POST['search_keyword'] . '%';
    $stmt = $mysqli->prepare("SELECT * FROM Award WHERE Award_Advisee LIKE ? OR Award_ProjectName LIKE ? OR Award_CompName_Position LIKE ? OR Award_organizer LIKE ?");
    $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    if (count($search_results) === 0) {
        echo '<div>查無資料</div>';
    } else {
        echo '<div style="overflow-x:auto;"><table style="margin-top:20px;width:100%;border-collapse:collapse;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.07);">';
        echo '<tr style="background:#007bff;color:#fff;"><th>獎項ID</th><th>教師編號</th><th>學生姓名</th><th>作品/計畫名稱</th><th>競賽名稱與名次</th><th>得獎日期</th><th>主辦單位</th><th>操作</th></tr>';
        foreach ($search_results as $row) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Award_ID']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Prof_ID']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Award_Advisee']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Award_ProjectName']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Award_CompName_Position']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Award_Date']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Award_organizer']) . '</td>';
            echo '<td><a href="award_edit.php?id=' . $row['Award_ID'] . '">編輯</a> | <a href="award_delete.php?id=' . $row['Award_ID'] . '" onclick="return confirm(\'確定要刪除嗎？\');">刪除</a></td>';
            echo '</tr>';
        }
        echo '</table></div>';
        // 不顯示回後台按鈕
    }
} else {
    echo '請輸入查詢關鍵字';
}
?>
