<?php

    if (!isset($_SESSION)) {
        session_start();
    }

    $username = $_SESSION['username'];

?>

<div class="header">
    Welcome, <?php echo htmlspecialchars($username); ?>!
    [<a href="home.php">Home</a>]
    [<a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>">View Profile</a>]
    [<a href="userlist.php">View Users List</a>]
    [<a href="logout.php">Log Out</a>]
</div>