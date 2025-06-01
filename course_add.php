<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 新增課程資料
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: index.php");
    exit();
}
include('db.php');

// 取得所有課程，依時段分組
$courses = [];
$res = $mysqli->query("SELECT * FROM CourseInfo");
while($row = $res->fetch_assoc()) {
    $period = $row['Course_Period'];
    if (!isset($courses[$period])) $courses[$period] = [];
    $courses[$period][] = $row;
}

// 新增課程處理
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $Course_Name = $_POST['Course_Name'] ?? '';
    $Course_Credit = $_POST['Course_Credit'] ?? '';
    $Course_Req = $_POST['Course_Req'] ?? '';
    $Course_Class = $_POST['Course_Class'] ?? null;
    $Course_Teachers = $_POST['Course_Teachers'] ?? null;
    $Course_Period = $_POST['Course_Period'] ?? '';
    $Course_Location = $_POST['Course_Location'] ?? '';

    if (empty($Course_Name) || $Course_Credit === '' || empty($Course_Req) || empty($Course_Period) || empty($Course_Location)) {
        echo "<script>alert('所有必填欄位都要填寫！');</script>";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO CourseInfo (Course_Name, Course_Credit, Course_Req, Course_Class, Course_Teachers, Course_Period, Course_Location) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("SQL prepare failed: " . htmlspecialchars($mysqli->error));
        }
        $stmt->bind_param("sisssss", $Course_Name, $Course_Credit, $Course_Req, $Course_Class, $Course_Teachers, $Course_Period, $Course_Location);
        if ($stmt->execute()) {
            // header("Location: course_edit.php");
            // exit();
            echo "<script>if(window.parent && window.parent !== window){window.parent.postMessage('course_add_success','*');}else{location.href='dashboard.php';}</script>";
            exit();
        } else {
            echo "<script>alert('新增失敗！');</script>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>新增課程</title>
    <style>
        body { font-family: 'Microsoft JhengHei', Arial, sans-serif; background: #f3f4f6; }
        h1 { text-align: center; margin: 30px 0; }
        .main { display: flex; gap: 32px; align-items: flex-start; justify-content: center; }
        table { border-collapse: collapse; background: #fff; box-shadow: 0 2px 12px #eee; }
        th, td { border: 1px solid #ccc; width: 72px; height: 34px; text-align: center; position: relative; font-size: 0.98em; }
        th { background: #ffe066; color: #222; font-weight: bold; }
        td.editable { cursor: pointer; background: #f5f6fa; }
        td.editable:hover, td.editable.selected { background: #e5e6ea; }
        #addPanel { min-width:340px; background:#fffbe8; padding:18px 18px 12px 18px; border-radius:10px; box-shadow:0 2px 8px #eee; display:none; }
        .course-list { font-size:0.98em; color:#007bff; margin:2px 0 0 0; }
    </style>
</head>
<body>
    <h1>新增課程</h1>
    <div class="main">
        <table id="addTable">
            <thead>
                <tr>
                    <th></th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= 14; $i++): ?>
                <tr>
                    <th><?= $i ?></th>
                    <?php for ($j = 1; $j <= 5; $j++):
                        $period = $i.'-'.$j; ?>
                    <td class="editable" data-row="<?= $i ?>" data-col="<?= $j ?>" data-period="<?= $period ?>">
                        <?php if (!empty($courses[$period])): ?>
                            <div class="course-list">
                            <?php foreach($courses[$period] as $c): ?>
                                <div><?= htmlspecialchars($c['Course_Name']) ?></div>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <?php endfor; ?>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <div id="addPanel"></div>
    </div>
    <script>
    // 點選格子顯示新增表單
    const table = document.getElementById('addTable');
    let selectedTd = null;
    table.querySelectorAll('td.editable').forEach(td => {
        td.addEventListener('click', function() {
            if (selectedTd) selectedTd.classList.remove('selected');
            this.classList.add('selected');
            selectedTd = this;
            const period = this.dataset.period;
            showAddPanel(period);
        });
    });
    function showAddPanel(period) {
        let html = `<h3>新增課程（時段：${period}）</h3>
        <form method='post' action='course_add.php'>
            <input type='hidden' name='Course_Period' value='${period}'>
            <input type='hidden' name='add_course' value='1'>
            <label>課程名稱：<input type='text' name='Course_Name' required></label><br>
            <label>學分數：<input type='number' name='Course_Credit' min='0' required></label><br>
            <label>必/選修：<select name='Course_Req'><option value='必修'>必修</option><option value='選修'>選修</option></select></label><br>
            <label>授課班級：<input type='text' name='Course_Class'></label><br>
            <label>講師：<input type='text' name='Course_Teachers'></label><br>
            <label>上課地點：<input type='text' name='Course_Location' required></label><br>
            <div style='margin-top:10px;'><button type='submit'>新增</button></div>
        </form>`;
        document.getElementById('addPanel').innerHTML = html;
        document.getElementById('addPanel').style.display = 'block';
    }
    </script>
</body>
</html>
