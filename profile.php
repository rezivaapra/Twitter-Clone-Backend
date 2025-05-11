<?php

    session_start();
    require_once('dbconnect.php');

    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    if (!isset($_GET['id'])) {
        header('Location: home.php');
        exit();
    }

    try {
        $currentUserData = $db->users->findOne([
            '_id' => new MongoDB\BSON\ObjectId($_SESSION['user'])
        ]);
        
        $profileId = $_GET['id'];
        $profileData = $db->users->findOne([
            '_id' => new MongoDB\BSON\ObjectId($profileId)
        ]);

        if (!$profileData) {
            header('Location: home.php');
            exit();
        }

        function getRecentTweets($db, $profileId) {
            return $db->tweets->find([
                'authorId' => $profileId
            ], [
                'sort' => ['created' => -1]
            ]);
        }

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($profileData['username'] ?? 'User'); ?> | Twitter Clone</title>
    <style>
        .tweet { border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    
    <div class="tweets">
        <?php
        $recentTweets = getRecentTweets($db, $profileId);
        foreach ($recentTweets as $tweet) {
            echo '<div class="tweet">';
            echo '<strong>' . htmlspecialchars($tweet['authorName']) . '</strong><br>';
            echo '<p>' . htmlspecialchars($tweet['body']) . '</p>';
            echo '<small>' . htmlspecialchars($tweet['created'] ?? 'No date') . '</small>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>