<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

// ✅ Fetch only vehicles that DO NOT have active tickets
$vehicles = $conn->query("
    SELECT v.Vehicle_ID, v.Plate_no
    FROM Vehicle v
    WHERE v.Vehicle_ID NOT IN (
        SELECT i.Vehicle_ID
        FROM Issued_For i
        JOIN Parking_Ticket pt ON i.Ticket_ID = pt.Ticket_ID
        WHERE pt.ExitTime IS NULL
    )
");

// ✅ Fetch only available spaces
$spaces = $conn->query("SELECT Space_ID FROM Parking_Space WHERE Status='Available'");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $space_id = $_POST['space_id'];

    // Call stored procedure
    $stmt = $conn->prepare("CALL create_ticket(?, ?)");
    $stmt->bind_param("ii", $vehicle_id, $space_id);
    $stmt->execute();

    // Get the returned ticket ID
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $ticket_id = $row['Ticket_ID'];

    $message = "✅ Parking ticket #$ticket_id successfully issued!";
    $stmt->close();
}
?>

<?php include '../header.php'; ?>
<div class="card p-4 shadow">
  <h3>Create Parking Ticket</h3>
  <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Select Vehicle</label>
      <select name="vehicle_id" class="form-select" required>
        <option value="">-- Choose Vehicle --</option>
        <?php while ($v = $vehicles->fetch_assoc()) { ?>
          <option value="<?= $v['Vehicle_ID'] ?>"><?= htmlspecialchars($v['Plate_no']) ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Select Parking Space</label>
      <select name="space_id" class="form-select" required>
        <option value="">-- Choose Space --</option>
        <?php while ($s = $spaces->fetch_assoc()) { ?>
          <option value="<?= $s['Space_ID'] ?>">Space <?= $s['Space_ID'] ?></option>
        <?php } ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Issue Ticket</button>
  </form>
</div>
<?php include '../footer.php'; ?>
