<?php

    session_start();
    require_once('dbconnect.php');

    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    $userData = $db->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user'])]);

    function get_user_list($db) {
        $result = $db->users->find();
        $users =  iterator_to_array($result);
        return $users;
    }
    
?>

<html>
    <head>
        <title>User List | Twitter Clone</title>
    </head>
    <body>
        <?php include('header.php'); ?>
        <div>
            <p><b>List of users:</b></p>
            <?php
            $user_list = get_user_list($db);
            foreach ($user_list as $user) {
                echo '<span>' . $user['username'] . '</span>';
                echo ' [<a href="profile.php?id=' . $user['_id'] . '">View Profile</a>]';
                echo ' [<a href="follow.php?id=' . $user['_id'] . '">Follow</a>]';
                echo '<hr>';
            }
            ?>
        </div>
    </body>
</html>