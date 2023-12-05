<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require 'vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
    $db = $client->selectDatabase('budgetbuddy');
    $collection = $db->Users;

    // Collect login credentials from the form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Find the user in the database
    $user = $collection->findOne([
        'Users.Username' => $username,
        'Users.Password' => $password // Ideally, you should use password_verify() here
    ]);

    // Check if user credentials are valid
    if ($user) {
        // Assuming the user document contains a Users array with user details
        foreach ($user['Users'] as $userDetails) {
            if ($userDetails['Username'] == $username && $userDetails['Password'] == $password) {
                // Store UserId in $_SESSION
                $_SESSION['UserId'] = $userDetails['UserId'];
                break;
            }
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    http_response_code(500);
}

?>
