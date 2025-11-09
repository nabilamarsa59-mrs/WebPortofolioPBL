<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - PoliKarya</title>
  <link rel="stylesheet" href="styleLogin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <form id="loginForm">
      <div class="form-group">
        <label for="nim">NIM:</label>
        <input type="text" id="nim" name="nim" placeholder="Masukkan NIM">
        <div class="error-message" id="nimError">NIM tidak boleh kosong</div>
      </div>

      <div class="form-group">
        <label for="password">Kata sandi:</label>
        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi">
        <div class="error-message" id="passwordError">Kata sandi harus minimal 6 karakter</div>
      </div>

      <button type="submit" class="submit-btn">Login</button>

      <p class="register-link">Belum punya akun? <a href="RegisterPage.php">Daftar</a></p>

      <div class="button-group">
        <button type="button" class="btn back" onclick="window.location.href='RegisterPage.php'">Kembali</button>
      </div>
    </form>
  </div>
  <a href="HomePage.php"></a>
  <script src="scriptLogin.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
