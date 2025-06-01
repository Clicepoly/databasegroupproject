<?php
session_start();
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    include('db.php');
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            $_SESSION['is_login'] = true;
            $_SESSION['username'] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "帳號或密碼錯誤";
        }
    } else {
        $error = "帳號或密碼錯誤";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>登入後台</title>
    <style>
        body { font-family: "Microsoft JhengHei", Arial, sans-serif; background: #e3f0ff; }
        .login-box {
            background: #fff; padding: 30px; margin: 80px auto; width: 320px;
            border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        label { display: block; margin-bottom: 12px; }
        input[type="text"], input[type="password"] {
            width: 95%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            margin-top: 10px; padding: 8px 20px; background: #007bff; color: #fff;
            border: none; border-radius: 4px; cursor: pointer; font-size: 1em;
        }
        .error { color: #dc3545; margin-bottom: 12px; }
    </style>
</head>
<body>
    <form class="login-box" method="post">
        <h2>後台登入</h2>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <label>帳號：<input type="text" name="username" required></label>
        <label>密碼：<input type="password" name="password" required></label>
        <button type="submit">登入</button>
    </form>
</body>
</html>