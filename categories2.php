<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['UserId'])) {
    header("Location: login.html");
    exit;
}

$userId = $_SESSION['UserId'];

header('Content-Type: text/html; charset=UTF-8');

require 'vendor/autoload.php';


try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://evaldo:JaR1BTyn1U6m9RhA@cluster0.ouhw8da.mongodb.net");
    $db = $client->selectDatabase('budgetbuddy');
    $collection = $db->Categories;
    
    // Find categories for the logged user
    $query = ['Categories.UserId' => $userId];
    $result = $collection->find($query);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budget Buddy</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="styles.css">


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

    .modal-open {
    padding-right: 0px!important;
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

    <section id="categoryList" class="mt-4">
    <header class="d-flex justify-content-between align-items-center title-page">
        <h1>Categories</h1>
        <button id="newCategoryBtn" class="btn btn-primary">+ New Category</button>
    </header>
    <table id="categoriesTable" class="table table-striped table-bordered mt-3" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Type</th>
                <th class="hid">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $entry): ?>
                <?php foreach ($entry['Categories'] as $category): ?>
                    <?php if ($category['UserId'] == $userId): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['Name']); ?></td>
                            <td><?php echo htmlspecialchars($category['Description']); ?></td>
                            <td><?php echo htmlspecialchars($category['Type']); ?></td>
                            <td class="edit-icon-cell">
                            <span class="edit-icon" onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">✎</span>
                                <span class="delete-icon" onclick="deleteCategory(<?php echo htmlspecialchars($category['CategoryId']); ?>)">🗑️</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editCategoryForm">
          <input type="hidden" id="editCategoryId" name="categoryId">
          <div class="form-group">
            <label for="editCategoryName">Name</label>
            <input type="text" class="form-control" id="editCategoryName" name="name" required>
          </div>
          <div class="form-group">
            <label for="editCategoryDescription">Description</label>
            <textarea class="form-control" id="editCategoryDescription" name="description" required></textarea>
          </div>
          <div class="form-group">
            <label for="editCategoryType">Type</label>
            <select class="form-control" id="editCategoryType" name="type">
              <option value="Expense">Expense</option>
              <option value="Income">Income</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="editCategoryForm">Save Changes</button>
      </div>
    </div>
  </div>
</div>
    </table>
</section>

 
<div id="modalOverlay" style="display: none;"></div>

<!-- New Category Modal -->
<div id="newCategoryModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalButton" onclick="closeModal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="newCategoryForm">
          <input type="hidden" id="categoryId" name="categoryId">
          <div class="form-group">
            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="categoryName" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="categoryDesc">Description:</label>
            <input type="text" id="categoryDesc" name="categoryDesc" class="form-control" required>        
          </div>
          <fieldset class="form-group">
            <legend>Type:</legend>
            <div class="form-check">
              <input type="radio" id="income" name="categoryType" class="form-check-input" value="Income" checked>
              <label for="income" class="form-check-label">Income</label>
            </div>
            <div class="form-check">
              <input type="radio" id="expense" name="categoryType" class="form-check-input" value="Expense">
              <label for="expense" class="form-check-label">Expense</label>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" form="newCategoryForm">Save Category</button>
      </div>
    </div>
  </div>
</div>

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
   $(document).ready(function() {
     $('#categoriesTable').DataTable(
      {
      responsive: true,
        rowCallback: function(row, data, index){
            if(data[2] === 'Expense') { // Assuming 'Type' is in the second column
                $(row).css('color', '#dc3545');
            } else if(data[2] === 'Income') {
                $(row).css('color', '#0d6efd');
            }
        }
      }
     );
   });
   function closeModal() {
  $('#editCategoryModal').modal('hide'); // Hide the modal
  $('#modalOverlay').hide(); // Hide the overlay
}


   function editCategory(category) {
  // Populate the form fields
  $('#editCategoryId').val(category.CategoryId);
  $('#editCategoryName').val(category.Name);
  $('#editCategoryDescription').val(category.Description);
  $('#editCategoryType').val(category.Type);

  // Show the modal
  $('#editCategoryModal').modal('show');
}

 </script>
 
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
     integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
     crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <script src="general.js"></script>
   <script src="categories2.js"></script>
 </body>
 
 </html>