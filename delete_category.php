<?php
session_start();

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['UserId'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

// MongoDB connection setup...
require 'vendor/autoload.php'; // Include Composer's autoloader

$client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
$db = $client->selectDatabase('budgetbuddy');
$collection = $db->Categories;

// Get the categoryId from the POST data
$categoryId = isset($_POST['categoryId']) ? (int) $_POST['categoryId'] : null;

// Perform the delete operation
$result = $collection->updateOne(
    [],
    ['$pull' => ['Categories' => ['CategoryId' => $categoryId]]]
);

if ($result->isAcknowledged() && $result->getModifiedCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete the category.']);
}
?>
