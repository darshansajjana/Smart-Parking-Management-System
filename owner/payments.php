<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['Owner_ID'];
$message = "";

// 💰 Handle payment
if (isset($_POST['pay_ticket_id'])) {
    $ticket_id = $_POST['pay_ticket_id'];
    $method = $_POST['method'];

    // Get Fee from Parking_Ticket
    $stmt = $conn->prepare("
        SELECT Fee FROM Parking_Ticket 
        WHERE Ticket_ID = ? AND ExitTime IS NOT NULL
    ");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->bind_result($amount);
    $stmt->fetch();
    $stmt->close();

    if ($amount) {
        // Insert payment record
        $stmt = $conn->prepare("
            INSERT INTO Payment (Method, Status, Amount, Ticket_ID, Payment_Date)
            VALUES (?, 'Completed', ?, ?, NOW())
        ");
        $stmt->bind_param("sdi", $method, $amount, $ticket_id);
        $stmt->execute();
        $stmt->close();

        $message = "✅ Payment of ₹$amount successful for Ticket #$ticket_id!";
    } else {
        $message = "⚠️ Invalid ticket or not ready for payment.";
    }
}

// 🚗 Fetch unpaid tickets belonging to this owner
$pending = $conn->query("
    SELECT 
        pt.Ticket_ID, 
        v.Plate_no, 
        ps.Space_ID,
        pt.EntryTime, 
        pt.ExitTime, 
        pt.Duration, 
        pt.Fee
    FROM Parking_Ticket pt
    JOIN Issued_For i ON pt.Ticket_ID = i.Ticket_ID
    JOIN Vehicle v ON i.Vehicle_ID = v.Vehicle_ID
    JOIN Parking_Space ps ON i.Space_ID = ps.Space_ID
    WHERE v.Owner_ID = $owner_id
      AND pt.ExitTime IS NOT NULL 
      AND pt.Ticket_ID NOT IN (SELECT Ticket_ID FROM Payment)
    ORDER BY pt.ExitTime DESC
");
?>

<?php include '../header.php'; ?>

<div class="card p-4 shadow">
  <h3>Pending Payments</h3>
  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if ($pending->num_rows > 0): ?>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Ticket ID</th>
          <th>Plate No</th>
          <th>Space</th>
          <th>Entry</th>
          <th>Exit</th>
          <th>Duration (min)</th>
          <th>Fee (₹)</th>
          <th>Payment Method</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $pending->fetch_assoc()): ?>
        <tr>
          <td><?= $row['Ticket_ID'] ?></td>
          <td><?= htmlspecialchars($row['Plate_no']) ?></td>
          <td><?= htmlspecialchars($row['Space_ID']) ?></td>
          <td><?= htmlspecialchars($row['EntryTime']) ?></td>
          <td><?= htmlspecialchars($row['ExitTime']) ?></td>
          <td><?= htmlspecialchars($row['Duration']) ?></td>
          <td><?= htmlspecialchars($row['Fee']) ?></td>
          <td>
            <form method="POST" class="d-flex">
              <input type="hidden" name="pay_ticket_id" value="<?= $row['Ticket_ID'] ?>">
              <select name="method" class="form-select form-select-sm me-2" required>
                <option value="">Select</option>
                <option value="Cash">Cash</option>
                <option value="UPI">UPI</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
              </select>
              <button type="submit" class="btn btn-success btn-sm">Pay</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-success">🎉 All your payments are cleared!</div>
  <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
