<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['UserId'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

header('Content-Type: application/json');

require 'vendor/autoload.php';

// MongoDB connection setup...
    $client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
    $db = $client->selectDatabase('budgetbuddy');

// Select the collection
$collection = $db->Transactions;

// Extract the user ID from the session
$userId = $_SESSION['UserId'];

// Collect data from the form
$transactionType = $_POST['type'];
$amount = (float)$_POST['amount'];
$currency = "CAD";
$date = $_POST['date'];
$description = $_POST['description'];
$category = $_POST['category'];
// ... other fields ...

$lastTransactionIdAggregation = $collection->aggregate([
    ['$unwind' => '$Transactions'],
    ['$sort' => ['Transactions.TransactionId' => -1]],
    ['$limit' => 1],
    ['$project' => ['Transactions.TransactionId' => 1]]
]);

$lastTransactionId = 0;
foreach ($lastTransactionIdAggregation as $doc) {
    $lastTransactionId = $doc['Transactions']['TransactionId'];
    break;
}
$newTransactionId = $lastTransactionId + 1;

// Prepare the new transaction document
$newTransaction = [
    'TransactionId' => $newTransactionId,
    'UserId' => $userId,
    'CategoryId' => $category, // You need to get the actual CategoryId from the form if necessary
    'Type' => $transactionType,
    'Amount' => $amount,
    'Currency' => $currency,
    'Date' => $date,
    'Description' => $description,
];

// Add the new transaction to the Transactions array of the document
$updateResult = $collection->updateOne(
    ['_id' => new MongoDB\BSON\ObjectId('654b04a137b14ff54a06909b')], // Use your actual document ID
    ['$push' => ['Transactions' => $newTransaction]]
);

if ($updateResult->getModifiedCount() == 0) {
    echo json_encode(['success' => false, 'error' => 'Failed to add the transaction.']);
} else {
    echo json_encode(['success' => true]);
}

?>