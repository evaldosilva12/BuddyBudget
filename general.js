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

// Modify openAddModal and openEditModal to use the new populateCategories signature
function openAddModal(transactionType) {
    populateCategories(transactionType, 'addCategory'); // Use the correct ID for the add modal
    document.getElementById('addTransactionType').value = transactionType;
    document.getElementById('addTransactionTitle').textContent = 'Add ' + transactionType;
    document.getElementById('addTransactionModal').style.display = 'block';
  }
  
  // Function to close the add transaction modal
  function closeAddModal() {
    document.getElementById('addTransactionModal').style.display = 'none';
  }
  
  // Event listeners for the add income/expense buttons
  document.getElementById('addIncomeBtn').addEventListener('click', function() {
    $('#addTransactionModal').modal('show');
    openAddModal('Income');
  });
  
  document.getElementById('addExpenseBtn').addEventListener('click', function() {
    $('#addTransactionModal').modal('show');
    openAddModal('Expense');
  });
  
  // Handle the form submission
  document.getElementById('addTransactionForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
  
    fetch('add_transaction.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok ' + response.statusText);
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        // Close the modal and refresh the transaction list
        closeAddModal();
        // Refresh the page or just the list of transactions
        //window.location.reload();
        window.location.href = 'transactions.php'; 
      } else {
        // Display the error message from the server
        alert('Error adding transaction: ' + data.error); // or display it in a dedicated error message element
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error adding transaction: ' + error.message); // Display error to the user
    });
  });