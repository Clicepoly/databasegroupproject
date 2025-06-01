<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

// 編輯課程（儲存）
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Course_ID']) && !isset($_POST['delete_course_id'])) {
    $Course_ID = intval($_POST['Course_ID']);
    $Course_Name = $_POST['Course_Name'] ?? '';
    $Course_Credit = $_POST['Course_Credit'] ?? '';
    $Course_Req = $_POST['Course_Req'] ?? '';
    $Course_Class = $_POST['Course_Class'] ?? null;
    $Course_Teachers = $_POST['Course_Teachers'] ?? null;
    $Course_Location = $_POST['Course_Location'] ?? '';
    $stmt = $mysqli->prepare("UPDATE CourseInfo SET Course_Name=?, Course_Credit=?, Course_Req=?, Course_Class=?, Course_Teachers=?, Course_Location=? WHERE Course_ID=?");
    $stmt->bind_param("sissssi", $Course_Name, $Course_Credit, $Course_Req, $Course_Class, $Course_Teachers, $Course_Location, $Course_ID);
    $stmt->execute();
    $stmt->close();
    echo "<script>if(window.parent && window.parent !== window){window.parent.postMessage('course_edit_success','*');}else{location.href='dashboard.php';}</script>";
    exit();
}

// 新增：處理刪除課程
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_course_id'])) {
    $delete_id = intval($_POST['delete_course_id']);
    $stmt = $mysqli->prepare("DELETE FROM CourseInfo WHERE Course_ID = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>if(window.parent && window.parent !== window){window.parent.postMessage('course_edit_success','*');}else{location.href='dashboard.php';}</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>課表編輯</title>
    <style>
        body { font-family: 'Microsoft JhengHei', Arial, sans-serif; background: #f3f4f6; }
        h1 { text-align: center; margin: 30px 0; }
        .main { display: flex; gap: 32px; align-items: flex-start; justify-content: center; }
        table { border-collapse: collapse; background: #fff; box-shadow: 0 2px 12px #eee; }
        th, td { border: 1px solid #ccc; width: 72px; height: 34px; text-align: center; position: relative; font-size: 0.98em; }
        th { background: #ffe066; color: #222; font-weight: bold; }
        td.editable { cursor: pointer; background: #f5f6fa; }
        td.editable:hover, td.editable.hovered { background: #e5e6ea; }
        .hover-list { position: absolute; left: 0; top: 100%; background: #fffbe8; border: 1px solid #ffe066; z-index: 20; min-width: 120px; box-shadow: 0 2px 8px #eee; }
        .hover-list li { padding: 4px 8px; font-size: 1em; cursor: pointer; }
        .hover-list li:hover { background: #ffe066; color: #007bff; }
        #editPanel { min-width:340px; background:#fffbe8; padding:18px 18px 12px 18px; border-radius:10px; box-shadow:0 2px 8px #eee; display:none; }
    </style>
</head>
<body>
    <h1>課表編輯</h1>
    <div class="main">
        <table id="editTable" data-courses='<?= htmlspecialchars(json_encode($courses)) ?>'>
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
                            <?php foreach($courses[$period] as $c): ?>
                                <div><?= htmlspecialchars($c['Course_Name']) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <?php endfor; ?>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <div id="editPanel"></div>
    </div>
    <script id="courseEditScript">
    // 封裝成 function 以便 dashboard.php 動態呼叫
    function initCourseEditTable() {
        const table = document.getElementById('editTable');
        if (!table) return;
        let hoverList;
        const courses = JSON.parse(table.getAttribute('data-courses'));
        table.querySelectorAll('td.editable').forEach(td => {
            td.onmouseenter = function() {
                const period = this.dataset.period;
                if (courses[period] && courses[period].length > 0) {
                    this.classList.add('hovered');
                    hoverList = document.createElement('ul');
                    hoverList.className = 'hover-list';
                    courses[period].forEach(c => {
                        const li = document.createElement('li');
                        li.textContent = c.Course_Name;
                        li.onclick = function(e) {
                            e.stopPropagation();
                            window.showEditPanel(c);
                            if (hoverList) hoverList.remove();
                        };
                        hoverList.appendChild(li);
                    });
                    this.appendChild(hoverList);
                }
            };
            td.onmouseleave = function() {
                this.classList.remove('hovered');
                if (hoverList) hoverList.remove();
            };
            td.onclick = function() {
                const period = this.dataset.period;
                if (courses[period] && courses[period].length > 0) {
                    let html = '<h3>選擇要編輯的課程</h3><ul style="list-style:none;padding:0;">';
                    courses[period].forEach(c => {
                        html += `<li style='padding:6px 0;cursor:pointer;color:#007bff;' onclick='window.showEditPanel(${JSON.stringify(c)})'>${c.Course_Name}</li>`;
                    });
                    html += '</ul>';
                    document.getElementById('editPanel').style.display = 'block';
                    document.getElementById('editPanel').innerHTML = html;
                } else {
                    document.getElementById('editPanel').style.display = 'none';
                }
            };
        });
    }
    window.showEditPanel = function(c) {
        let html = `<h3>編輯課程</h3>
        <form method='post' action='course_edit.php' style='margin-bottom:10px;'>
            <input type='hidden' name='Course_ID' value='${c.Course_ID}'>
            <label>課程名稱：<input type='text' name='Course_Name' value='${c.Course_Name||''}' required></label><br>
            <label>講師：<input type='text' name='Course_Teachers' value='${c.Course_Teachers||''}'></label><br>
            <label>學分數：<input type='number' name='Course_Credit' value='${c.Course_Credit||''}' min='0'></label><br>
            <label>必/選修：<select name='Course_Req'><option value='必修'>必修</option><option value='選修'>選修</option></select></label><br>
            <label>授課班級：<input type='text' name='Course_Class' value='${c.Course_Class||''}'></label><br>
            <label>上課地點：<input type='text' name='Course_Location' value='${c.Course_Location||''}'></label><br>
            <div style='margin-top:10px;'><button type='submit'>儲存</button>
            <button type='button' id='deleteBtn' style='margin-left:16px;background:#ff5e5e;color:#fff;border:none;padding:6px 18px;border-radius:5px;cursor:pointer;'>刪除</button></div>
        </form>`;
        document.getElementById('editPanel').innerHTML = html;
        document.getElementById('editPanel').style.display = 'block';
        document.querySelector("select[name='Course_Req']").value = c.Course_Req;
        // 刪除按鈕事件
        document.getElementById('deleteBtn').onclick = function() {
            if(confirm('確定要刪除此課程嗎？')){
                var form = document.createElement('form');
                form.method = 'post';
                form.action = 'course_edit.php';
                var idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'delete_course_id';
                idInput.value = c.Course_ID;
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        };
    };
    // 自動初始化（單獨開啟 course_edit.php 時）
    if (typeof window.dashboardCourseEditLoaded === 'undefined') {
        window.dashboardCourseEditLoaded = true;
        initCourseEditTable();
    }
    </script>
</body>
</html>
