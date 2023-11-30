<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Set the header so that the client knows to expect JSON

require 'vendor/autoload.php'; // Include Composer's autoloader

// Check if the user is logged in
if (!isset($_SESSION['UserId'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

// MongoDB connection setup...
$client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
$db = $client->selectDatabase('budgetbuddy');

// Select the collection
$collection = $db->Categories;

// Extract the user ID from the session
$userId = $_SESSION['UserId'];

if (isset($_POST['categoryId'])) {
    $categoryId = (int)$_POST['categoryId'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $type = $_POST['type'];

    // Find the document that contains the category
    $categoryDocument = $collection->findOne([
        'Categories.CategoryId' => $categoryId,
        'Categories.UserId' => $userId  // Make sure to update only the categories of the logged-in user
    ]);

    // If the document is found and the category belongs to the user
    if ($categoryDocument) {
        // Update the category within the Categories array of the found document
        $updateResult = $collection->updateOne(
            [
                '_id' => $categoryDocument['_id'],
                'Categories' => [
                    '$elemMatch' => [
                        'CategoryId' => $categoryId,
                        'UserId' => $userId
                    ]
                ]
            ],
            [
                '$set' => [
                    'Categories.$.Name' => $name,
                    'Categories.$.Description' => $description,
                    'Categories.$.Type' => $type
                ]
            ]
        );

        // Check if the update was successful
        if ($updateResult->getModifiedCount() == 0) {
            echo json_encode(['success' => false, 'error' => 'Failed to update the category.']);
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Category not found or does not belong to the user.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Category ID not provided.']);
}
?>
