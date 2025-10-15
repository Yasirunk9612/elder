<?php
session_start();
include 'includes/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit(); }

$messages = [];
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM users WHERE id=$id")) {
        $messages[] = "User #$id deleted.";
        header('Location: dashboard_admin.php');
        exit();
    } else {
        $messages[] = 'Delete failed: ' . htmlspecialchars(mysqli_error($conn));
    }
}

// Basic counts
$countUsers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0] ?? 0;
$countDoctors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='doctor'"))[0] ?? 0;
$countElders = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='elderly'"))[0] ?? 0;

$pageTitle = 'Admin Dashboard';
$extraCss = ['css/admin.css']; 
include 'includes/header.php';
?>

<h2 class="page-title">Admin Dashboard</h2>

<?php if($messages): ?>
    <div class="notice-box">
        <?php foreach($messages as $m): ?>
            <div class="notice-item"><?= $m ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="stats-row">
    <div class="stat">
        <div class="stat-num"><?= $countUsers ?></div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $countDoctors ?></div>
        <div class="stat-label">Doctors</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $countElders ?></div>
        <div class="stat-label">Elderly</div>
    </div>
    <div class="stat add">
        <a class="add-user-link" href="register.php">+ Add User</a>
    </div>
</div>

<div class="table-box">
    <div class="table-head">
        <strong>Users</strong>
        <input type="text" placeholder="Search..." id="userSearch" class="search-input" />
    </div>
    <table id="adminUsers" class="simple-table">
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($result)):
            $id = (int)$row['id'];
        ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $id ?>" class="action edit">Edit</a>
                    <a href="dashboard_admin.php?delete=<?= $id ?>" class="action delete" onclick="return confirm('Delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('userSearch').addEventListener('input', function(){
  const val = this.value.toLowerCase();
  document.querySelectorAll('#adminUsers tbody tr').forEach(tr => {
    const text = tr.textContent.toLowerCase();
    tr.style.display = text.includes(val) ? '' : 'none';
  });
});
</script>

<?php include 'includes/footer.php'; ?>
