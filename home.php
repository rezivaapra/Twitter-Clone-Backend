<?php 

    session_start();
    require_once('dbconnect.php');

    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    try {
        $userId = new MongoDB\BSON\ObjectId($_SESSION['user']);
        $userData = $db->users->findOne(['_id' => $userId]);

        if ($userData) {
            $_SESSION['username'] = $userData->username ?? 'Unknown';
            $_SESSION['user_id'] = (string)$userData->_id;
        } else {
            session_destroy();
            header('Location: index.php');
            exit();
        }
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
        session_destroy();
        header('Location: index.php');
        exit();
    }

    function get_recent_tweets($db, $userIdStr) {
        try {
            $userId = new MongoDB\BSON\ObjectId($userIdStr);

            $followingCursor = $db->following->find(['follower' => $userId]);
            $following = iterator_to_array($followingCursor);

            $users_following = array_map(function($entry) {
                return (string)$entry['user'];
            }, $following);

            $tweetsCursor = $db->tweets->find(
                ['authorId' => ['$in' => $users_following]],
                ['sort' => ['created' => -1]]
            );

            return iterator_to_array($tweetsCursor);
        } catch (Exception $e) {
            error_log('Error fetching tweets: ' . $e->getMessage());
            return [];
        }
    }

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home | Twitter Clone</title>
</head>
<body>
    <?php include('header.php'); ?>

    <form method="post" action="create_tweet.php">
        <fieldset>
            <label for="tweet">What's happening?</label><br>
            <textarea name="body" rows="4" cols="50" required></textarea><br>
            <input type="submit" value="Tweet" />
        </fieldset>
    </form>

    <div>
        <p><b>Tweets from people you're following:</b></p>
        <?php
            $recent_tweets = get_recent_tweets($db, $_SESSION['user']);
            if (empty($recent_tweets)) {
                echo "<p>No tweets found.</p>";
            } else {
                foreach ($recent_tweets as $tweet) {
                    echo '<p><a href="profile.php?id=' . htmlspecialchars($tweet['authorId']) . '">' . htmlspecialchars($tweet['authorName']) . '</a></p>';
                    echo '<p>' . htmlspecialchars($tweet['body']) . '</p>';
                    echo '<p>' . htmlspecialchars($tweet['created']) . '</p>';
                    echo '<hr>';
                }
            }
        ?>
    </div>
</body>
</html>
