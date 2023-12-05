    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">Budget Buddy</a>
      <div class="ml-auto">
        <a href="#"><i class="menu-icon fas fa-bell"></i></a>
        <a href="#"><i class="menu-icon fas fa-cog"></i></a>
        <form action="logout.php" method="post">
          <button type="submit" name="logout" class="logout-bt">
            <i class="menu-icon fas fa-sign-out-alt"></i>
          </button>
        </form>
      </div>
      <?php if (isset($userId)) { ?>      
      <div class="user-profile">
        <img src="profile2.png" alt="User ID: <?php echo htmlspecialchars($userId); ?>" width="40" height="40">
      </div>
      <?php } else { ?>
        <p>User ID is not set in the session. Please <a href="login.html">login</a>.</p>
      <?php } ?>      
    </nav>