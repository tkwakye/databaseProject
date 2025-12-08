<?php
// public/login.php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM Users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        header('Location: /index.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .form-container {
        background: white;
        padding: 25px;
        border-radius: 6px;
        width: 320px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
    }

    h2 {
        margin-bottom: 15px;
    }

    .error-box {
        background: #ffe5e5;
        border: 1px solid #ff9e9e;
        color: #b30000;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        font-size: 14px;
    }

    label {
        display: block;
        text-align: left;
        margin-bottom: 10px;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-top: 3px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        margin-top: 10px;
        width: 100%;
        padding: 10px;
        background: #007BFF;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 15px;
        cursor: pointer;
    }

    button:hover {
        background: #0056b3;
    }

    .switch-link {
        margin-top: 12px;
        font-size: 14px;
    }
</style>

</head>
<body>

<div class="form-container">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error-box">
             <?php echo e($error); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Email
            <input name="email" type="email" required>
        </label>

        <label>Password
            <input name="password" type="password" required>
        </label>

        <button>Login</button>
    </form>

    <p class="switch-link">
        No account? <a href="/register.php">Register</a>
    </p>
</div>

</body>
</html>
