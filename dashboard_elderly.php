<?php
session_start();
include("includes/db.php");
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'elderly') { header("Location: login.php"); exit(); }
$user_id = (int)$_SESSION['user_id'];
?>
<?php $pageTitle = 'Your Health Records | Elder Care'; $extraCss=['css/elder.css']; include 'includes/header.php'; ?>
<div class="container">
    <div class="flex justify-between items-center gap-md" style="flex-wrap:wrap;">
        <h2 class="mt-0 mb-0" style="text-align:left;">Your Health Records</h2>
        <div class="table-search" style="margin:0;">
            <input type="text" placeholder="Search records..." data-table-filter data-target="elder-records" />
        </div>
    </div>
    <div class="table-wrapper fade-in" style="margin-top:1.5rem;">
        <table id="elder-records">
            <thead>
                <tr><th>ID</th><th>Type</th><th>Value</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM health WHERE user_id='$user_id' ORDER BY id DESC");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['health_type']) . "</td>
                    <td>" . htmlspecialchars($row['value']) . "</td>
                    <td>{$row['recorded_at']}</td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
<?php include 'includes/footer.php'; ?>
