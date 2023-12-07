<?php
// reports.php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];

require 'vendor/autoload.php';

// Get the number of days from the URL parameter, default to 15 if not set
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

// Calculate the date for 'n' days ago in 'Y-m-d' format
$dateNDaysAgo = new DateTime();
$dateNDaysAgo->modify("-$days days");
$dateNDaysAgoFormatted = $dateNDaysAgo->format('Y-m-d');

// Connect to MongoDB and fetch data
$client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
$db = $client->selectDatabase('budgetbuddy');

// Fetch transactions and categories
$transactionsCollection = $db->Transactions;
$categoriesCollection = $db->Categories;

// Calculate the date for 15 days ago in 'Y-m-d' format
$date15DaysAgo = new DateTime();
$date15DaysAgo->modify("-$days days");
$date15DaysAgoFormatted = $date15DaysAgo->format('Y-m-d');
// echo $date15DaysAgoFormatted;

// Define the aggregation pipeline
$pipeline = [
    ['$match' => ['Transactions.UserId' => $userId]], 
    ['$project' => [
        'FilteredTransactions' => [
            '$filter' => [
                'input' => '$Transactions', 
                'as' => 'transaction', 
                'cond' => [
                    '$and' => [
                        ['$eq' => ['$$transaction.UserId', $userId]], 
                        ['$gte' => ['$$transaction.Date', $date15DaysAgoFormatted]]
                    ]
                ]
            ]
        ]
    ]]
];

// Execute the aggregation
$result = $db->Transactions->aggregate($pipeline);

// Initialize variables
$categoryExpenses = [];
$totalIncome = 0;
$totalExpenses = 0;

// Process the result of the aggregation
foreach ($result as $doc) {
    foreach ($doc['FilteredTransactions'] as $transaction) {
        $amount = $transaction['Amount'];
        if ($transaction['Type'] == 'Income') {
            $totalIncome += $amount;
        } else if ($transaction['Type'] == 'Expense') {
            $totalExpenses += $amount;
            $categoryExpenses[$transaction['CategoryId']] = ($categoryExpenses[$transaction['CategoryId']] ?? 0) + $amount;
        }
    }
}

// Fetch categories for the user
$categoryResult = $db->Categories->find(['Categories.UserId' => $userId]);

// Map category names to expenses
foreach ($categoryResult as $category) {
    foreach ($category['Categories'] as $cat) {
        if ($cat['UserId'] == $userId) {
            $categoryId = $cat['CategoryId'];
            if (isset($categoryExpenses[$categoryId])) {
                $categoryExpenses[$cat['Name']] = $categoryExpenses[$categoryId];
                unset($categoryExpenses[$categoryId]);
            }
        }
    }
}

// Encode data for JavaScript
$categoryExpensesJson = json_encode($categoryExpenses);
$incomeExpenseDataJson = json_encode(['Income' => $totalIncome, 'Expenses' => $totalExpenses]);

// Initialize array for budgets
$categoryBudgets = [];
$totalBudget = 0;

// Fetch categories and their budgets for the user
$categoryResult = $db->Categories->find(['Categories.UserId' => $userId]);

// foreach ($categoryResult as $category) {
//     foreach ($category['Categories'] as $cat) {
//         if ($cat['UserId'] == $userId) {
//             // Check if 'Budget' field is set, if not, use 0 as default
//             $budget = $cat['Budget'] ?? 0;
//             $categoryBudgets[$cat['Name']] = $budget;
//             $totalBudget += $budget;
//         }
//     }
// }

foreach ($categoryResult as $category) {
    foreach ($category['Categories'] as $cat) {
        if ($cat['UserId'] == $userId && isset($cat['Budget'])) {
            $categoryBudgets[$cat['Name']] = $cat['Budget'];
            $totalBudget += $cat['Budget'];
        }
    }
}


// Initialize array for budgets
$totalBudget = 0;

// Fetch categories and their budgets for the user
$categoryResult = $db->Categories->find(['Categories.UserId' => $userId]);

foreach ($categoryResult as $category) {
    foreach ($category['Categories'] as $cat) {
        if ($cat['UserId'] == $userId) {
            // Check if 'Budget' field is set, if not, use 0 as default
            $totalBudget += $cat['Budget'] ?? 0;
        }
    }
}

$totalDataJson = json_encode(['TotalSpent' => $totalExpenses, 'TotalBudget' => $totalBudget]);


// Fetch all transactions for the user - Total Balance
$pipeline = [
    ['$match' => ['Transactions.UserId' => $userId]],
    ['$unwind' => '$Transactions'],
    ['$match' => ['Transactions.UserId' => $userId]],
    ['$group' => [
        '_id' => '$userId',
        'TotalIncome' => [
            '$sum' => [
                '$cond' => [['$eq' => ['$Transactions.Type', 'Income']], '$Transactions.Amount', 0]
            ]
        ],
        'TotalExpenses' => [
            '$sum' => [
                '$cond' => [['$eq' => ['$Transactions.Type', 'Expense']], '$Transactions.Amount', 0]
            ]
        ]
    ]]
];

// Execute the aggregation
$result = $db->Transactions->aggregate($pipeline);

// Initialize variables
$totalIncome = 0;
$totalExpenses = 0;

// Process the result of the aggregation
foreach ($result as $doc) {
    $totalIncome = $doc['TotalIncome'];
    $totalExpenses = $doc['TotalExpenses'];
}

$totalBalance = $totalIncome - $totalExpenses;



// Fetch the last 5 transactions for the user, irrespective of the date range
$pipeline = [
    ['$match' => ['Transactions.UserId' => $userId]],
    ['$unwind' => '$Transactions'],
    ['$match' => ['Transactions.UserId' => $userId]],
    ['$sort' => ['Transactions.Date' => -1]],
    ['$limit' => 5],
    ['$group' => [
        '_id' => '$userId',
        'RecentTransactions' => ['$push' => '$Transactions']
    ]]
];

// Execute the aggregation
$result = $db->Transactions->aggregate($pipeline);

// Extract recent transactions
$recentTransactions = [];
foreach ($result as $doc) {
    $recentTransactions = $doc['RecentTransactions'];
}


$remainingBudgets = [];
foreach ($categoryBudgets as $categoryName => $budget) {
    $spent = $categoryExpenses[$categoryName] ?? 0;
    $remaining = $budget - $spent;
    $remainingBudgets[$categoryName] = $remaining;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Buddy</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" />


    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .ml-auto a {
            color: white;
        }

        .ml-auto a:hover {
            color: white;
        }

        .ml-auto,
        .mx-auto {
            display: flex;
            gap: 20px;
        }

        .bg-light {
            background-color: #10c469 !important;
        }

        .menu-item .fa-solid,
        .fas {
            font-size: 16px;
        }

        .nav-link {
            padding: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .menu-item {
                padding: 10px;
                flex-basis: 50%;
                /* Each item takes half the width on smaller screens */
            }
        }



        /* MODAL */
        #newCategoryModal .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        #modalOverlay {
            position: fixed;
            /* Stay in place */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.5);
            /* Black with opacity */
            z-index: 1;
            /* Sit on top */
        }

        .title-page {
            margin-bottom: 40px;
        }

        .edit-icon-cell,
        .delete-icon-cell,
        .hid {
            cursor: pointer;
        }

        tr:hover .edit-icon-cell,
        tr:hover .delete-icon-cell,
        .hid:hover {
            visibility: visible;
        }

        .modal-backdrop {
            position: relative;
        }

        .dataTables_wrapper .dataTables_length {
            margin-right: 30px;
        }

        .btn-income,
        .btn-expense,
        .btn-show-all {
            display: none !important;
        }

        .btn-show-all {
            margin-right: 90px !important;
        }

        /* Tablet and smaller screens (max-width: 768px) */
        @media screen and (max-width: 768px) {
            .btn-show-all {
                margin-right: 20px !important;
            }
        }
        main {
            background: rgb(0 0 0 / 0%);
            -webkit-box-shadow: none;
            box-shadow: none;
            margin-top: 20px;
        }
        .card {
            padding: 30px;
            background-color: #fff;
            margin-bottom: 1.5rem;
            -webkit-box-shadow: 0 1px 2px #ccc;
            box-shadow: 0 1px 2px #ccc;
        }
        .card-body {
            padding: 0;
        }
        .table {
            font-size: small;
        }        
    </style>
</head>

<body>

    <header>
        <?php include 'navbar.php';?>
    </header>

    <!-- Menu Section -->
    <?php include 'menu.php';?>

    <main class="container">
        <section id="reportsPage" class="mt-4">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <h1>Dashboard</h1>
                <!-- Date Filter -->
                <select id="dateFilter" onchange="updateChartsWithDateRange()" class="form-select">
                    <option value="" disabled selected hidden>Select Time Period</option>
                    <option value="15">Last 15 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="60">Last 60 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </header>

            <!-- Overview Cards -->
            <div class="row">
                <!-- Full Height Column -->
                <div class="col d-flex flex-column col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header"><h4>Total Balance</h4></div>
                        <div class="card-body">
                            <p class="card-text" style="font-size: xxx-large;"><small>$</small>
                                <?php echo $totalBalance; ?>
                            </p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4>Recent Transactions</h4></div>
                        <div class="card-body">
                        <div class="table-responsive">
                             <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentTransactions as $transaction): ?>
                                    <tr style="color: <?php echo $transaction['Type'] == 'Expense' ? 'red' : 'blue'; ?>;">
                                        <td>
                                            <?php echo $transaction['Date']; ?>
                                        </td>
                                        <td>
                                            <?php echo $transaction['Amount']; ?>
                                        </td>
                                        <td>
                                            <?php echo $transaction['Type']; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <a href="transactions.php" class="btn btn-primary btn-sm">See More</a>
                        </div>
                        </div>                        
                    </div>
                    <div class="card">
                        <div class="card-header"><h4>Budget Summary</h4></div>
                        <div class="card-body">
                        <div class="table-responsive">
                             <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Budget</th>
                                        <th>Spent</th>
                                        <th>Remaining Budget</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($categoryBudgets as $categoryName => $budget): ?>
                                    <?php 
                                        $spent = $categoryExpenses[$categoryName] ?? 0;
                                        $remaining = $budget - $spent;
                                        $color = $remaining >= 0 ? 'blue' : 'red';
                                    ?>
                                    <tr style="color: <?php echo $color; ?>;">
                                        <td>
                                            <?php echo $categoryName; ?>
                                        </td>
                                        <td>
                                            <?php echo $budget; ?>
                                        </td>
                                        <td>
                                            <?php echo $categoryExpenses[$categoryName] ?? 0; ?>
                                        </td>
                                        <td>
                                            <?php echo $remainingBudgets[$categoryName]; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Column with Two Rows -->
                <div class="col">
                    <!-- Row 1 -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card">
                                <h2>Category Expenses</h2>
                                <canvas id="categoryExpensesChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <h2>Income Vs Expenses</h2>
                                <canvas id="incomeExpensesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Row 2 -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <h2>Total Budget Vs Total Spent</h2>
                                <canvas id="totalBudgetChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <div id="modalOverlay" style="display: none;"></div>        
        <?php include 'add_transaction_modal.php';?>
    </main>
    <footer class="footer">
        <div class="container">
            <span class="text-muted">Copyright © 2023 Budget Buddy</span>
        </div>
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
 
 <!-- DataTables JS -->
 <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>    
    <script>
        // Decode PHP data in JavaScript
        var categoryExpensesData = <?php echo $categoryExpensesJson; ?>;
        var incomeExpenseData = <?php echo $incomeExpenseDataJson; ?>;
        // var categoryBudgetsData = ?php echo json_encode($categoryBudgets); ?>;
        var totalData = <?php echo $totalDataJson; ?>;


        // Generate random color
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Predefined set of colors
        var predefinedColors = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(50, 205, 50, 0.2)',
            'rgba(255, 69, 0, 0.2)',
            'rgba(0, 128, 128, 0.2)',
            'rgba(128, 0, 128, 0.2)',
        ];

        var categoryExpensesData = <?php echo $categoryExpensesJson; ?>;
        var categoryColors = [];
        var categoryBorderColors = [];

        Object.keys(categoryExpensesData).forEach(function (key, index) {
            var color = predefinedColors[index % predefinedColors.length] || getRandomColor();
            categoryColors.push(color);

            var borderColor = color.replace('0.2', '1'); // Changing alpha for border
            categoryBorderColors.push(borderColor);
        });

        var ctx1 = document.getElementById('categoryExpensesChart').getContext('2d');
        var categoryExpensesChart = new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: Object.keys(categoryExpensesData),
                datasets: [{
                    label: 'Expenses by Category',
                    data: Object.values(categoryExpensesData),
                    backgroundColor: categoryColors,
                    borderColor: categoryBorderColors,
                    borderWidth: 1
                }]
            }
        });

        var ctx2 = document.getElementById('incomeExpensesChart').getContext('2d');
        var incomeExpensesChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Income', 'Expenses'],
                datasets: [{
                    label: 'Income Vs Expenses',
                    data: [incomeExpenseData['Income'], incomeExpenseData['Expenses']],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });

        // var ctx3 = document.getElementById('budgetExpensesChart').getContext('2d');
        // var budgetExpensesChart = new Chart(ctx3, {
        //     type: 'bar',
        //     data: {
        //         labels: Object.keys(categoryBudgetsData),
        //         datasets: [
        //             {
        //                 label: 'Budget',
        //                 data: Object.values(categoryBudgetsData),
        //                 backgroundColor: 'rgba(54, 162, 235, 0.5)',
        //                 borderColor: 'rgba(54, 162, 235, 1)',
        //                 borderWidth: 1
        //             },
        //             {
        //                 label: 'Expenses',
        //                 data: Object.values(categoryExpensesData),
        //                 backgroundColor: 'rgba(255, 99, 132, 0.5)',
        //                 borderColor: 'rgba(255, 99, 132, 1)',
        //                 borderWidth: 1
        //             }
        //         ]
        //     },
        //     options: {
        //         scales: {
        //             yAxes: [{
        //                 ticks: { beginAtZero: true }
        //             }]
        //         }
        //     }
        // });
var ctx3 = document.getElementById('totalBudgetChart').getContext('2d');
var totalBudgetChart = new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: ['Total Budget', 'Total Spent'],
        datasets: [{
            data: [totalData.TotalBudget, totalData.TotalSpent],
            backgroundColor: [
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 99, 132, 0.5)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false // Ensure the legend is not displayed
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.chart.data.labels[context.dataIndex];
                        let value = context.raw;
                        return label + ': ' + value;
                    }
                }
            }
        },
        // ... other options ...
    }
});
          
    </script>
<script>
    function updateChartsWithDateRange() {
        var selectedDays = document.getElementById('dateFilter').value;
        window.location.href = 'index.php?days=' + selectedDays;
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
     integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
     crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <script src="general.js"></script>
 </body>
</html>
