<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Buddy</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
    <link rel="stylesheet" type="text/css" href="styles.css">

    <style>
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
            background-color: #10c469 !important;
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

        .title-page {
            margin-bottom: 40px;
        }

        .edit-icon-cell,
        .delete-icon-cell,
        .hid {
            cursor: pointer;
        }

        tr:hover .edit-icon-cell,
        tr:hover .delete-icon-cell,
        .hid:hover {
            visibility: visible;
        }

        .modal-backdrop {
            position: relative;
        }

        .dataTables_wrapper .dataTables_length {
            margin-right: 30px;
        }

        .btn-income,
        .btn-expense,
        .btn-show-all {
            display: none !important;
        }

        .btn-show-all {
            margin-right: 90px !important;
        }

        /* Tablet and smaller screens (max-width: 768px) */
        @media screen and (max-width: 768px) {
            .btn-show-all {
                margin-right: 20px !important;
            }
        }
/* Color Scheme Enhancement */
:root {
    --primary-color: #17a2b8; /* A refreshing blue shade */
    --text-color: #333;
}

body {
    font-family: 'Raleway', sans-serif;
    color: var(--text-color);
}


/* Form Styling */
input[type="text"], input[type="password"] {
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 10px;
    width: 100%;
    margin-bottom: 15px;
}

button[type="submit"] {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: darken(var(--primary-color), 10%);
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
    }
}

/* General Layout and Spacing */
.container {
    padding: 20px;
}

/* Interactive Elements */
a:hover, button:hover {
    opacity: 0.8;
}            

/* Additional CSS for Centering */
.center-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; /* This makes sure that the wrapper takes at least the full viewport height */
}

.container {
    flex: 1;
    max-width: 400px; /* Adjust the max-width as per your design */
}

    </style>
</head>

<body>

    <header>
        <?php include 'navbar-login.php';?>
    </header>
    <div class="center-wrapper">
        <main class="container">
        <section id="loginSection">
        <form id="loginForm">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <div id="loginError" style="color: red; display: none;">Invalid username or password.</div>
        </section>
        </main>
        </div>
    <footer class="footer">
        <div class="container">
            <span class="text-muted">Copyright © 2023 Budget Buddy</span>
        </div>
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

  <script src="login.js"></script>
</body>
</html>
