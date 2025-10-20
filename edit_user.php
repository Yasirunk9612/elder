<?php
include("includes/db.php");
include("includes/auth.php");
if ($_SESSION['role'] != 'admin') { header("Location: login.php"); exit(); }

$id = (int)$_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$id"));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $role  = $_POST['role'];
    mysqli_query($conn, "UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$id");
    header("Location: dashboard_admin.php"); exit();
}
?>

<?php 
$pageTitle = 'Edit User | Elder Care'; 
$extraCss = ['css/edit.css']; 
include 'includes/header.php'; 
?>

<div class="edit-user-container">
    <div class="edit-user-card fade-in">
        <h2>Edit User</h2>
        <form method="POST" class="edit-user-form" novalidate>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required />
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                    <option value="doctor" <?= $user['role']=='doctor'?'selected':'' ?>>Doctor</option>
                    <option value="elderly" <?= $user['role']=='elderly'?'selected':'' ?>>Elderly</option>
                </select>
            </div>
            <button type="submit" class="btn">Update User</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
