document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById('newCategoryModal');
  var modalOverlay = document.getElementById('modalOverlay');
  var btn = document.getElementById('newCategoryBtn');
  var span = document.getElementsByClassName('close')[0];

  btn.onclick = function() {
    $('#newCategoryModal').modal('show');
    modal.style.display = 'block';
    modalOverlay.style.display = 'block';
  }

  span.onclick = function() {
    modal.style.display = 'none';
    modalOverlay.style.display = 'none';
    $('#modalOverlay').modal('hide');

  }

  window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
      modalOverlay.style.display = 'none';
      $('#modalOverlay').modal('hide');

    }
  }
});

document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('update_category.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Close the modal and refresh the transaction list
        alert('Category updated successfully');
        $('#editCategoryModal').modal('hide');
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


  function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category?')) {
      fetch('delete_category.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'categoryId=' + categoryId
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Category deleted successfully
          window.location.reload();
        } else {
          alert('Error deleting category: ' + data.error);
        }
      })
      .catch(error => {
        alert('Error deleting category: ' + error.message);
      });
    }
  }

  

  document.getElementById('newCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
  
    // Get the text content of the categoriesTable_info element
    const infoText = document.getElementById('categoriesTable_info').textContent;
    
    // Extract the total number of categories using a regular expression
    const match = /of (\d+) entries/.exec(infoText);
    const totalCategories = match ? parseInt(match[1], 10) : 0;

    // Calculate the next category ID by adding 2 to the total number of categories
    const nextCategoryId = totalCategories + 2;

    // Set the next category ID in the hidden input
    document.getElementById('categoryId').value = nextCategoryId;
    formData.append('categoryId', nextCategoryId);
    console.log("Next Category ID:", nextCategoryId);
      
    fetch('add_category2.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        console.log('Category added successfully. Details:', data);
        console.log('Category added with ID:', data.id);
        document.getElementById('newCategoryModal').style.display = 'none'; // Close the modal
        document.getElementById('modalOverlay').style.display = 'none'; // Close the overlay
        this.reset(); // Reset the form fields
        window.location.reload();
      } else {
        console.error('Error adding category:', data.error);
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });

// Add an event listener to the modal
$('#editCategoryModal').on('hidden.bs.modal', function () {
  document.getElementById('modalOverlay').style.display = 'none';
});
