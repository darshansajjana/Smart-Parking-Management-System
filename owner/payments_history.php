<?php
// owner/payment_history.php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = (int) $_SESSION['Owner_ID'];

// Total spent aggregation
$total_sql = "
    SELECT IFNULL(SUM(p.Amount), 0) AS total_spent
    FROM Payment p
    JOIN Parking_Ticket pt ON p.Ticket_ID = pt.Ticket_ID
    JOIN Issued_For i ON pt.Ticket_ID = i.Ticket_ID
    JOIN Vehicle v ON i.Vehicle_ID = v.Vehicle_ID
    WHERE v.Owner_ID = ?
";
$stmt_total = $conn->prepare($total_sql);
$stmt_total->bind_param("i", $owner_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result()->fetch_assoc();
$total_spent = $total_result['total_spent'] ?? 0.00;
$stmt_total->close();

// Main payment history query using Issued_For as bridge
$sql = "
    SELECT 
        p.Payment_ID,
        p.Ticket_ID,
        v.Plate_no,
        v.Type AS Vehicle_Type,
        pt.EntryTime,
        pt.ExitTime,
        TIMESTAMPDIFF(MINUTE, pt.EntryTime, pt.ExitTime) AS Duration_Minutes,
        p.Amount AS Paid_Amount,
        p.Payment_Date,
        i.Space_ID,
        pl.Name AS Lot_Name,
        pl.Location AS Lot_Location
    FROM Payment p
    JOIN Parking_Ticket pt ON p.Ticket_ID = pt.Ticket_ID
    JOIN Issued_For i ON pt.Ticket_ID = i.Ticket_ID
    JOIN Vehicle v ON i.Vehicle_ID = v.Vehicle_ID
    LEFT JOIN Parking_Space ps ON i.Space_ID = ps.Space_ID
    LEFT JOIN Parking_Lot pl ON ps.Lot_ID = pl.Lot_ID
    WHERE v.Owner_ID = ?
    ORDER BY p.Payment_Date DESC, p.Payment_ID DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include '../header.php'; ?>

<div class="container mt-4">
  <div class="card shadow-sm p-4">
    <h3 class="mb-3">Payment History</h3>

    <div class="mb-3">
      <div class="alert alert-success mb-0">
        <strong>Total Amount Spent:</strong> ₹<?= number_format($total_spent, 2) ?>
      </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
      <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Payment ID</th>
            <th>Ticket ID</th>
            <th>Plate No</th>
            <th>Type</th>
            <th>Lot</th>
            <th>Space ID</th>
            <th>Entry Time</th>
            <th>Exit Time</th>
            <th>Duration</th>
            <th>Amount (₹)</th>
            <th>Payment Date</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): 
              $duration_min = (int)$row['Duration_Minutes'];
              $hours = floor($duration_min / 60);
              $mins = $duration_min % 60;
              $duration_text = ($hours ? $hours . ' hr ' : '') . $mins . ' min';
          ?>
            <tr>
              <td><?= htmlspecialchars($row['Payment_ID']) ?></td>
              <td><?= htmlspecialchars($row['Ticket_ID']) ?></td>
              <td><?= strtoupper(htmlspecialchars($row['Plate_no'])) ?></td>
              <td><?= htmlspecialchars($row['Vehicle_Type']) ?></td>
              <td><?= htmlspecialchars($row['Lot_Name'] ?? '—') ?></td>
              <td><?= htmlspecialchars($row['Space_ID'] ?? '—') ?></td>
              <td><?= htmlspecialchars($row['EntryTime']) ?></td>
              <td><?= htmlspecialchars($row['ExitTime']) ?></td>
              <td><?= $duration_text ?></td>
              <td>₹<?= number_format($row['Paid_Amount'], 2) ?></td>
              <td><?= htmlspecialchars($row['Payment_Date']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info">No payment records found for your vehicles.</div>
    <?php endif; ?>
  </div>
</div>

<?php include '../footer.php'; ?>

<?php
// cleanup
$stmt->close();
$conn->close();
?>
