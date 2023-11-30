// Function to open the edit modal with transaction data
function openEditModal(transactionData) {


    // Set the form fields with the transaction data
    document.getElementById('editTransactionId').value = transactionData.TransactionId;
    document.getElementById('editDate').value = transactionData.Date;
    document.getElementById('editType').value = transactionData.Type;
    document.getElementById('editDescription').value = transactionData.Description;
    document.getElementById('editAmount').value = transactionData.Amount;
    document.getElementById('editCurrency').value = transactionData.Currency;
    // Set the category; you will need to handle this according to your categories structure
    // ... other fields as necessary
  
  // Ensure the transaction type is correctly set in lowercase for comparison
  const transactionType = transactionData.Type.toLowerCase();

  // Populate the category select element based on the type of transaction
  // Pass the current category ID to select it
  populateCategories(transactionData.Type, 'editCategory', transactionData.CategoryId);
  

  // Show the modal
  document.getElementById('editTransactionModal').style.display = 'block';
  $('#editTransactionModal').modal('show');
}
  
  
  // Function to close the edit modal
  function closeEditModal() {
    document.getElementById('editTransactionModal').style.display = 'none';
  }
  
  document.getElementById('editTransactionForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('update_transaction.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Close the modal and refresh the transaction list
        closeEditModal();
        // // Assume fetchTransactions is a function that refreshes the transaction list
        // fetchTransactions(); 
        // Refresh the entire page
        window.location.reload();        
      } else {
        // Handle errors, perhaps display a message to the user
        console.error('Error saving transaction:', data.error);
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });
  

function populateCategories(transactionType, selectElementId, selectedCategoryId) {
  fetch('fetch_categories_select.php')
    .then(response => response.json())
    .then(allCategories => {
        const categorySelect = document.getElementById(selectElementId);
        categorySelect.innerHTML = ''; // Clear existing options

        // Filter categories based on the transactionType
        const filteredCategories = allCategories.filter(category => 
            category.Type.toLowerCase() === transactionType.toLowerCase()
        );

        console.log('Filtered categories:', filteredCategories); // Debugging log

        filteredCategories.forEach(category => {
            const option = new Option(category.Name, category.CategoryId);
            // Ensure the category ID is a string for comparison
            option.selected = String(category.CategoryId) === String(selectedCategoryId);
            categorySelect.add(option);
        });

        // Debugging log
        console.log('Selected Category ID:', selectedCategoryId);
    })
    .catch(error => {
        console.error('Error fetching categories:', error);
    });
}





function deleteTransaction(transactionId) {
  if (confirm('Are you sure you want to delete this transaction?')) {
    fetch('delete_transaction.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'transactionId=' + transactionId
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        // Transaction deleted successfully
        window.location.reload();
      } else {
        alert('Error deleting transaction: ' + data.error);
      }
    })
    .catch(error => {
      alert('Error deleting transaction: ' + error.message);
    });
  }
}
