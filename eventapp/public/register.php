<?php
// public/register.php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$first || !$last || !$email || !$pass) {
        $error = "All fields required.";
    } else {
        // Check email unique
        $stmt = $pdo->prepare('SELECT user_id FROM Users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO Users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)');
            $stmt->execute([$first, $last, $email, $hash]);
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            header('Location: /index.php');
            exit;
        }
    }
}
?>
<!-- Simple HTML form -->
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
<h2>Register</h2>
<?php if(!empty($error)) echo '<p style="color:red;">'.e($error).'</p>'; ?>
<form method="post">
  <label>First name <input name="first_name" required></label><br>
  <label>Last name <input name="last_name" required></label><br>
  <label>Email <input name="email" type="email" required></label><br>
  <label>Password <input name="password" type="password" required></label><br>
  <button>Register</button>
</form>
<p>Already registered? <a href="/login.php">Log in</a></p>
</body>
</html>
