<?php
session_start();
include('db.php');
//$result = $mysqli->query("SELECT * FROM teachers");


// 取得所有職位
$title_result = $mysqli->query("SELECT DISTINCT Prof_title FROM teachers ORDER BY Prof_title");

// 取得目前選擇的職位
$selected_title = isset($_GET['title']) ? $_GET['title'] : '-';

// 根據選擇的職位查詢老師
if ($selected_title === '-' || $selected_title === '') {
    $result = $mysqli->query("SELECT * FROM teachers");
} else {
    $stmt = $mysqli->prepare("SELECT * FROM teachers WHERE Prof_title = ?");
    $stmt->bind_param("s", $selected_title);
    $stmt->execute();
    $result = $stmt->get_result();
}



?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>逢甲資訊系統</title>
    <style>
        body {
            font-family: 'Segoe UI', 'Microsoft JhengHei', Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
        }
        .navbar {
            background: #fffbe8;
            color: #222;
            box-shadow: 0 2px 16px #e0e0c0;
            border-radius: 0 0 18px 18px;
            padding: 0 32px 0 32px;
            position: relative;
            min-height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar-title {
            color: #222;
            font-size: 1.5em;
            font-weight: bold;
            letter-spacing: 2px;
            text-shadow: none;
            padding: 0 0 0 8px;
        }
        .navbar-menu {
            display: flex;
            gap: 32px;
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
        }
        .navbar-menu a {
            color: #222;
            text-shadow: none;
            text-decoration: none;
            font-size: 1.1em;
            padding: 8px 0;
            transition: color 0.2s;
        }
        .navbar-menu a:hover {
            color: #007bff;
        }
        .msg-warning {
            color: #ff5e5e;
            text-align: center;
            margin-top: 16px;
            font-size: 1.1em;
        }
        h1 {
            text-align: center;
            margin-top: 40px;
            color: #222;
            letter-spacing: 2px;
            text-shadow: none;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            margin: 40px auto;
            max-width: 1100px;
        }
        .card {
            background: #e5e6ea;
            border-radius: 14px;
            box-shadow: 0 2px 8px #e0e0e0;
            padding: 28px 36px;
            width: 320px;
            margin-bottom: 20px;
            transition: box-shadow 0.2s, transform 0.2s;
            color: #222;
            border: 1.5px solid #e0e0e0;
            display: flex;
            flex-direction: column;
        }
        .card:hover {
            box-shadow: 0 8px 24px #d0d0d0;
            transform: translateY(-4px) scale(1.03);
        }
        .card-title {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 8px;
            color: #007bff;
            letter-spacing: 1px;
            text-shadow: none;
        }
        .card-info {
            margin-bottom: 6px;
            color: #222;
        }
        .card-label {
            color: #888;
            font-size: 0.98em;
            margin-right: 4px;
        }
        @media (max-width: 700px) {
            .navbar {
                flex-direction: column;
                height: auto;
                padding: 0 10px;
                border-radius: 0 0 12px 12px;
            }
            .navbar-menu {
                position: static;
                transform: none;
                right: 0;
                top: 0;
                gap: 16px;
                margin-top: 8px;
            }
            .container { flex-direction: column; align-items: center; }
            .card { width: 95%; }
        }

        // new
        .side-menu {
            position: fixed;
            top: 100px; /* 距離頂部可依需求調整 */
            left: 0;
            min-width: 180px;
            background: #fffbe8;
            border-radius: 12px;
            box-shadow: 0 2px 8px #e0e0c0;
            margin: 0 0 0 0;
            padding: 18px 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: fit-content;
            z-index: 10;
        }
        .main-layout {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            max-width: 1200px;
            margin: 0 auto;
            margin-left: 85px; /* 預留側邊選單寬度+間距 */
        }
        .side-menu a {
            display: block; /* 讓每個選單項目獨立一行 */
            color: #222;
            text-decoration: none;
            padding: 8px 24px;
            border-radius: 8px;
            font-size: 1.08em;
            transition: background 0.18s, color 0.18s;
        }
        .side-menu a.active, .side-menu a:hover {
            background: #ffe066;
            color: #007bff;
            font-weight: bold;
        }
        @media (max-width: 900px) {
            .main-layout { flex-direction: column; }
            .side-menu { flex-direction: row; margin: 24px 0 0 0; padding: 8px 0; }
            .side-menu a { padding: 8px 12px; font-size: 1em; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">逢甲資訊系統</div>
        <div class="navbar-menu">
            <a href="index.php">教授資訊</a>
            <a href="course_view.php">課表</a>
            <?php if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true): ?>
                <a href="logout.php">登出</a>
            <?php else: ?>
                <a href="login.php">登入</a>
            <?php endif; ?>
            <a href="<?php echo (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) ? 'dashboard.php' : '#'; ?>"
               onclick="return <?php if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) { ?>alert('請先登入才能進入控制台！');false;<?php } else { ?>true;<?php } ?>">
               控制台
            </a>
        </div>
    </div>
    <?php if (isset($_GET['need_login'])): ?>
        <div class="msg-warning">請先登入才能進入控制台！</div>
    <?php endif; ?>
    <h1>教師資料一覽</h1>
    <div class="main-layout">
        <nav class="side-menu">
            <a href="index.php?title=-" class="<?= ($selected_title === '-' ? 'active' : '') ?>">- 全部職位</a>
            <?php while($title_row = $title_result->fetch_assoc()): ?>
                <a href="index.php?title=<?= urlencode($title_row['Prof_title']) ?>"
                   class="<?= ($selected_title === $title_row['Prof_title'] ? 'active' : '') ?>">
                   <?= htmlspecialchars($title_row['Prof_title']) ?>
                </a>
            <?php endwhile; ?>
        </nav>
        <div class="container">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="card" style="flex-direction:row; align-items:center; min-height:160px; width:420px;">
                <?php if (!empty($row['Prof_Image'])): ?>
                    <img src="<?= htmlspecialchars($row['Prof_Image']) ?>" alt="大頭照" style="width:110px;height:110px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #ccc;margin-right:24px;flex-shrink:0;">
                <?php endif; ?>
                <div style="flex:1; min-width:0;">
                    <div class="card-title">
                        <a href="professor.php?id=<?= urlencode($row['Prof_ID']) ?>">
                            <?= htmlspecialchars($row['Prof_Name']) ?>
                        </a>
                    </div>
                    <div class="card-info">
                        <span class="card-label">職稱：</span><?= htmlspecialchars($row['Prof_title']) ?>
                    </div>
                    <div class="card-info">
                        <span class="card-label">電子郵件：</span><?= htmlspecialchars($row['Prof_EmailAddress']) ?>
                    </div>
                    <div class="card-info">
                        <span class="card-label">電話分機：</span><?= htmlspecialchars($row['Prof_ExtensionNumber']) ?>
                    </div>
                    <div class="card-info">
                        <span class="card-label">研究領域：</span><?= htmlspecialchars($row['Prof_ResearchFields']) ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>