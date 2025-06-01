<?php
// professor.php
// 顯示單一教授詳細頁面
include('db.php');
$Prof_ID = $_GET['id'] ?? '';
if (!$Prof_ID) {
    echo '未指定教授';
    exit();
}
$stmt = $mysqli->prepare("SELECT * FROM teachers WHERE Prof_ID = ?");
$stmt->bind_param("s", $Prof_ID);
$stmt->execute();
$prof = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$prof) {
    echo '找不到該教授';
    exit();
}
// 取得學歷資料
$stmt2 = $mysqli->prepare("SELECT * FROM EducationalBackground WHERE Prof_ID = ?");
$stmt2->bind_param("s", $Prof_ID);
$stmt2->execute();
$edus = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
// 取得經歷資料，分校內/校外
$stmt3 = $mysqli->prepare("SELECT * FROM Experience WHERE Prof_ID = ?");
$stmt3->bind_param("s", $Prof_ID);
$stmt3->execute();
$exps = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt3->close();
$exp_in = [];
$exp_out = [];
foreach ($exps as $exp) {
    if (strpos($exp['Experience_type'], '校內') !== false) {
        $exp_in[] = $exp;
    } else {
        $exp_out[] = $exp;
    }
}
// 取得獲獎資料
$stmt4 = $mysqli->prepare("SELECT * FROM Award WHERE Prof_ID = ? ORDER BY Award_Date DESC");
$stmt4->bind_param("s", $Prof_ID);
$stmt4->execute();
$awards = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt4->close();
// 取得計畫資料，分國科會/產學合作
$stmt5 = $mysqli->prepare("SELECT * FROM Project WHERE Prof_ID = ?");
$stmt5->bind_param("s", $Prof_ID);
$stmt5->execute();
$projects = $stmt5->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt5->close();
$proj_nstc = [];
$proj_industry = [];
foreach ($projects as $proj) {
    if ($proj['Project_Type'] === '國科會') {
        $proj_nstc[] = $proj;
    } else if ($proj['Project_Type'] === '產學合作') {
        $proj_industry[] = $proj;
    }
}
// 取得演講資料
$stmt6 = $mysqli->prepare("SELECT * FROM Speech WHERE Prof_ID = ? ORDER BY Speech_Date DESC");
$stmt6->bind_param("s", $Prof_ID);
$stmt6->execute();
$speeches = $stmt6->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt6->close();
// 取得教材與作品資料
$stmt7 = $mysqli->prepare("SELECT * FROM TeachingMaterials WHERE Prof_ID = ?");
$stmt7->bind_param("s", $Prof_ID);
$stmt7->execute();
$teachmats = $stmt7->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt7->close();
// 取得專利資料
$stmt8 = $mysqli->prepare("SELECT * FROM Patent WHERE Prof_ID = ?");
$stmt8->bind_param("s", $Prof_ID);
$stmt8->execute();
$patents = $stmt8->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt8->close();
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($prof['Prof_Name']) ?> - 教授資訊</title>
    <style>
        body {
            font-family: 'Segoe UI', 'Microsoft JhengHei', Arial, sans-serif;
            background:rgb(255, 255, 255);
            color: #222;
            text-align: left;
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
        h1, h2, h3 {
            color: #222;
            text-shadow: none;
            letter-spacing: 2px;
            text-align: left;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 24px;
            margin: 40px auto;
            max-width: 900px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 32px #e0e0e0;
            border: 1.5px solid #e0e0e0;
            padding: 40px 32px 32px 32px;
        }
        .info, .label {
            color: #222;
            margin-bottom: 4px;
            font-size: 1.08em;
            text-align: left;
        }
        .card-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin: 18px 0;
            width: 100%;
        }
        .card {
            background: #e5e6ea;
            border-radius: 14px;
            box-shadow: 0 2px 8px #e0e0e0;
            padding: 22px 28px;
            border: 1.5px solid #e0e0e0;
            color: #222;
            transition: box-shadow 0.2s, transform 0.2s;
            font-size: 1.08em;
            text-align: left;
        }
        .card:hover {
            box-shadow: 0 8px 24px #d0d0d0;
            transform: translateY(-2px) scale(1.025);
        }
        .card-title {
            font-size: 0.98em;
            margin-bottom: 8px;
            color:rgb(0, 0, 0);
            letter-spacing: 1px;
            text-shadow: none;
            text-align: left;
        }
        .card-label {
            color:rgb(0, 0, 0);
            font-size: 0.98em;
            margin-right: 6px;
            text-align: left;
        }
        a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.2s;
        }
        a:hover {
            color: #222;
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
            .container { padding: 10px; }
            .card { padding: 12px 8px; width: 95%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">← 回教授列表</a>
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 24px;">
            <div style="flex:1; min-width:0;">
                <h1><?= htmlspecialchars($prof['Prof_Name']) ?></h1>
                <div class="info"><span class="label">職稱：</span><?= htmlspecialchars($prof['Prof_title']) ?></div>
                <div class="info"><span class="label">電子郵件：</span><?= htmlspecialchars($prof['Prof_EmailAddress']) ?></div>
                <div class="info"><span class="label">電話分機：</span><?= htmlspecialchars($prof['Prof_ExtensionNumber']) ?></div>
                <?php if (!empty($prof['Prof_ResearchFields'])): ?>
                <div class="info"><span class="label">研究領域：</span><?= htmlspecialchars($prof['Prof_ResearchFields']) ?></div>
                <?php endif; ?>
            </div>
            <?php if (!empty($prof['Prof_Image'])): ?>
            <div style="flex-shrink:0; text-align:right;">
                <img src="<?= htmlspecialchars($prof['Prof_Image']) ?>" alt="大頭照" style="max-width:160px;max-height:160px;border-radius:12px;box-shadow:0 2px 12px #ccc;">
            </div>
            <?php endif; ?>
        </div>
        <h2>學歷</h2>
        <?php if (count($edus) > 0): ?>
        <div class="card-list">
            <?php foreach($edus as $edu): ?>
            <div class="card">
                <div class="card-title">學校：<?= htmlspecialchars($edu['EduBG_University']) ?></div>
                <div><span class="card-label">系所：</span><?= htmlspecialchars($edu['EduBG_Department']) ?></div>
                <div><span class="card-label">學位：</span><?= htmlspecialchars($edu['EduBG_Degree']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div>尚無學歷資料</div>
        <?php endif; ?>
        <h2>經歷</h2>
        <?php if (count($exp_in) > 0): ?>
            <h3>校內經歷</h3>
            <div class="card-list">
            <?php foreach($exp_in as $exp): ?>
                <div class="card">
                    <div class="card-title">類型：<?= htmlspecialchars($exp['Experience_type']) ?></div>
                    <div><span class="card-label">職稱/職位：</span><?= htmlspecialchars($exp['Experience_position']) ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (count($exp_out) > 0): ?>
            <h3>校外經歷</h3>
            <div class="card-list">
            <?php foreach($exp_out as $exp): ?>
                <div class="card">
                    <div class="card-title">類型：<?= htmlspecialchars($exp['Experience_type']) ?></div>
                    <div><span class="card-label">職稱/職位：</span><?= htmlspecialchars($exp['Experience_position']) ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (count($exp_in) === 0 && count($exp_out) === 0): ?>
            <div>尚無經歷資料</div>
        <?php endif; ?>

<?php
// 取得論文資料
$stmtPaper = $mysqli->prepare("SELECT * FROM Paper WHERE Prof_ID = ? ORDER BY Paper_PublishDate DESC, Paper_ID DESC");
$stmtPaper->bind_param("s", $Prof_ID);
$stmtPaper->execute();
$papers = $stmtPaper->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtPaper->close();
?>
<h2>論文</h2>
<?php if (count($papers) > 0): ?>
<div class="card-list">
    <?php foreach($papers as $paper): ?>
    <div class="card">
        <div class="card-title">標題：<?= htmlspecialchars($paper['Paper_Title']) ?></div>
        <div><span class="card-label">作者：</span><?= htmlspecialchars($paper['Paper_Author']) ?></div>
        <div><span class="card-label">類型：</span><?= htmlspecialchars($paper['Paper_Category']) ?></div>
        <?php if ($paper['Paper_Category'] === '期刊'): ?>
            <div><span class="card-label">期刊名稱：</span><?= htmlspecialchars($paper['Paper_JournalName']) ?></div>
        <?php elseif ($paper['Paper_Category'] === '會議'): ?>
            <div><span class="card-label">會議名稱：</span><?= htmlspecialchars($paper['Paper_ConferenceName']) ?></div>
            <div><span class="card-label">地點：</span><?= htmlspecialchars($paper['Paper_ConferenceLocation']) ?></div>
        <?php elseif ($paper['Paper_Category'] === '專書'): ?>
            <div><span class="card-label">書名：</span><?= htmlspecialchars($paper['Paper_BookTitle']) ?></div>
            <div><span class="card-label">專書類型：</span><?= htmlspecialchars($paper['Paper_BookType']) ?></div>
        <?php endif; ?>
        <div><span class="card-label">出版社：</span><?= htmlspecialchars($paper['Paper_Publisher']) ?></div>
        <div><span class="card-label">發表日期：</span><?= htmlspecialchars($paper['Paper_PublishDate']) ?></div>
        <?php if (!empty($paper['Paper_Indexing'])): ?>
        <div><span class="card-label">收錄索引：</span><?= htmlspecialchars($paper['Paper_Indexing']) ?></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div>尚無論文資料</div>
<?php endif; ?>

<h2>指導學生獲獎</h2>
<?php if (count($awards) > 0): ?>
        <div class="card-list">
            <?php foreach($awards as $award): ?>
            <div class="card">
                <div class="card-title">學生：<?= htmlspecialchars($award['Award_Advisee']) ?></div>
                <div><span class="card-label">作品/計畫：</span><?= htmlspecialchars($award['Award_ProjectName']) ?></div>
                <div><span class="card-label">競賽與名次：</span><?= htmlspecialchars($award['Award_CompName_Position']) ?></div>
                <div><span class="card-label">得獎日期：</span><?= htmlspecialchars($award['Award_Date']) ?></div>
                <div><span class="card-label">主辦單位：</span><?= htmlspecialchars($award['Award_organizer']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div>尚無獲獎資料</div>
        <?php endif; ?>
        <h2>計畫</h2>
        <?php if (count($proj_nstc) > 0): ?>
            <h3>國科會計畫</h3>
            <div class="card-list">
            <?php foreach($proj_nstc as $proj): ?>
                <div class="card">
                    <div class="card-title">計畫名稱：<?= htmlspecialchars($proj['Project_Name']) ?></div>
                    <div><span class="card-label">期間：</span><?= htmlspecialchars($proj['Project_Duration']) ?></div>
                    <div><span class="card-label">擔任職務：</span><?= htmlspecialchars($proj['Project_TakenPosition']) ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (count($proj_industry) > 0): ?>
            <h3>產學合作計畫</h3>
            <div class="card-list">
            <?php foreach($proj_industry as $proj): ?>
                <div class="card">
                    <div class="card-title">計畫名稱：<?= htmlspecialchars($proj['Project_Name']) ?></div>
                    <div><span class="card-label">期間：</span><?= htmlspecialchars($proj['Project_Duration']) ?></div>
                    <div><span class="card-label">擔任職務：</span><?= htmlspecialchars($proj['Project_TakenPosition']) ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (count($proj_nstc) === 0 && count($proj_industry) === 0): ?>
            <div>尚無計畫資料</div>
        <?php endif; ?>
        <h2>演講</h2>
        <?php if (count($speeches) > 0): ?>
        <div class="card-list">
            <?php foreach($speeches as $speech): ?>
            <div class="card">
                <div class="card-title">演講名稱：<?= htmlspecialchars($speech['Speech_Name']) ?></div>
                <div><span class="card-label">對象/場合：</span><?= htmlspecialchars($speech['Speech_Audience']) ?></div>
                <div><span class="card-label">日期：</span><?= htmlspecialchars($speech['Speech_Date']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div>尚無演講資料</div>
        <?php endif; ?>
        <h2>教材與作品</h2>
        <?php if (count($teachmats) > 0): ?>
        <div class="card-list">
            <?php foreach($teachmats as $tm): ?>
            <div class="card">
                <div class="card-title">教材/作品名稱：<?= htmlspecialchars($tm['TeachMat_Name']) ?></div>
                <div><span class="card-label">作者：</span><?= htmlspecialchars($tm['TeachMat_Author']) ?></div>
                <div><span class="card-label">出版社/發表單位：</span><?= htmlspecialchars($tm['TeachMat_Publisher']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div>尚無教材與作品資料</div>
        <?php endif; ?>
        <h2>核准專利</h2>
        <?php if (isset($patents) && count($patents) > 0): ?>
        <div class="card-list">
            <?php foreach($patents as $pat): ?>
            <div class="card">
                <div class="card-title">專利類型：<?= htmlspecialchars($pat['Patent_Type']) ?></div>
                <div><span class="card-label">專利名稱/內容：</span><?= htmlspecialchars($pat['Patent_Name']) ?></div>
                <div><span class="card-label">專利時間：</span><?= htmlspecialchars($pat['Patent_Term']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div>尚無專利資料</div>
        <?php endif; ?>
    </div>
</body>
</html>
