<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require 'vendor/autoload.php';

try {
    // Connect to MongoDB
    // Move the MongoDB connection string to a secure place before deploying to production
    $client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
    $db = $client->selectDatabase('budgetbuddy');

    // Select the collection
    $collection = $db->Categories;

    // Collect data from the form
    $categoryName = $_POST['categoryName'] ?? '';
    $categoryType = $_POST['categoryType'] ?? '';
    $categoryDesc = $_POST['categoryDesc'] ?? '';
    $categoryId = isset($_POST['categoryId']) ? (int)$_POST['categoryId'] : null;

    // Perform input validation as needed
    if (empty($categoryName) || empty($categoryDesc) || empty($categoryType) || is_null($categoryId)) {
        throw new Exception('Category name and type are required.');
    }

    // Prepare the new category document
    $newCategory = [
        'CategoryId' => $categoryId,
        'Name' => $categoryName,
        'Description' => $categoryDesc,
        'Type' => $categoryType,
    ];

    // The ObjectId of the document you want to update
    $documentId = '654b044d37b14ff54a069099';

    // Update the specific document by adding the new category
    $result = $collection->updateOne(
        ['_id' => new \MongoDB\BSON\ObjectId($documentId)],
        ['$push' => ['Categories' => $newCategory]]
    );

    // Check if the update was successful
    if ($result->getModifiedCount() == 0) {
        throw new Exception('Failed to add the category.');
    }

    // Return success message
    echo json_encode(['success' => true, 'id' => $documentId]);

} catch (Exception $e) {
    // Return an error message if something goes wrong
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    http_response_code(500); // Send a HTTP 500 response code if there's an error
}

?>
