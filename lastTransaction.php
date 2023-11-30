<?php
session_start();

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['UserId'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

// MongoDB connection setup...
require 'vendor/autoload.php'; // Include Composer's autoloader

// Use your actual credentials and database information
$client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
$db = $client->selectDatabase('budgetbuddy');

// Select the collection
$collection = $db->Transactions;

// Aggregate to unwind the transactions and get the last TransactionId
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

echo $lastTransactionId;
echo " ";
echo $newTransactionId;
?>
