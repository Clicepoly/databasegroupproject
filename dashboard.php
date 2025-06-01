<?php
session_start();
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: front.php");
    exit();
}
?>

<?php
include('db.php');
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>教師後台管理</title>
    <style>
        body {
            font-family: 'Segoe UI', 'Microsoft JhengHei', Arial, sans-serif;
            background: #f3f4f6;
            color: #222;
        }
        h1 {
            background: #fffbe8;
            color: #222;
            text-shadow: none;
            border-radius: 0 0 18px 18px;
            box-shadow: 0 2px 12px #eee;
            padding: 24px 0 20px 0;
            margin: 0 0 30px 0;
            text-align: center;
            letter-spacing: 2px;
        }
        nav {
            background: #fffbe8;
            color: #222;
            box-shadow: 0 2px 12px #eee;
            border-radius: 0 0 18px 18px;
            padding: 0 32px 0 32px;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
        }
        nav a {
            color: #222;
            background: none;
            text-shadow: none;
            font-weight: bold;
            font-size: 1.1em;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
            margin: 0 12px 0 0;
            display: inline-block;
        }
        nav a:last-child {
            margin-right: 0;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 32px #e0e0e0;
            border: 1.5px solid #e0e0e0;
            width: 97%;
            max-width: 900px;
            margin: 30px auto;
            padding: 32px 24px 24px 24px;
        }
        label[for^="functionSelect"] {
            color: #222;
        }
        select, input[type="text"], input[type="email"], input[type="date"] {
            background: #f5f6fa;
            color: #222;
            border: 1.5px solid #e0e0e0;
        }
        form {
            background: #e5e6ea;
            color: #222;
            border-radius: 12px;
            box-shadow: 0 2px 12px #e0e0e0;
            border: 1.5px solid #e0e0e0;
            margin-bottom: 18px;
        }
        form label {
            display: block;
            margin-bottom: 16px;
            color: #222;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="date"],
        form select {
            width: 98%;
            max-width: 480px;
            padding: 10px 12px;
            font-size: 1.08em;
            margin-top: 6px;
            margin-bottom: 2px;
            border: 1.5px solid #e0e0e0;
            border-radius: 5px;
            background: #f5f6fa;
            box-sizing: border-box;
            display: block;
        }
        form button {
            background: #ffe066;
            color: #222;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 2px 8px #e0e0e0;
            padding: 10px 28px;
            margin-top: 14px;
            font-size: 1.08em;
            transition: background 0.2s;
        }
        form button:hover {
            background: #ffd700;
            color: #007bff;
        }
        table {
            background: #e5e6ea;
            color: #222;
            border-radius: 10px;
            box-shadow: 0 2px 12px #e0e0e0;
            border: 1.5px solid #e0e0e0;
        }
        th {
            background: #fffbe8;
            color: #007bff;
        }
        tr:nth-child(even) {
            background: #f5f6fa;
        }
        td, th {
            border: 1px solid #e0e0e0;
        }
        a {
            color: #007bff;
        }
        a.delete-link {
            color: #ff5e5e;
        }
        a:hover {
            color: #222;
        }
        .menu-bar {
            background: #fffbe8;
            border-radius: 0 0 18px 18px;
            box-shadow: 0 2px 12px #eee;
            margin-bottom: 32px;
            padding: 0 0 0 0;
        }
        .menu-bar nav {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            padding: 0 32px;
            min-height: 56px;
            background: transparent;
            box-shadow: none;
        }
        .menu-bar nav a {
            color: #222;
            background: none;
            text-shadow: none;
            font-weight: bold;
            font-size: 1.1em;
            padding: 12px 18px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
            margin: 0 12px 0 0;
            display: inline-block;
        }
        .menu-bar nav a:last-child {
            margin-right: 0;
        }
        .menu-bar nav a:hover, .menu-bar nav a.active {
            background: #ffe066;
            color: #007bff;
        }
        /* 讓維護選單的下拉式選單更大（不變深色） */
        select#functionSelect,
        select#userFunctionSelect,
        select#eduFunctionSelect,
        select#expFunctionSelect,
        select#awardFunctionSelect,
        select#projectFunctionSelect,
        select#speechFunctionSelect,
        select#teachmatFunctionSelect,
        select#patentFunctionSelect,
        select#paperFunctionSelect {
            min-width: 200px;
            min-height: 38px;
            font-size: 1.08em;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: bold;
            background: #fffbe8;
            color: #222;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px #eee;
        }
        select#functionSelect:focus,
        select#userFunctionSelect:focus,
        select#eduFunctionSelect:focus,
        select#expFunctionSelect:focus,
        select#awardFunctionSelect:focus,
        select#projectFunctionSelect:focus,
        select#speechFunctionSelect:focus,
        select#teachmatFunctionSelect:focus,
        select#patentFunctionSelect:focus,
        select#paperFunctionSelect:focus {
            background: #fffbe8;
            color: #222;
            outline: 2px solid #888;
        }
        select#functionSelect option:checked,
        select#userFunctionSelect option:checked,
        select#eduFunctionSelect option:checked,
        select#expFunctionSelect option:checked,
        select#awardFunctionSelect option:checked,
        select#projectFunctionSelect option:checked,
        select#speechFunctionSelect option:checked,
        select#teachmatFunctionSelect option:checked,
        select#patentFunctionSelect option:checked,
        select#paperFunctionSelect option:checked {
            background: #ffe066;
            color: #222;
        }
        /* 查詢與修改/刪除教師表格欄位與文字顏色調整為黑色 */
        .teacher-table, .teacher-table th, .teacher-table td,
        #deleteSection table, #deleteSection th, #deleteSection td,
        #searchSection table, #searchSection th, #searchSection td,
        #deleteEduSection table, #deleteEduSection th, #deleteEduSection td,
        #searchEduSection table, #searchEduSection th, #searchEduSection td,
        #deleteExpSection table, #deleteExpSection th, #deleteExpSection td,
        #searchExpSection table, #searchExpSection th, #searchExpSection td,
        #deleteAwardSection table, #deleteAwardSection th, #deleteAwardSection td,
        #searchAwardSection table, #searchAwardSection th, #searchAwardSection td,
        #deleteProjectSection table, #deleteProjectSection th, #deleteProjectSection td,
        #searchProjectSection table, #searchProjectSection th, #searchProjectSection td,
        #deleteSpeechSection table, #deleteSpeechSection th, #deleteSpeechSection td,
        #searchSpeechSection table, #searchSpeechSection th, #searchSpeechSection td,
        #deleteTeachMatSection table, #deleteTeachMatSection th, #deleteTeachMatSection td,
        #searchTeachMatSection table, #searchTeachMatSection th, #searchTeachMatSection td,
        #deletePatentSection table, #deletePatentSection th, #deletePatentSection td,
        #searchPatentSection table, #searchPatentSection th, #searchPatentSection td {
            color: #111 !important;
        }
        /* 查詢與修改/刪除教師表格欄位與文字顏色調整為灰色 */
        .teacher-table, .teacher-table th, .teacher-table td {
            th, td { font-size: 0.97em; padding: 8px 4px; }
            nav {
                flex-direction: column;
                height: auto;
                padding: 0 10px;
                border-radius: 0 0 12px 12px;
                align-items: flex-start;
            }
            nav a {
                margin: 4px 0;
                font-size: 1em;
                width: 100%;
            }
            .menu-bar nav {
                flex-direction: column;
                align-items: flex-start;
                padding: 0 10px;
            }
            .menu-bar nav a {
                margin: 4px 0;
                font-size: 1em;
                width: 100%;
            }
        }
        table, th, td {
            font-size: 1.13em !important;
            padding: 14px 12px !important;
        }
        th {
            font-weight: bold;
        }
        select#courseFunctionSelect {
            min-width: 200px;
            min-height: 38px;
            font-size: 1.08em;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: bold;
            background: #fffbe8;
            color: #222;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px #eee;
        }
        select#courseFunctionSelect:focus {
            background: #fffbe8;
            color: #222;
            outline: 2px solid #888;
        }
        select#courseFunctionSelect option:checked {
            background: #ffe066;
            color: #222;
        }
    </style>
    <script>
        // 將所有 menu 的 section 預設隱藏，僅選擇功能後顯示
        function hideAllSections(menuId) {
            var menu = document.getElementById(menuId);
            if (!menu) return;
            var sections = menu.querySelectorAll('.section');
            sections.forEach(function(sec) { sec.style.display = 'none'; });
        }
        // 以 showSection 為例，其他 showXXXSection 也需同理調整
        function showSection() {
            var select = document.getElementById('functionSelect');
            hideAllSections('teacherMenu');
            if (select.value === 'add') {
                document.getElementById('addSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deleteSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchSection').style.display = 'block';
            }
        }
        function showUserSection() {
            var select = document.getElementById('userFunctionSelect');
            hideAllSections('userMenu');
            if (select.value === 'add') {
                document.getElementById('addUserSection').style.display = 'block';
            } else if (select.value === 'edit') {
                document.getElementById('editUserSection').style.display = 'block';
            }
        }
        function showEduSection() {
            var select = document.getElementById('eduFunctionSelect');
            hideAllSections('eduMenu');
            if (select.value === 'add') {
                document.getElementById('addEduSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deleteEduSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchEduSection').style.display = 'block';
            }
        }
        function showExpSection() {
            var select = document.getElementById('expFunctionSelect');
            hideAllSections('expMenu');
            if (select.value === 'add') {
                document.getElementById('addExpSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deleteExpSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchExpSection').style.display = 'block';
            }
        }
        function showAwardSection() {
            var select = document.getElementById('awardFunctionSelect');
            hideAllSections('awardMenu');
            if (select.value === 'add') {
                document.getElementById('addAwardSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deleteAwardSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchAwardSection').style.display = 'block';
            }
        }
        function showProjectSection() {
            var select = document.getElementById('projectFunctionSelect');
            hideAllSections('projectMenu');
            if (select.value === 'add') {
                document.getElementById('addProjectSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deleteProjectSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchProjectSection').style.display = 'block';
            }
        }
        function showSpeechSection() {
            var select = document.getElementById('speechFunctionSelect');
            hideAllSections('speechMenu');
            if (select.value === 'add') {
                document.getElementById('addSpeechSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deleteSpeechSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchSpeechSection').style.display = 'block';
            }
        }
        function showTeachMatSection() {
            var select = document.getElementById('teachmatFunctionSelect');
            hideAllSections('teachmatMenu');
            if (select.value === 'add') {
                document.getElementById('addTeachMatSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deleteTeachMatSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchTeachMatSection').style.display = 'block';
            }
        }
        function showPatentSection() {
            var select = document.getElementById('patentFunctionSelect');
            hideAllSections('patentMenu');
            if (select.value === 'add') {
                document.getElementById('addPatentSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deletePatentSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchPatentSection').style.display = 'block';
            }
        }
        function showPaperSection() {
            var select = document.getElementById('paperFunctionSelect');
            hideAllSections('paperMenu');
            if (select.value === 'add') {
                document.getElementById('addPaperSection').style.display = 'block';
            } else if (select.value === 'delete') {
                document.getElementById('deletePaperSection').style.display = 'block';
            } else if (select.value === 'search') {
                document.getElementById('searchPaperSection').style.display = 'block';
            }
        }
        window.onload = function() {
            // 預設全部功能選單都隱藏
            document.getElementById('teacherMenu').style.display = 'none';
            document.getElementById('userMenu').style.display = 'none';
            document.getElementById('eduMenu').style.display = 'none';
            document.getElementById('expMenu').style.display = 'none';
            document.getElementById('awardMenu').style.display = 'none';
            document.getElementById('projectMenu').style.display = 'none';
            document.getElementById('speechMenu').style.display = 'none';
            document.getElementById('teachmatMenu').style.display = 'none';
            document.getElementById('patentMenu').style.display = 'none';
            document.getElementById('paperMenu').style.display = 'none';
            document.getElementById('courseMenu').style.display = 'none';
            // 預設顯示 teacherMenu，並將 navTeacher 設為 active
            document.getElementById('teacherMenu').style.display = 'block';
            var navTeacher = document.getElementById('navTeacher');
            if (navTeacher) navTeacher.classList.add('active');
            // section 全部隱藏，下拉選單重設
            hideAllSections('teacherMenu');
            var select = document.getElementById('functionSelect');
            if (select) select.value = '';
        };
        // 維護選單按鈕點擊時只顯示下拉選單
        document.addEventListener('DOMContentLoaded', function() {
            var menuMap = {
                navTeacher: 'teacherMenu',
                navLogin: 'userMenu',
                navEdu: 'eduMenu',
                navExp: 'expMenu',
                navAward: 'awardMenu',
                navProject: 'projectMenu',
                navPaper: 'paperMenu',
                navSpeech: 'speechMenu',
                navTeachMat: 'teachmatMenu',
                navPatent: 'patentMenu',
                navCourse: 'courseMenu'
            };
            Object.keys(menuMap).forEach(function(navId) {
                var navBtn = document.getElementById(navId);
                if (navBtn) {
                    navBtn.onclick = function(e) {
                        e.preventDefault();
                        // 全部隱藏
                        Object.values(menuMap).forEach(function(menuId) {
                            var menu = document.getElementById(menuId);
                            if (menu) menu.style.display = 'none';
                        });
                        // 取消所有 active 樣式
                        Object.keys(menuMap).forEach(function(otherId) {
                            var otherBtn = document.getElementById(otherId);
                            if (otherBtn) otherBtn.classList.remove('active');
                        });
                        // 設定目前按鈕為 active
                        navBtn.classList.add('active');
                        // 顯示對應下拉選單
                        var menu = document.getElementById(menuMap[navId]);
                        if (menu) {
                            menu.style.display = 'block';
                            // 隱藏所有 section
                            var sections = menu.querySelectorAll('.section');
                            sections.forEach(function(sec) { sec.style.display = 'none'; });
                            // 重設下拉選單
                            var select = menu.querySelector('select');
                            if (select) select.value = '';
                        }
                    };
                }
            });
        });
        function showCourseSection() {
            var v = document.getElementById('courseFunctionSelect').value;
            document.getElementById('addCourseSection').style.display = (v==='add') ? 'block' : 'none';
            document.getElementById('editCourseSection').style.display = (v==='edit') ? 'block' : 'none';
            document.getElementById('deleteCourseSection').style.display = (v==='delete') ? 'block' : 'none';
        }
        // 查詢功能AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // 教師查詢
            var searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchForm);
                    fetch('info_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchResult').innerHTML = txt;
                    });
                };
            }
            // 學歷查詢
            var searchEduForm = document.getElementById('searchEduForm');
            if (searchEduForm) {
                searchEduForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchEduForm);
                    fetch('edu_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchEduResult').innerHTML = txt;
                    });
                };
            }
            // 經歷查詢
            var searchExpForm = document.getElementById('searchExpForm');
            if (searchExpForm) {
                searchExpForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchExpForm);
                    fetch('exp_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchExpResult').innerHTML = txt;
                    });
                };
            }
            // 獎項查詢
            var searchAwardForm = document.getElementById('searchAwardForm');
            if (searchAwardForm) {
                searchAwardForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchAwardForm);
                    fetch('award_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchAwardResult').innerHTML = txt;
                    });
                };
            }
            // 計畫查詢
            var searchProjectForm = document.getElementById('searchProjectForm');
            if (searchProjectForm) {
                searchProjectForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchProjectForm);
                    fetch('project_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchProjectResult').innerHTML = txt;
                    });
                };
            }
            // 演講查詢
            var searchSpeechForm = document.getElementById('searchSpeechForm');
            if (searchSpeechForm) {
                searchSpeechForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchSpeechForm);
                    fetch('speech_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchSpeechResult').innerHTML = txt;
                    });
                };
            }
            // 教材查詢
            var searchTeachMatForm = document.getElementById('searchTeachMatForm');
            if (searchTeachMatForm) {
                searchTeachMatForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchTeachMatForm);
                    fetch('teachmat_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchTeachMatResult').innerHTML = txt;
                    });
                };
            }
            // 專利查詢
            var searchPatentForm = document.getElementById('searchPatentForm');
            if (searchPatentForm) {
                searchPatentForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchPatentForm);
                    fetch('patent_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchPatentResult').innerHTML = txt;
                    });
                };
            }
            // 論文查詢
            var searchPaperForm = document.getElementById('searchPaperForm');
            if (searchPaperForm) {
                searchPaperForm.onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(searchPaperForm);
                    fetch('paper_search.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        document.getElementById('searchPaperResult').innerHTML = txt;
                    });
                };
            }
        });
    </script>
</head>
<body>
    <h1>教師後台管理</h1>
    <a href="index.php" style="float:right; margin: 20px 120px 0 0; font-size: 1.1em;">回前台</a>
    <a href="logout.php" style="float:right; margin: 20px 40px 0 0; font-size: 1.1em;">登出</a>
    <div class="menu-bar">
        <nav>
            <a href="#" id="navTeacher">維護教師資訊</a>
            <a href="#" id="navEdu">維護學歷資料</a>
            <a href="#" id="navExp">維護經歷資料</a>
            <a href="#" id="navAward">維護獲獎資料</a>
            <a href="#" id="navProject">維護計畫資料</a>
            <a href="#" id="navPaper">維護論文資料</a>
            <a href="#" id="navSpeech">維護演講資料</a>
            <a href="#" id="navTeachMat">維護教材與作品</a>
            <a href="#" id="navPatent">維護專利</a>
            <a href="#" id="navCourse">維護課表</a>
            <a href="#" id="navLogin">維護登入資訊</a>
        </nav>
    </div>
    <div class="container">
        <div id="teacherMenu" style="display:none;">
            <label for="functionSelect"><strong>請選擇功能：</strong></label>
            <select id="functionSelect" onchange="showSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增教師</option>
                <option value="delete">修改/刪除教師</option>
                <option value="search">查詢教師</option>
            </select>
            <div id="addSection" class="section" style="display:none;">
                <h2>新增教師</h2>
                <form action="info_add.php" method="post" enctype="multipart/form-data">
                    <label>教師編號：
                        <input type="text" name="Prof_ID" required>
                    </label>
                    <label>姓名：
                        <input type="text" name="Prof_Name" required>
                    </label>
                    <label>職稱：
                        <input type="text" name="Prof_title" required>
                    </label>
                    <label>電子郵件：
                        <input type="email" name="Prof_EmailAddress" required>
                    </label>
                    <label>電話分機：
                        <input type="text" name="Prof_ExtensionNumber" required>
                    </label>
                    <label>研究領域：
                        <input type="text" name="Prof_ResearchFields" required>
                    </label>
                    <label>大頭照：
                        <input type="file" name="Prof_Image" accept="image/*">
                    </label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deleteSection" class="section" style="display:none;">
                <h2>修改/刪除教師</h2>
                <table class="teacher-table">
                    <tr>
                        <th>教師編號</th>
                        <th>姓名</th>
                        <th>職稱</th>
                        <th>電子郵件</th>
                        <th>電話分機</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $result2 = $mysqli->query("SELECT * FROM teachers");
                    while($row = $result2->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_title']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_EmailAddress']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ExtensionNumber']) ?></td>
                        <td>
                            <a href="info_edit.php?id=<?= urlencode($row['Prof_ID']) ?>">修改</a>
                            <a class="delete-link" href="info_delete.php?id=<?= urlencode($row['Prof_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchSection" class="section" style="display:none;">
                <h2>查詢教師</h2>
                <form id="searchForm" method="post" action="info_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入姓名或職稱關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchResult"></div>
            </div>
        </div>

        <div id="userMenu" style="display:none;">
            <label for="userFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="userFunctionSelect" onchange="showUserSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增帳號</option>
                <option value="edit">修改帳號</option>
            </select>
            <div id="addUserSection" class="section" style="display:none;">
                <h2>新增帳號</h2>
                <form method="post" action="user_manage.php">
                    <input type="text" name="username" placeholder="帳號" required>
                    <input type="password" name="password" placeholder="密碼" required>
                    <button type="submit" name="add_user">新增帳號</button>
                </form>
            </div>
            <div id="editUserSection" class="section" style="display:none;">
                <h2>修改帳號</h2>
                <form method="post" action="user_manage.php">
                    <table>
                        <tr><th>帳號</th><th>操作</th></tr>
                        <?php 
                        $users = $mysqli->query("SELECT * FROM users");
                        while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <form class="edit-form" method="post" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required style="width:90px;">
                                    <input type="password" name="password" placeholder="新密碼(不修改可留空)">
                                    <button type="submit" name="edit_user">修改</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </form>
            </div>
        </div>

        <div id="eduMenu" style="display:none;">
            <label for="eduFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="eduFunctionSelect" onchange="showEduSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增學歷</option>
                <option value="delete">修改/刪除學歷</option>
                <option value="search">查詢學歷</option>
            </select>
            <div id="addEduSection" class="section" style="display:none;">
                <h2>新增學歷</h2>
                <form action="edu_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label>
                    <label>學校：<input type="text" name="EduBG_University" required></label>
                    <label>系所：<input type="text" name="EduBG_Department" required></label>
                    <label>學位：<input type="text" name="EduBG_Degree" required></label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deleteEduSection" class="section" style="display:none;">
                <h2>修改/刪除學歷</h2>
                <table>
                    <tr>
                        <th>學歷ID</th>
                        <th>教師編號</th>
                        <th>學校</th>
                        <th>系所</th>
                        <th>學位</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $resultEdu = $mysqli->query("SELECT * FROM EducationalBackground");
                    while($row = $resultEdu->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['EduBG_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['EduBG_University']) ?></td>
                        <td><?= htmlspecialchars($row['EduBG_Department']) ?></td>
                        <td><?= htmlspecialchars($row['EduBG_Degree']) ?></td>
                        <td>
                            <a href="edu_edit.php?id=<?= urlencode($row['EduBG_ID']) ?>">修改</a>
                            <a class="delete-link" href="edu_delete.php?id=<?= urlencode($row['EduBG_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchEduSection" class="section" style="display:none;">
                <h2>查詢學歷</h2>
                <form id="searchEduForm" method="post" action="edu_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入學校、系所或學位關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchEduResult"></div>
            </div>
        </div>

        <div id="expMenu" style="display:none;">
            <label for="expFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="expFunctionSelect" onchange="showExpSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增經歷</option>
                <option value="delete">修改/刪除經歷</option>
                <option value="search">查詢經歷</option>
            </select>
            <div id="addExpSection" class="section" style="display:none;">
                <h2>新增經歷</h2>
                <form action="exp_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label>
                    <label>經歷類型：<input type="text" name="Experience_type" required></label>
                    <label>職稱/職位：<input type="text" name="Experience_position" required></label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deleteExpSection" class="section" style="display:none;">
                <h2>修改/刪除經歷</h2>
                <table>
                    <tr>
                        <th>經歷ID</th>
                        <th>教師編號</th>
                        <th>經歷類型</th>
                        <th>職稱/職位</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $resultExp = $mysqli->query("SELECT * FROM Experience");
                    while($row = $resultExp->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Experience_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Experience_type']) ?></td>
                        <td><?= htmlspecialchars($row['Experience_position']) ?></td>
                        <td>
                            <a href="exp_edit.php?id=<?= urlencode($row['Experience_ID']) ?>">修改</a>
                            <a class="delete-link" href="exp_delete.php?id=<?= urlencode($row['Experience_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchExpSection" class="section" style="display:none;">
                <h2>查詢經歷</h2>
                <form id="searchExpForm" method="post" action="exp_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入經歷類型或職稱關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchExpResult"></div>
            </div>
        </div>

        <div id="awardMenu" style="display:none;">
            <label for="awardFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="awardFunctionSelect" onchange="showAwardSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增獎項</option>
                <option value="delete">修改/刪除獎項</option>
                <option value="search">查詢獎項</option>
            </select>
            <div id="addAwardSection" class="section" style="display:none;">
                <h2>新增獎項</h2>
                <form action="award_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label>
                    <label>學生姓名：<input type="text" name="Award_Advisee" required></label>
                    <label>作品/計畫名稱：<input type="text" name="Award_ProjectName" required></label>
                    <label>競賽名稱與名次：<input type="text" name="Award_CompName_Position" required></label>
                    <label>得獎日期：<input type="date" name="Award_Date" required></label>
                    <label>主辦單位：<input type="text" name="Award_organizer" required></label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deleteAwardSection" class="section" style="display:none;">
                <h2>修改/刪除獎項</h2>
                <table>
                    <tr>
                        <th>獎項ID</th>
                        <th>教師編號</th>
                        <th>學生姓名</th>
                        <th>作品/計畫名稱</th>
                        <th>競賽名稱與名次</th>
                        <th>得獎日期</th>
                        <th>主辦單位</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $resultAward = $mysqli->query("SELECT * FROM Award");
                    while($row = $resultAward->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Award_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Award_Advisee']) ?></td>
                        <td><?= htmlspecialchars($row['Award_ProjectName']) ?></td>
                        <td><?= htmlspecialchars($row['Award_CompName_Position']) ?></td>
                        <td><?= htmlspecialchars($row['Award_Date']) ?></td>
                        <td><?= htmlspecialchars($row['Award_organizer']) ?></td>
                        <td>
                            <a href="award_edit.php?id=<?= urlencode($row['Award_ID']) ?>">修改</a>
                            <a class="delete-link" href="award_delete.php?id=<?= urlencode($row['Award_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchAwardSection" class="section" style="display:none;">
                <h2>查詢獎項</h2>
                <form id="searchAwardForm" method="post" action="award_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入學生、作品、競賽或主辦單位關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchAwardResult"></div>
            </div>
        </div>

        <div id="projectMenu" style="display:none;">
            <label for="projectFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="projectFunctionSelect" onchange="showProjectSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增計畫</option>
                <option value="delete">修改/刪除計畫</option>
                <option value="search">查詢計畫</option>
            </select>
            <div id="addProjectSection" class="section" style="display:none;">
                <h2>新增計畫</h2>
                <form action="project_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label>
                    <label>計畫名稱：<input type="text" name="Project_Name" required></label>
                    <label>計畫期間：<input type="text" name="Project_Duration" required></label>
                    <label>計畫類型：
                        <select name="Project_Type" required>
                            <option value="">--請選擇--</option>
                            <option value="國科會">國科會</option>
                            <option value="產學合作">產學合作</option>
                        </select>
                    </label>
                    <label>擔任職務：<input type="text" name="Project_TakenPosition" required></label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deleteProjectSection" class="section" style="display:none;">
                <h2>修改/刪除計畫</h2>
                <table>
                    <tr>
                        <th>計畫ID</th>
                        <th>教師編號</th>
                        <th>計畫名稱</th>
                        <th>計畫期間</th>
                        <th>計畫類型</th>
                        <th>擔任職務</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $resultProject = $mysqli->query("SELECT * FROM Project");
                    while($row = $resultProject->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Project_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Project_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Project_Duration']) ?></td>
                        <td><?= htmlspecialchars($row['Project_Type']) ?></td>
                        <td><?= htmlspecialchars($row['Project_TakenPosition']) ?></td>
                        <td>
                            <a href="project_edit.php?id=<?= urlencode($row['Project_ID']) ?>">修改</a>
                            <a class="delete-link" href="project_delete.php?id=<?= urlencode($row['Project_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchProjectSection" class="section" style="display:none;">
                <h2>查詢計畫</h2>
                <form id="searchProjectForm" method="post" action="project_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入計畫名稱、期間、類型或職務關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchProjectResult"></div>
            </div>
        </div>

        <div id="speechMenu" style="display:none;">
            <label for="speechFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="speechFunctionSelect" onchange="showSpeechSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增演講</option>
                <option value="delete">修改/刪除演講</option>
                <option value="search">查詢演講</option>
            </select>
            <div id="addSpeechSection" class="section" style="display:none;">
                <h2>新增演講</h2>
                <form action="speech_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label>
                    <label>演講名稱：<input type="text" name="Speech_Name" required></label>
                    <label>對象/場合：<input type="text" name="Speech_Audience" required></label>
                    <label>日期：<input type="date" name="Speech_Date" required></label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deleteSpeechSection" class="section" style="display:none;">
                <h2>修改/刪除演講</h2>
                <table>
                    <tr>
                        <th>演講ID</th>
                        <th>教師編號</th>
                        <th>演講名稱</th>
                        <th>對象/場合</th>
                        <th>日期</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $resultSpeech = $mysqli->query("SELECT * FROM Speech");
                    while($row = $resultSpeech->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Speech_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Speech_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Speech_Audience']) ?></td>
                        <td><?= htmlspecialchars($row['Speech_Date']) ?></td>
                        <td>
                            <a href="speech_edit.php?id=<?= urlencode($row['Speech_ID']) ?>">修改</a>
                            <a class="delete-link" href="speech_delete.php?id=<?= urlencode($row['Speech_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchSpeechSection" class="section" style="display:none;">
                <h2>查詢演講</h2>
                <form id="searchSpeechForm" method="post" action="speech_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入演講名稱或對象關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchSpeechResult"></div>
            </div>
        </div>

        <div id="teachmatMenu" style="display:none;">
            <label for="teachmatFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="teachmatFunctionSelect" onchange="showTeachMatSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增教材與作品</option>
                <option value="delete">修改/刪除教材與作品</option>
                <option value="search">查詢教材與作品</option>
            </select>
            <div id="addTeachMatSection" class="section" style="display:none;">
                <h2>新增教材與作品</h2>
                <form action="teachmat_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label>
                    <label>作者：<input type="text" name="TeachMat_Author" required></label>
                    <label>教材/作品名稱：<input type="text" name="TeachMat_Name" required></label>
                    <label>出版社/發表單位：<input type="text" name="TeachMat_Publisher" required></label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deleteTeachMatSection" class="section" style="display:none;">
                <h2>修改/刪除教材與作品</h2>
                <table>
                    <tr>
                        <th>教材ID</th>
                        <th>教師編號</th>
                        <th>作者</th>
                        <th>教材/作品名稱</th>
                        <th>出版社/發表單位</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $resultTeachMat = $mysqli->query("SELECT * FROM TeachingMaterials");
                    while($row = $resultTeachMat->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['TeachMat_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['TeachMat_Author']) ?></td>
                        <td><?= htmlspecialchars($row['TeachMat_Name']) ?></td>
                        <td><?= htmlspecialchars($row['TeachMat_Publisher']) ?></td>
                        <td>
                            <a href="teachmat_edit.php?id=<?= urlencode($row['TeachMat_ID']) ?>">修改</a>
                            <a class="delete-link" href="teachmat_delete.php?id=<?= urlencode($row['TeachMat_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchTeachMatSection" class="section" style="display:none;">
                <h2>查詢教材與作品</h2>
                <form id="searchTeachMatForm" method="post" action="teachmat_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入作者、名稱或出版社關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchTeachMatResult"></div>
            </div>
        </div>

        <div id="patentMenu" style="display:none;">
            <label for="patentFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="patentFunctionSelect" onchange="showPatentSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增專利</option>
                <option value="delete">修改/刪除專利</option>
                <option value="search">查詢專利</option>
            </select>
            <div id="addPatentSection" class="section" style="display:none;">
                <h2>新增專利</h2>
                <form action="patent_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label>
                    <label>專利類型：<input type="text" name="Patent_Type" required></label>
                    <label>專利名稱/內容：<input type="text" name="Patent_Name" required></label>
                    <label>專利時間：<input type="text" name="Patent_Term" required></label>
                    <button type="submit">新增</button>
                </form>
            </div>
            <div id="deletePatentSection" class="section" style="display:none;">
                <h2>修改/刪除專利</h2>
                <table>
                    <tr>
                        <th>專利ID</th>
                        <th>教師編號</th>
                        <th>專利類型</th>
                        <th>專利名稱/內容</th>
                        <th>專利時間</th>
                        <th>操作</th>
                    </tr>
                    <?php
                    $resultPatent = $mysqli->query("SELECT * FROM Patent");
                    while($row = $resultPatent->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Patent_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Patent_Type']) ?></td>
                        <td><?= htmlspecialchars($row['Patent_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Patent_Term']) ?></td>
                        <td>
                            <a href="patent_edit.php?id=<?= urlencode($row['Patent_ID']) ?>">修改</a>
                            <a class="delete-link" href="patent_delete.php?id=<?= urlencode($row['Patent_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchPatentSection" class="section" style="display:none;">
                <h2>查詢專利</h2>
                <form id="searchPatentForm" method="post" action="patent_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入專利類型或名稱關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchPatentResult"></div>
            </div>
        </div>

        <div id="paperMenu" style="display:none;">
            <label for="paperFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="paperFunctionSelect" onchange="showPaperSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增論文</option>
                <option value="delete">修改/刪除論文</option>
                <option value="search">查詢論文</option>
            </select>
            <div id="addPaperSection" class="section" style="display:none;">
                <h2>新增論文</h2>
                <form action="paper_add.php" method="post">
                    <label>教師編號：<input type="text" name="Prof_ID" required></label><br>
                    <label>作者（多位請以空格分隔）：<input type="text" name="Paper_Author" placeholder="作者1 作者2 ..." required></label><br>
                    <label>論文標題：<input type="text" name="Paper_Title" required></label><br>
                    <label>類型：
                        <select name="Paper_Category" id="Paper_Category_dash" onchange="updateFieldsDash()" required>
                            <option value="期刊">期刊</option>
                            <option value="會議">會議</option>
                            <option value="專書">專書</option>
                        </select>
                    </label><br>
                    <div id="journal_fields_dash" style="display:none;">
                        <label>期刊名稱：<input type="text" name="Paper_JournalName"></label><br>
                    </div>
                    <div id="conference_fields_dash" style="display:none;">
                        <label>會議名稱：<input type="text" name="Paper_ConferenceName"></label><br>
                        <label>地點：<input type="text" name="Paper_ConferenceLocation"></label><br>
                    </div>
                    <div id="book_fields_dash" style="display:none;">
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
                <script>
                function updateFieldsDash() {
                    var cat = document.getElementById('Paper_Category_dash').value;
                    document.getElementById('journal_fields_dash').style.display = (cat === '期刊') ? '' : 'none';
                    document.getElementById('conference_fields_dash').style.display = (cat === '會議') ? '' : 'none';
                    document.getElementById('book_fields_dash').style.display = (cat === '專書') ? '' : 'none';
                }
                document.getElementById('Paper_Category_dash').addEventListener('change', updateFieldsDash);
                window.addEventListener('DOMContentLoaded', updateFieldsDash);
                </script>
            </div>
            <div id="deletePaperSection" class="section" style="display:none;">
                <h2>修改/刪除論文</h2>
                <table>
                    <tr>
                        <th>ID</th><th>教師編號</th><th>作者</th><th>標題</th><th>類型</th><th>操作</th>
                    </tr>
                    <?php $resultPaper = $mysqli->query("SELECT * FROM Paper");
                    while($row = $resultPaper->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Paper_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Prof_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Paper_Author']) ?></td>
                        <td><?= htmlspecialchars($row['Paper_Title']) ?></td>
                        <td><?= htmlspecialchars($row['Paper_Category']) ?></td>
                        <td>
                            <a href="paper_edit.php?id=<?= urlencode($row['Paper_ID']) ?>">修改</a>
                            <a class="delete-link" href="paper_delete.php?id=<?= urlencode($row['Paper_ID']) ?>" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div id="searchPaperSection" class="section" style="display:none;">
                <h2>查詢論文</h2>
                <form id="searchPaperForm" method="post" action="paper_search.php" onsubmit="return false;">
                    <input type="text" name="search_keyword" placeholder="請輸入作者、標題、類型、期刊、會議、書名、出版社關鍵字" required>
                    <button type="submit">查詢</button>
                </form>
                <div id="searchPaperResult"></div>
            </div>
        </div>

        <div id="courseMenu" style="display:none;">
            <label for="courseFunctionSelect"><strong>請選擇功能：</strong></label>
            <select id="courseFunctionSelect" onchange="showCourseSection()">
                <option value="">-- 請選擇 --</option>
                <option value="add">新增課表</option>
                <option value="edit">修改/刪除課表</option>
            </select>
            <div id="addCourseSection" class="section" style="display:none;">
                <h2 style="margin-bottom:18px;">新增課表</h2>
                <?php include('course_add.php'); ?>
            </div>
            <div id="editCourseSection" class="section" style="display:none;">
                <h2 style="margin-bottom:18px;">修改課表</h2>
                <?php include('course_edit.php'); ?>
            </div>
            <div id="deleteCourseSection" class="section" style="display:none;">
                <h2 style="margin-bottom:18px;">刪除課表</h2>
                <div style="color:#888;">（請於課表修改頁面刪除課程，或由管理員進行批次刪除）</div>
            </div>
        </div>

        <script>
        function showCourseSection() {
            var v = document.getElementById('courseFunctionSelect').value;
            document.getElementById('addCourseSection').style.display = (v==='add') ? 'block' : 'none';
            document.getElementById('editCourseSection').style.display = (v==='edit') ? 'block' : 'none';
            document.getElementById('deleteCourseSection').style.display = (v==='delete') ? 'block' : 'none';
        }
        // 課表資料結構
        let courseSchedule = Array.from({length:14},()=>Array.from({length:5},()=>({Course_Name:'',Course_Teachers:'',Course_Credit:'',Course_Req:'必修',Course_Class:'',Course_Location:''})));
        function renderCourseTable() {
            document.querySelectorAll('#courseTable td.course-editable').forEach(td => {
                const row = td.dataset.row-1, col = td.dataset.col-1;
                td.textContent = courseSchedule[row][col].Course_Name || '';
            });
        }
        if (document.getElementById('courseTable')) {
            document.querySelectorAll('#courseTable td.course-editable').forEach(td => {
                td.onclick = function() {
                    const row = this.dataset.row-1, col = this.dataset.col-1;
                    const panel = document.getElementById('courseFormPanel');
                    panel.style.display = 'block';
                    panel.innerHTML = `
                        <h3>課程資訊（${row+1} 節 ${['Mon','Tue','Wed','Thu','Fri'][col]}）</h3>
                        <form id="courseEditForm">
                            <label>課程名稱：<input type="text" name="Course_Name" value="${courseSchedule[row][col].Course_Name||''}" required></label><br>
                            <label>學分數：<input type="number" name="Course_Credit" value="${courseSchedule[row][col].Course_Credit||''}" min="0"></label><br>
                            <label>必/選修：<select name="Course_Req"><option value="必修">必修</option><option value="選修">選修</option></select></label><br>
                            <label>授課班級：<input type="text" name="Course_Class" value="${courseSchedule[row][col].Course_Class||''}"></label><br>
                            <label>講師（可多位，以空格分隔）：<input type="text" name="Course_Teachers" value="${courseSchedule[row][col].Course_Teachers||''}"></label><br>
                            <label>上課地點：<input type="text" name="Course_Location" value="${courseSchedule[row][col].Course_Location||''}"></label><br>
                            <div style="margin-top:10px;"><button type="submit">儲存</button> <button type="button" id="cancelEditBtn">取消</button></div>
                        </form>
                    `;
                    panel.querySelector('select[name="Course_Req"]').value = courseSchedule[row][col].Course_Req;
                    panel.querySelector('#courseEditForm').onsubmit = function(e) {
                        e.preventDefault();
                        courseSchedule[row][col] = {
                            Course_Name: this.Course_Name.value,
                            Course_Credit: this.Course_Credit.value,
                            Course_Req: this.Course_Req.value,
                            Course_Class: this.Course_Class.value,
                            Course_Teachers: this.Course_Teachers.value,
                            Course_Location: this.Course_Location.value
                        };
                        renderCourseTable();
                        panel.style.display = 'none';
                    };
                    panel.querySelector('#cancelEditBtn').onclick = function() {
                        panel.style.display = 'none';
                    };
                };
            });
            document.getElementById('saveCourseBtn').onclick = function() {
                // 將 row/col 轉為 Course_Period，並確保欄位順序正確
                let scheduleWithPeriod = courseSchedule.map((rowArr, i) => rowArr.map((colObj, j) => {
                    return {
                        Course_Name: colObj.Course_Name,
                        Course_Credit: colObj.Course_Credit,
                        Course_Req: colObj.Course_Req,
                        Course_Class: colObj.Course_Class,
                        Course_Teachers: colObj.Course_Teachers,
                        Course_Period: (i+1)+'-'+(j+1),
                        Course_Location: colObj.Course_Location
                    };
                }));
                fetch('course_add.php', {
                    method: 'POST',
                    headers: {'Content-Type':'application/x-www-form-urlencoded'},
                    body: 'schedule='+encodeURIComponent(JSON.stringify(scheduleWithPeriod))
                }).then(r=>r.text()).then(txt=>{
                    alert('課表已儲存！');
                    location.reload();
                });
            };
            renderCourseTable();
        }
    </script>
</body>
</html>