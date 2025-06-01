<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>課表瀏覽</title>
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
        #infoPanel { min-width:340px; background:#fffbe8; padding:18px 18px 12px 18px; border-radius:10px; box-shadow:0 2px 8px #eee; display:none; margin-left:24px; }
        .course-list { font-size:0.98em; color:#007bff; margin:2px 0 0 0; }
        /* index.php 樣式 navbar */
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
    <h1>課表瀏覽</h1>
    <div class="main">
        <table id="viewTable" data-courses='<?= htmlspecialchars(json_encode($courses)) ?>'>
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
        <div id="infoPanel"></div>
    </div>
    <script>
    // 時段對應表
    const dayMap = ['','星期一','星期二','星期三','星期四','星期五'];
    const timeMap = [
        '',
        '08:10~09:00',
        '09:10~10:00',
        '10:10~11:00',
        '11:10~12:00',
        '12:10~13:00',
        '13:10~14:00',
        '14:10~15:00',
        '15:10~16:00',
        '16:10~17:00',
        '17:10~18:00',
        '18:30~19:20',
        '19:25~20:15',
        '20:20~21:10',
        '21:15~22:05'
    ];
    function periodText(period) {
        const [row, col] = period.split('-').map(Number);
        return dayMap[col] + ' ' + timeMap[row];
    }
    // 滑鼠互動與資訊顯示
    const table = document.getElementById('viewTable');
    const infoPanel = document.getElementById('infoPanel');
    const courses = JSON.parse(table.getAttribute('data-courses'));
    let hoverList;
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
                        showInfoPanel(period, c.Course_ID);
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
                showInfoPanel(period);
            } else {
                infoPanel.style.display = 'none';
            }
        };
    });
    function showInfoPanel(period, courseId) {
        let html = '';
        if (courses[period] && courses[period].length > 0) {
            html += `<h3>${periodText(period)} 課程</h3><ul style="list-style:none;padding:0;">`;
            courses[period].forEach(c => {
                html += `<li style='padding:6px 0;cursor:pointer;color:#007bff;' onclick='window.showCourseDetail(${JSON.stringify(c)})'>${c.Course_Name}</li>`;
            });
            html += '</ul>';
        }
        infoPanel.innerHTML = html;
        infoPanel.style.display = 'block';
    }
    window.showCourseDetail = function(c) {
        let periodStr = periodText(c.Course_Period);
        let html = `<h3>課程資訊</h3>
        <div><b>課程名稱：</b>${c.Course_Name}</div>
        <div><b>學分數：</b>${c.Course_Credit}</div>
        <div><b>必/選修：</b>${c.Course_Req}</div>
        <div><b>授課班級：</b>${c.Course_Class||''}</div>
        <div><b>講師：</b>${c.Course_Teachers||''}</div>
        <div><b>上課地點：</b>${c.Course_Location||''}</div>
        <div><b>時段：</b>${periodStr}</div>
        <button onclick='window.closeCourseDetail()' style='margin-top:12px;padding:6px 18px;border-radius:6px;background:#ffe066;color:#222;border:none;cursor:pointer;'>返回</button>`;
        infoPanel.innerHTML = html;
        infoPanel.style.display = 'block';
    }
    window.closeCourseDetail = function() {
        infoPanel.style.display = 'none';
    }
    </script>
</body>
</html>
