document.getElementById('loginForm').addEventListener('submit', function(event) {
  event.preventDefault();

  let nim = document.getElementById('nim').value.trim();
  let password = document.getElementById('password').value.trim();

  let valid = true;

  if (nim === '') {
    document.getElementById('nimError').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('nimError').style.display = 'none';
  }

  if (password.length < 6) {
    document.getElementById('passwordError').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('passwordError').style.display = 'none';
  }

  if (!valid) return;

  let users = JSON.parse(localStorage.getItem('users')) || [];
  let user = users.find(u => u.nim === nim && u.password === password);

  if (user) {
    alert('Login berhasil! Selamat datang di WorkPiece.');
    localStorage.setItem('loggedInUser', nim);
    window.location.href = 'HomePage.php';
  } else {
    alert('NIM atau kata sandi salah!');
  }
});
