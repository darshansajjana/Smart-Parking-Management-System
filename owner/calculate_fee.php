<?php
session_start();
include '../db_connection.php';

// Restrict access to Owner only
if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Owner') {
    header("Location: ../login.php");
    exit();
}

$message = "";
$total_fee = null;

// Fetch available vehicle types dynamically
$vehicle_types = $conn->query("SELECT Vehicle_Type FROM Rate_Config ORDER BY Vehicle_Type");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_type = $_POST['vehicle_type'];
    $duration = intval($_POST['duration']); // in minutes

    // Call SQL function to calculate fee
    $query = $conn->query("SELECT calculate_fee_by_type('$vehicle_type', $duration) AS total_fee");
    if ($query && $row = $query->fetch_assoc()) {
        $total_fee = $row['total_fee'];
        $message = "Parking Fee Calculated Successfully!";
    } else {
        $message = "Error calculating fee. Please try again.";
    }
}
?>

<?php include '../header.php'; ?>

<div class="card shadow p-4 mt-4">
    <h3 class="mb-3">Calculate Parking Fee</h3>

    <?php if ($message): ?>
        <div class="alert <?= $total_fee ? 'alert-success' : 'alert-danger' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mb-3">
        <div class="mb-3">
            <label class="form-label">Select Vehicle Type</label>
            <select name="vehicle_type" class="form-select" required>
                <option value="">-- Choose Vehicle Type --</option>
                <?php while ($v = $vehicle_types->fetch_assoc()) { ?>
                    <option value="<?= htmlspecialchars($v['Vehicle_Type']) ?>">
                        <?= htmlspecialchars($v['Vehicle_Type']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Enter Duration (in minutes)</label>
            <input type="number" name="duration" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary">Calculate Fee</button>
    </form>

    <?php if ($total_fee !== null): ?>
        <div class="alert alert-info">
            <strong>Total Fee:</strong> ₹<?= number_format($total_fee, 2) ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
