<?php
include('db.php');
$result = $mysqli->query("SELECT * FROM teachers");
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>教師列表</title>
</head>
<body>
    <h1>教師列表</h1>
    <a href="dashboard.php">回新增頁</a>
    <table border="1" cellpadding="5">
        <tr>
            <th>姓名</th>
            <th>職稱</th>
            <th>電子郵件</th>
            <th>研究領域</th>
            <th>操作</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['research_area']) ?></td>
            <td>
                <a href="info_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>