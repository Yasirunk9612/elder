<?php
include("includes/db.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = $_POST['role'];
    $ok = false; $err = null;
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if (mysqli_query($conn, $sql)) { $ok = true; } else { $err = mysqli_error($conn); }
}
?>
<?php $pageTitle = 'Register | Elder Care';$extraCss=['css/register.css']; include 'includes/header.php'; ?>
    <div class="container">
        <div class="card fade-in">
            <h2 class="text-center mt-0">Create Account</h2>
            <p class="text-center" style="margin-top:-.5rem;color:var(--color-text-light);">Register to start managing elder health data</p>
            <?php if(isset($ok) && $ok): ?>
                <div class="toast success" style="position:static; box-shadow:none; background:var(--color-success); color:#fff; border:0; margin-bottom:1rem;">✅ Registered successfully. <a style="color:#fff;text-decoration:underline;" href="login.php">Login</a></div>
            <?php elseif(isset($err)): ?>
                <div class="toast error" style="position:static; box-shadow:none; background:var(--color-danger); color:#fff; border:0; margin-bottom:1rem;">⚠️ Error: <?= htmlspecialchars($err) ?></div>
            <?php endif; ?>
            <form name="registerForm" method="POST" onsubmit="return validateRegisterForm()" novalidate>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" placeholder="Jane Doe" required />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="you@example.com" required />
                </div>
                <div class="form-group">
                    <label for="regPass">Password</label>
                    <input type="password" name="password" id="regPass" placeholder="Create a password" required />
                    <div class="password-meter mt-1"></div>
                    <label style="display:flex;align-items:center;gap:.35rem;font-weight:500;font-size:.7rem;cursor:pointer;margin-top:.5rem;">
                        <input type="checkbox" onclick="togglePassword('regPass')" /> Show Password
                    </label>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                        <option value="elderly">Elderly</option>
                    </select>
                </div>
                <button type="submit" class="btn" style="width:100%;">Register</button>
            </form>
            <p class="text-center" style="font-size:.8rem;margin-top:1rem;">Already have an account? <a href="login.php">Login</a></p>
        </div>
    <?php include 'includes/footer.php'; ?>



    <script>
document.addEventListener("DOMContentLoaded", () => {
  const passInput = document.getElementById("regPass");
  const meter = document.querySelector(".password-meter");
  if (passInput && meter) {
    passInput.addEventListener("input", () => {
      const val = passInput.value;
      let strength = "weak";
      if (val.length > 8 && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[!@#$%^&*]/.test(val))
        strength = "strong";
      else if (val.length >= 6 && /[0-9]/.test(val))
        strength = "medium";
      meter.className = "password-meter " + strength;
    });
  }
});
</script>
