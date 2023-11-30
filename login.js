document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const formData = new FormData(this);
    fetch('login.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        // Redirect to the user's dashboard or main page on successful login
        window.location.href = '/budgetbuddy/dash.php';
      } else {
        // Show an error message if login is unsuccessful
        document.getElementById('loginError').style.display = 'block';
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });
  