<?php
session_start();

// Check if the UserId is set in the session
if (isset($_SESSION['UserId'])) {
    $userId = $_SESSION['UserId'];
    echo "User ID: " . $userId;
} else {
  header("Location: login.html");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Budget Buddy</title>
  <!-- Include your CSS files here -->
</head>
<body>
  <main>
    <section id="dashboardSection">
      <!-- The PHP block will output the user's ID here -->
      <?php if (isset($userId)) { ?>
        <p>Welcome back, your User ID is: <?php echo htmlspecialchars($userId); ?></p>
        <!-- Logout form -->
        <form action="logout.php" method="post">
          <button type="submit" name="logout">Logout</button>
        </form>
      <?php } else { ?>
        <p>User ID is not set in the session. Please <a href="login.html">login</a>.</p>
      <?php } ?>
    </section>
  </main>
</body>
</html>
