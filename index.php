<?php

    session_start();
    require_once('dbconnect.php');

    if (isset($_SESSION['user'])) {
        header('Location: home.php');
        exit();
    }

    $error = false;

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = "Username dan password harus diisi";
        } else {
            try {
                $hashedInputPassword = hash('sha256', $password);
                
                $result = $db->users->findOne([
                    'username' => $username,
                    'password' => $hashedInputPassword
                ]);
                
                if ($result) {
                    $_SESSION['user'] = (string)$result->_id;
                    $_SESSION['username'] = $username;
                    
                    header('Location: home.php');
                    exit();
                } else {
                    $error = "Username atau password salah";
                }
            } catch (Exception $e) {
                $error = "Terjadi kesalahan sistem";
            }
        }
    }

?>

<html>
    <head>
        <title>Login | Twitter Clone</title>
    </head>
    <body>
        <?php if ($error): ?>
            <div style="color: red;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="post" action="index.php">
            <fieldset>
                <label for="username">Username: </label>
                <input type="text" name="username" required /><br>
                <label for="password">Password: </label>
                <input type="password" name="password" required /><br>
                <input type="submit" value="Login" />
            </fieldset>
        </form>
        <a href="register.php">No account? Register here.</a>
    </body>
</html>