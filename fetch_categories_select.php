<?php
session_start();

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['UserId'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['UserId'];

require 'vendor/autoload.php'; // Include Composer's autoloader

// Use your actual credentials and database information
$client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
$db = $client->selectDatabase('budgetbuddy');

// Select the Categories collection
$collection = $db->Categories;

// Fetch all categories
// Query to fetch categories for the logged-in user
$query = ['Categories.UserId' => $userId];
$result = $collection->find($query);

$userCategories = [];

foreach ($result as $doc) {
    foreach ($doc['Categories'] as $category) {
        if ($category['UserId'] == $userId) {
            array_push($userCategories, $category);
        }
    }
}

echo json_encode($userCategories);
