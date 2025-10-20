<?php
session_start();
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='$role'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($role == "admin") header("Location: dashboard_admin.php");
        elseif ($role == "doctor") header("Location: dashboard_doctor.php");
        else header("Location: dashboard_elderly.php");
        exit();
    } else {
        $error = "Invalid login credentials";
    }
}
?>
<?php $pageTitle = 'Login | Elder Care';$extraCss=['css/log.css']; include 'includes/header.php'; ?>
    <div class="container">
        <div class="card fade-in">
            <h2 class="text-center mt-0">Welcome Back</h2>
            <p class="text-center" style="margin-top:-.5rem;color:var(--color-text-light);">Sign in to manage elder care records</p>
            <?php if(isset($error)): ?>
                <div class="toast error" style="position:static; box-shadow:none; background:var(--color-danger); color:#fff; border:0; margin-bottom:1rem;">⚠️ <?= $error ?></div>
            <?php endif; ?>
            <form name="loginForm" method="POST" onsubmit="return validateLoginForm()" novalidate>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required />
                </div>
                <div class="form-group">
                    <label for="logPass">Password</label>
                    <input type="password" name="password" id="logPass" placeholder="••••••••" required />
                    <div class="flex gap-sm items-center mt-1">
                        <label style="display:flex;align-items:center;gap:.35rem;font-weight:500;font-size:.7rem;cursor:pointer;">
                            <input type="checkbox" onclick="togglePassword('logPass')" /> Show Password
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                        <option value="elderly">Elderly</option>
                    </select>
                </div>
                <button type="submit" class="btn w-full" style="width:100%;">Login</button>
            </form>
            <p class="text-center" style="font-size:.8rem;margin-top:1rem;">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    <?php include 'includes/footer.php'; ?>
