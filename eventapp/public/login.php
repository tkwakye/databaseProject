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

        // regenerate session id
        session_regenerate_id(true);

        // ✅ Store user ID
        $_SESSION['user_id'] = $user['user_id'];

        // ✅ Store ROLE (admin or member)
        $_SESSION['role'] = $user['role'];

        header('Location: /index.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Login</h2>
<?php if(!empty($error)) echo '<p style="color:red;">'.e($error).'</p>'; ?>
<form method="post">
  <label>Email <input name="email" type="email" required></label><br>
  <label>Password <input name="password" type="password" required></label><br>
  <button>Login</button>
</form>
<p>No account? <a href="/register.php">Register</a></p>
</body>
</html>
