<?php

    session_start();
    require_once('dbconnect.php');

    if (isset($_SESSION['user'])) {
        header('Location: home.php');
        exit();
    }

    $error = false;
    $success = false;

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = "Username dan password harus diisi";
        } elseif (strlen($password) < 6) {
            $error = "Password minimal 6 karakter";
        } else {
            try {
                $existingUser = $db->users->findOne(['username' => $username]);
                
                if ($existingUser) {
                    $error = "Username sudah digunakan";
                } else {
                    $hashedPassword = hash('sha256', $password);
                    
                    $result = $db->users->insertOne([
                        'username' => $username,
                        'password' => $hashedPassword,
                        'created_at' => new MongoDB\BSON\UTCDateTime()
                    ]);
                    
                    if ($result->getInsertedCount() > 0) {
                        $success = "Registrasi berhasil! Silakan login";
                    } else {
                        $error = "Gagal melakukan registrasi";
                    }
                }
            } catch (Exception $e) {
                $error = "Terjadi kesalahan sistem: " . $e->getMessage();
            }
        }
    }

?>

<html>
    <head>
        <title>Register | Twitter Clone</title>
        <style>
            .error { color: red; }
            .success { color: green; }
        </style>
    </head>
    <body>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="post" action="register.php">
            <fieldset>
                <legend>Register</legend>
                <label for="username">Username: </label>
                <input type="text" name="username" required /><br>
                
                <label for="password">Password: </label>
                <input type="password" name="password" required minlength="6" /><br>
                
                <input type="submit" value="Sign Up" />
            </fieldset> 
        </form>
        <a href="index.php">Already have an account? Login here.</a>
    </body>
</html>