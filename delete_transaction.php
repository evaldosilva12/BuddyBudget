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
$collection = $db->Transactions;

// Get the TransactionId from the POST data
$transactionId = isset($_POST['transactionId']) ? (int) $_POST['transactionId'] : null;

// Perform the delete operation
$result = $collection->updateOne(
    [],
    ['$pull' => ['Transactions' => ['TransactionId' => $transactionId]]]
);

if ($result->isAcknowledged() && $result->getModifiedCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete the transaction.']);
}
?>
