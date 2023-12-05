<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];

header('Content-Type: text/html; charset=UTF-8');

require 'vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
    $db = $client->selectDatabase('budgetbuddy');

    // Fetch transactions for the logged-in user
    $transactionsCollection = $db->Transactions;
    $transactions = $transactionsCollection->findOne(['Transactions.UserId' => $userId]);

    if (is_null($transactions)) {
      throw new Exception("No transactions found for the user.");
  }

    // Fetch categories for the logged-in user
    $categoriesCollection = $db->Categories;
    $query = ['Categories.UserId' => $userId];
    $result = $categoriesCollection->find($query);

    $categoryMapping = [];
    foreach ($result as $doc) {
        foreach ($doc['Categories'] as $category) {
            if ($category['UserId'] == $userId) {
                $categoryMapping[$category['CategoryId']] = $category['Name'];
            }
        }
    }

    if (empty($categoryMapping)) {
        throw new Exception("No categories found for the user.");
    }

} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
}

/* Total */
$totalIncome = 0;
$totalExpenses = 0;

foreach ($transactions['Transactions'] as $transaction) {
    if ($transaction['UserId'] == $userId) {
        if ($transaction['Type'] == 'Income') {
            $totalIncome += $transaction['Amount'];
        } else if ($transaction['Type'] == 'Expense') {
            $totalExpenses += $transaction['Amount'];
        }
    }
}

$currentBalance = $totalIncome - $totalExpenses;

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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css"/>


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
      background-color: #10c469!important;
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
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
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
      position: fixed; /* Stay in place */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.5); /* Black with opacity */
      z-index: 1; /* Sit on top */
    }
    .title-page {
      margin-bottom:40px;
    }






    .edit-icon-cell, .delete-icon-cell, .hid {
      cursor: pointer;
    }

    tr:hover .edit-icon-cell, tr:hover .delete-icon-cell, .hid:hover {
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
      display:none!important;
    }

    .btn-show-all {
      margin-right: 90px!important;
    }
/* Tablet and smaller screens (max-width: 768px) */
@media screen and (max-width: 768px) {
    .btn-show-all {
        margin-right: 20px !important;
    }
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

<section id="transactionHistory" class="mt-4">
        <header class="d-flex justify-content-between align-items-center title-page">
            <h1>Transaction History</h1>
            <!-- <h1>Transaction History for <?php echo $userId; ?></h1> -->
            <!-- <button id="addIncomeBtn" class="btn btn-primary">Add Income</button>
            <button id="addExpenseBtn" class="btn btn-primary">Add Expense</button> -->
            <h5 style="text-align:right;">Current Balance:<br><span style="font-size: xx-large;">$ <?php echo htmlspecialchars($currentBalance); ?> <small>CAD</small></span></h5>
        </header>
        <section>
        <!-- <select id="monthFilter">
    <option value="">Select Month</option>
    <option value="01">January</option>
    <option value="02">February</option>
    <option value="03">March</option>
    <option value="04">April</option>
    <option value="05">May</option>
    <option value="06">June</option>
    <option value="07">July</option>
    <option value="08">August</option>
    <option value="09">September</option>
    <option value="10">October</option>
    <option value="11">November</option>
    <option value="12">December</option>
</select> -->

</section>        
      <?php if ($transactions): ?>
        <div id="custom-button-container"></div>
        <table id="transactionsTable" class="table table-striped table-bordered mt-3" style="width:100%">
          <thead>
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Category</th>
              <th>Description</th>
              <th>Amount</th>
              <th class="hid">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($transactions['Transactions'] as $transaction): ?>
              <?php if ($transaction['UserId'] == $userId): // Ensure transaction belongs to the user ?>
                <tr>
                  <td><?php echo htmlspecialchars($transaction['Date']); ?></td>
                  <td><?php echo htmlspecialchars($transaction['Type']); ?></td>
                  <td>
                      <?php
                          $categoryName = $categoryMapping[$transaction['CategoryId']] ?? 'Unknown Category';
                          echo htmlspecialchars($categoryName);
                      ?>
                  </td>
                  <td><?php echo htmlspecialchars($transaction['Description']); ?></td>
                  <td><?php echo htmlspecialchars($transaction['Amount']); ?></td>
                  <td class="edit-icon-cell">
                    <span class="edit-icon" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($transaction)); ?>)">
                    ✎
                    </span>
                    <span class="delete-icon" onclick="deleteTransaction(<?php echo htmlspecialchars($transaction['TransactionId']); ?>)">
                      🗑️
                    </span>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>

          <!-- Modal structure -->
<!-- Edit Transaction Modal -->
<div id="editTransactionModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Transaction</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeEditModal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editTransactionForm">
          <input type="hidden" id="editTransactionId" name="transactionId">
          <div class="form-group">
            <label for="editDate">Date:</label>
            <input type="date" id="editDate" name="date" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editType">Type:</label>
            <select id="editType" name="type" class="form-control" disabled>
              <option value="Expense">Expense</option>
              <option value="Income">Income</option>
            </select>
          </div>
          <div class="form-group">
            <label for="editCategory">Category:</label>
            <select id="editCategory" name="category" class="form-control" required>
              <!-- Options will be populated dynamically with JavaScript -->
            </select>
          </div>
          <div class="form-group">
            <label for="editDescription">Description:</label>
            <input type="text" id="editDescription" name="description" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editAmount">Amount:</label>
            <input type="number" id="editAmount" name="amount" class="form-control" required>
          </div>
          <div class="form-group">
            <!-- <label for="editCurrency">Currency:</label> -->
            <input type="hidden" id="editCurrency" name="currency" class="form-control" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeEditModal()">Close</button>
        <button type="submit" class="btn btn-primary" form="editTransactionForm">Save Changes</button>
      </div>
    </div>
  </div>
</div>

          <?php include 'add_transaction_modal.php';?>       

        </table>
      <?php else: ?>
        <p>No transactions found.</p>
      <?php endif; ?>
    </section>

    <div id="modalOverlay" style="display: none;"></div>

    </main>


  <footer class="footer">
    <div class="container">
      <span class="text-muted">Copyright © 2023 Budget Buddy</span>
    </div>
  </footer>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
  var table = $('#transactionsTable').DataTable({
      responsive: true,
      order: [[0, 'desc']],
      columnDefs: [
          {
                "targets": 0, // Target the first column for date formatting
                "render": function(data, type, row) {
                    if(type === 'display' || type === 'filter'){
                        var date = new Date(data);
                        var day = date.getDate();
                        var month = date.toLocaleString('default', { month: 'short' });
                        var year = date.getFullYear();
                        return day + '/' + month + '/' + year;
                    }
                    return data;
                }
            },
            { targets: 0, responsivePriority: 1 }, // First column has the highest priority
            { targets: 1, responsivePriority: 1 },
            { targets: 2, responsivePriority: 5 },
            { targets: 3, responsivePriority: 5 },
            { targets: 4, responsivePriority: 2 },
            { targets: -1, responsivePriority: 10000 } // Last column has the lowest priority
        ],
        rowCallback: function(row, data, index){
            if(data[1] === 'Expense') { // Assuming 'Type' is in the second column
                $(row).css('color', '#dc3545');
            } else if(data[1] === 'Income') {
                $(row).css('color', '#0d6efd');
            }
        },           
      dom: '<"top"lfB>rtip',
        buttons: [
            {
                text: 'Expense',
                className: 'btn-expense',
                action: function ( e, dt, node, config ) {
                    dt.columns(1).search('Expense').draw();
                    updateButtonStyles(node, 'btn-expense');
                }
            },
            {
                text: 'Income',
                className: 'btn-income',
                action: function ( e, dt, node, config ) {
                    dt.columns(1).search('Income').draw();
                    updateButtonStyles(node, 'btn-income');
                }
            },
            {
                text: 'Show All',
                className: 'btn-show-all',
                action: function ( e, dt, node, config ) {
                    dt.columns(1).search('').draw();
                    updateButtonStyles(node, 'btn-show-all');
                }
            },          
            {
                extend: 'copy',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Adjust the column indexes as needed
                }
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Adjust the column indexes as needed
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Adjust the column indexes as needed
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Adjust the column indexes as needed
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Adjust the column indexes as needed
                }
            }
        ]
    });

    // $('#monthFilter').on('change', function(){
    //     var selectedMonth = this.value;
    //     if(selectedMonth) {
    //         table.column(0).search(selectedMonth, true, false).draw();
    //     } else {
    //         table.column(0).search('').draw();
    //     }
    // });

    // Add data-label attributes
    table.buttons('.btn-expense').nodes().attr('data-label', 'Expense');
    table.buttons('.btn-income').nodes().attr('data-label', 'Income');
    table.buttons('.btn-show-all').nodes().attr('data-label', 'Show All');

    // // Move the custom buttons to the div
    // $(".btn-expense, .btn-income, .btn-show-all").each(function() {
    //     $(this).detach().appendTo('#custom-button-container');
    // });    

    function updateButtonStyles(selectedNode, selectedClass) {
        table.buttons('.btn-expense, .btn-income, .btn-show-all').nodes().each(function() {
            $(this).removeClass('selected');
        });
        table.buttons('.' + selectedClass).nodes().each(function() {
            if (this === selectedNode) {
                $(this).addClass('selected');
            }
        });
    }  
});
 
</script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
    integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="general.js"></script>
  <script src="transactions.js"></script>
</body>

</html>