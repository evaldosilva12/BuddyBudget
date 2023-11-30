<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budget Buddy</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
  <style>
    body {
      background: #edf0f5;
      font-family: Raleway, "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 14px;
      line-height: 1.428571429;
      color: #6a6c6f;
    }

    .navbar {
      padding: 10px;
    }

    .navbar-brand {
      flex-grow: 1;
      color: white!important;
      font-weight: 700;
    }

    .user-profile {
      display: flex;
      align-items: center;
      margin: 0 0 0 20px;
    }

    .user-profile img {
      border-radius: 50%;
    }

    .menu-section {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      background-color: #f8f9fa;
      width: 100%;
      -webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, .08);
      box-shadow: 0 2px 4px rgba(0, 0, 0, .08);
    }

    .menu-item {
      flex: 1;
      text-align: center;
      padding: 18px 15px 15px;
    }

    .menu-item:hover {
      background-color: rgba(237,240,245,.5);
      color: #10c469;
    }

    .menu-item a {
      color: #6a6c6f;
    }

    .menu-item a:hover {
      color: #10c469;
    }

    .menu-icon {
      font-size: 1.5rem;
      display: block;
      margin-bottom: 5px;
    }

    main {
      padding: 30px;
    }

    .footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      background-color: #f5f5f5;
      color: #8a8a8a;
      text-align: center;
      padding: 10px 0;
      border-top: 1px solid #e7e7e7;
    }

    .ml-auto a {
      color: white;
    }

    .ml-auto a:hover {
      color: white;
    }

    .ml-auto,
    .mx-auto {
      display: flex;
      gap: 20px;
    }

    .bg-light {
      background-color: #10c469!important;
    }

    .menu-item .fa-solid,
    .fas {
      font-size: 16px;
    }

    .nav-link {
      padding: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .menu-item {
        padding: 10px;
        flex-basis: 50%;
        /* Each item takes half the width on smaller screens */
      }
    }
  </style>
</head>

<body>

<header>
  <?php include 'navbar.php';?>
  </header>

  <!-- Menu Section -->
  <?php include 'menu.php';?>

  <main class="container">
    <!-- Main content -->
    <div class="flex-grow-1">dashboard here...</div>
  </main>

  <footer class="footer">
    <div class="container">
      <span class="text-muted">Copyright © 2023 Budget Buddy</span>
    </div>
  </footer>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
    integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>