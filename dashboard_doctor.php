<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $user_id = (int)$_POST['user_id'];
    $health_type = mysqli_real_escape_string($conn, $_POST['health_type']);
    $value = mysqli_real_escape_string($conn, $_POST['value']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    $sql = "UPDATE `health` SET `user_id`='$user_id', `health_type`='$health_type', `value`='$value', `notes`='$notes' WHERE `id`=$id";
    if (!mysqli_query($conn, $sql)) {
        echo '<div style="padding:12px;margin:12px;border:1px solid #f00;color:#900;background:#fee;">SQL Error (UPDATE): '.htmlspecialchars(mysqli_error($conn)).'</div>';
        exit;
    }
    header("Location: dashboard_doctor.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (!mysqli_query($conn, "DELETE FROM `health` WHERE `id`=$id")) {
        echo '<div style="padding:12px;margin:12px;border:1px solid #f00;color:#900;background:#fee;">SQL Error (DELETE): '.htmlspecialchars(mysqli_error($conn)).'</div>';
        exit;
    }
    header("Location: dashboard_doctor.php");
    exit();
}

// Handle insert
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $user_id = (int)$_POST['user_id'];
    $health_type = mysqli_real_escape_string($conn, $_POST['health_type']);
    $value = mysqli_real_escape_string($conn, $_POST['value']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    $doctor_id = (int)$_SESSION['user_id'];

    $sql = "INSERT INTO `health` (`user_id`, `doctor_id`, `health_type`, `value`, `notes`) 
            VALUES ('$user_id','$doctor_id','$health_type','$value','$notes')";
    if (!mysqli_query($conn, $sql)) {
        echo '<div style="padding:12px;margin:12px;border:1px solid #f00;color:#900;background:#fee;">SQL Error (INSERT): '.htmlspecialchars(mysqli_error($conn)).'</div>';
        exit;
    }
    header("Location: dashboard_doctor.php");
    exit();
}
?>

<?php 
$pageTitle = 'Doctor Dashboard | Elder Care';
$extraCss = ['css/doctor.css']; 
include 'includes/header.php'; 
?>

<style>
.modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-backdrop.active { display: flex; }
.modal {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    width: 100%;
    max-width: 450px;
}
</style>

<div class="container">
    <div class="flex justify-between items-center gap-md" style="flex-wrap:wrap;">
        <h2 class="mt-0 mb-0">Health Records</h2>
        <div class="table-search" style="margin:0;">
            <input type="text" placeholder="Search records..." data-table-filter data-target="records-table" />
        </div>
    </div>

    <div class="grid grid-2 grid-gap" style="margin-top:1.5rem;">
        <!-- ADD FORM -->
        <div class="card fade-in">
            <h3 class="mt-0" style="text-align:left;">Add Health Record</h3>
            <form method="POST" novalidate>
                <div class="form-group">
                    <label for="user_id">Elderly User</label>
                    <select name="user_id" id="user_id" required>
                        <?php
                        $elderly = mysqli_query($conn, "SELECT * FROM users WHERE role='elderly'");
                        while ($row = mysqli_fetch_assoc($elderly)) {
                            echo "<option value='{$row['id']}'>".htmlspecialchars($row['name'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="health_type">Health Type</label>
                    <input type="text" name="health_type" id="health_type" placeholder="Blood Pressure" required />
                </div>
                <div class="form-group">
                    <label for="value">Value</label>
                    <input type="text" name="value" id="value" placeholder="120/80" required />
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Optional notes..." style="width:100%;"></textarea>
                </div>
                <button type="submit" class="btn btn-success w-full">Save Record</button>
            </form>
        </div>

        <!-- RECORD TABLE -->
        <div>
            <div class="table-wrapper fade-in">
                <table id="records-table">
                    <thead>
                        <tr><th>ID</th><th>Patient</th><th>Type</th><th>Value</th><th>Notes</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT h.*, u.name FROM health h JOIN users u ON h.user_id = u.id ORDER BY h.id DESC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id = (int)$row['id'];
                        echo "<tr>
                                <td>{$id}</td>
                                <td>".htmlspecialchars($row['name'])."</td>
                                <td>".htmlspecialchars($row['health_type'])."</td>
                                <td>".htmlspecialchars($row['value'])."</td>
                                <td>".htmlspecialchars($row['notes'])."</td>
                                <td>{$row['recorded_at']}</td>
                                <td>
                                    <button type='button' class='btn btn-outline btn-small' data-edit='1'
                                        data-id='{$id}'
                                        data-user_id='{$row['user_id']}'
                                        data-health_type='".htmlspecialchars($row['health_type'], ENT_QUOTES)."'
                                        data-value='".htmlspecialchars($row['value'], ENT_QUOTES)."'
                                        data-notes='".htmlspecialchars($row['notes'] ?? '', ENT_QUOTES)."'>Edit</button>
                                    <a href='dashboard_doctor.php?delete={$id}' class='btn btn-danger btn-small' data-delete='true'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="edit-backdrop" class="modal-backdrop">
    <div class="modal">
        <h3 class="mt-0" style="text-align:left;">Edit Health Record</h3>
        <form method="POST" id="edit-form" novalidate>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="id" id="edit_id" />
            <div class="form-group">
                <label for="edit_user_id">Elderly User</label>
                <select name="user_id" id="edit_user_id" required>
                    <?php
                    $elderly2 = mysqli_query($conn, "SELECT * FROM users WHERE role='elderly'");
                    while ($row = mysqli_fetch_assoc($elderly2)) {
                        echo "<option value='{$row['id']}'>".htmlspecialchars($row['name'])."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_health_type">Health Type</label>
                <input type="text" name="health_type" id="edit_health_type" required />
            </div>
            <div class="form-group">
                <label for="edit_value">Value</label>
                <input type="text" name="value" id="edit_value" required />
            </div>
            <div class="form-group">
                <label for="edit_notes">Notes</label>
                <textarea name="notes" id="edit_notes" rows="3"></textarea>
            </div>
            <div class="flex justify-between gap-sm">
                <button type="button" class="btn btn-outline" data-cancel>Cancel</button>
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// --- Edit Modal Logic ---
function initDoctorEditModal() {
    const editBackdrop = document.getElementById('edit-backdrop');
    const form = document.getElementById('edit-form');
    const cancelBtn = form.querySelector('[data-cancel]');
    const idEl = document.getElementById('edit_id');
    const userEl = document.getElementById('edit_user_id');
    const typeEl = document.getElementById('edit_health_type');
    const valEl = document.getElementById('edit_value');
    const notesEl = document.getElementById('edit_notes');

    function openWith(btn) {
        idEl.value = btn.getAttribute('data-id') || '';
        userEl.value = btn.getAttribute('data-user_id') || '';
        typeEl.value = btn.getAttribute('data-health_type') || '';
        valEl.value = btn.getAttribute('data-value') || '';
        notesEl.value = btn.getAttribute('data-notes') || '';
        editBackdrop.classList.add('active');
    }

    document.addEventListener('click', (e) => {
        const target = e.target.closest('button[data-edit="1"]');
        if (target) {
            e.preventDefault();
            openWith(target);
        }
    });

    cancelBtn.addEventListener('click', () => editBackdrop.classList.remove('active'));
    editBackdrop.addEventListener('click', (e) => {
        if (e.target === editBackdrop) editBackdrop.classList.remove('active');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initDoctorEditModal();
});
</script>

<?php include 'includes/footer.php'; ?>
