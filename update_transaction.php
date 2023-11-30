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
$collection = $db->Transactions;

// Extract the user ID from the session
$userId = $_SESSION['UserId'];

if (isset($_POST['transactionId'])) {
    $transactionId = (int)$_POST['transactionId'];
    $amount = (float)$_POST['amount'];
    $description = $_POST['description'];
    $category = (int)$_POST['category'];
    $date = $_POST['date'];
    $currency = $_POST['currency'];
    // ... other fields ...

    // Find the document that contains the transaction
    $transactionDocument = $collection->findOne([
        'Transactions.TransactionId' => $transactionId,
        'Transactions.UserId' => $userId  // Make sure to update only the transactions of the logged-in user
    ]);

    // If the document is found and the transaction belongs to the user
    if ($transactionDocument) {
        // Update the transaction within the Transactions array of the found document
        $updateResult = $collection->updateOne(
            [
                '_id' => $transactionDocument['_id'],
                'Transactions' => [
                    '$elemMatch' => [
                        'TransactionId' => $transactionId,
                        'UserId' => $userId
                    ]
                ]
            ],
            [
                '$set' => [
                    'Transactions.$.Amount' => $amount,
                    'Transactions.$.Description' => $description,
                    'Transactions.$.CategoryId' => $category,
                    'Transactions.$.Date' => $date,
                    'Transactions.$.Currency' => $currency
                    // ... other fields ...
                ]
            ]
        );

        // Check if the update was successful
        if ($updateResult->getModifiedCount() == 0) {
            echo json_encode(['success' => false, 'error' => 'Failed to update the transaction.']);
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Transaction not found or does not belong to the user.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Transaction ID not provided.']);
}
?>
