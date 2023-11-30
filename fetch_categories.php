<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Set the header so that the client knows to expect JSON

require 'vendor/autoload.php'; // Include Composer's autoloader

try {
    // Connect to MongoDB
    // Important: Move the MongoDB connection string to a secure place before deploying to production
    $client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
    $db = $client->selectDatabase('budgetbuddy');

    // Select the collection
    $collection = $db->Categories;

    // Fetch all documents from the collection
    $documents = $collection->find();

    // Convert the cursor to an array of documents
    $categories = iterator_to_array($documents);

    // Echo the JSON representation of the categories
    echo json_encode($categories);

} catch (Exception $e) {
    echo json_encode(['error' => "Connection failed: " . $e->getMessage()]);
    http_response_code(500); // Send a HTTP 500 response code if there's a connection error
}

?>
