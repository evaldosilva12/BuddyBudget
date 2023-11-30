document.addEventListener('DOMContentLoaded', function() {
  fetchCategories();

  var modal = document.getElementById('newCategoryModal');
  var btn = document.getElementById('newCategoryBtn');
  var span = document.getElementsByClassName('close')[0];

  btn.onclick = function() {
    modal.style.display = 'block';
  }

  span.onclick = function() {
    modal.style.display = 'none';
  }

  window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  }

  // document.getElementById('newCategoryForm').onsubmit = function(event) {
  //   event.preventDefault();
  //   addNewCategory();
  // };
});

function fetchCategories() {
  fetch('fetch_categories.php')
    .then(response => response.json())
    .then(data => {
      const categoryList = document.getElementById('categoryList');
      categoryList.innerHTML = ''; // Clear existing list items
      
      // Access the Categories array within the first object of the returned data
      data[0].Categories.forEach(category => {
        const li = document.createElement('li');
        // Use the correct keys for the category name and description
        li.textContent = category.Name + ' - ' + category.Description + ' | ' + category.Type;
        // Assign a class for CSS styling if necessary
        li.className = 'category-item'; 
        li.setAttribute('data-id', category.CategoryId);
        categoryList.appendChild(li);
      });
    })
    .catch(error => console.error('Error:', error));
}


// function addNewCategory() {
//   const name = document.getElementById('categoryName').value;
//   const type = document.querySelector('input[name="categoryType"]:checked').value;

//   fetch('add_category.php', {
//     method: 'POST',
//     body: new URLSearchParams(`name=${name}&type=${type}`)
//   })
//   .then(response => {
//     if (response.ok) {
//       return response.json();
//     } else {
//       throw new Error('Network response was not ok.');
//     }
//   })
//   .then(data => {
//     fetchCategories();
//     modal.style.display = 'none';
//   })
//   .catch(error => {
//     console.error('Error:', error);
//   });
// }

document.getElementById('newCategoryForm').addEventListener('submit', function(event) {
  event.preventDefault();
  const formData = new FormData(this);

  // Get the last category ID and increment it by 1
  const lastLi = document.querySelector('#categoryList li:last-child');
  const nextCategoryId = lastLi ? parseInt(lastLi.getAttribute('data-id'), 10) + 1 : 1;

  // Set the next category ID in the hidden input
  document.getElementById('categoryId').value = nextCategoryId;
  formData.append('categoryId', nextCategoryId);
  console.log("ID:",nextCategoryId)
    
  fetch('add_category.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if(data.success) {
      console.log('Category added with ID:', data.id);
      fetchCategories(); // Call this function to refresh the categories list
      document.getElementById('newCategoryModal').style.display = 'none'; // Close the modal
      this.reset(); // Reset the form fields
    } else {
      console.error('Error adding category:', data.error);
    }
  })
  .catch(error => {
    console.error('Error:', error);
  });
});
