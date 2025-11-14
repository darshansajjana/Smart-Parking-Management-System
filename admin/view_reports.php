<?php
session_start();
include '../db_connection.php';
if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Basic stats
$total_lots = $conn->query("SELECT COUNT(*) AS cnt FROM Parking_Lot")->fetch_assoc()['cnt'];
$total_spaces = $conn->query("SELECT COUNT(*) AS cnt FROM Parking_Space")->fetch_assoc()['cnt'];
$occupied = $conn->query("SELECT COUNT(*) AS cnt FROM Parking_Space WHERE Status='Occupied'")->fetch_assoc()['cnt'];
$occupied_pct = $total_spaces ? round(($occupied / $total_spaces) * 100, 2) : 0;

// Revenue (sum payments)
$revenue = $conn->query("SELECT IFNULL(SUM(Amount),0) AS total FROM Payment")->fetch_assoc()['total'];

// Recent tickets (joined with Issued_For, Vehicle, and Parking_Space)
$tickets = $conn->query("
    SELECT 
        t.Ticket_ID,
        t.EntryTime,
        t.ExitTime,
        t.Fee,
        v.Plate_No,
        ps.Space_ID,
        pl.Name AS LotName
    FROM Issued_For i
    JOIN Parking_Ticket t ON i.Ticket_ID = t.Ticket_ID
    JOIN Vehicle v ON i.Vehicle_ID = v.Vehicle_ID
    JOIN Parking_Space ps ON i.Space_ID = ps.Space_ID
    JOIN Parking_Lot pl ON ps.Lot_ID = pl.Lot_ID
    ORDER BY t.EntryTime DESC
    LIMIT 10
");
?>
<?php include '../header.php'; ?>

<div class="card p-3">
  <h3>Reports</h3>

  <div class="row">
    <div class="col-md-3"><div class="card p-3 mb-3"><h5>Total Lots</h5><p><?= $total_lots ?></p></div></div>
    <div class="col-md-3"><div class="card p-3 mb-3"><h5>Total Spaces</h5><p><?= $total_spaces ?></p></div></div>
    <div class="col-md-3"><div class="card p-3 mb-3"><h5>Occupied (%)</h5><p><?= $occupied_pct ?>%</p></div></div>
    <div class="col-md-3"><div class="card p-3 mb-3"><h5>Total Revenue</h5><p>&#8377; <?= number_format($revenue, 2) ?></p></div></div>
  </div>

  <h5 class="mt-3">Recent Tickets</h5>
  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Ticket ID</th>
        <th>Plate No</th>
        <th>Parking Space</th>
        <th>Lot Name</th>
        <th>Entry</th>
        <th>Exit</th>
        <th>Fee</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($t = $tickets->fetch_assoc()) { ?>
      <tr>
        <td><?= $t['Ticket_ID'] ?></td>
        <td><?= htmlspecialchars($t['Plate_No']) ?></td>
        <td><?= htmlspecialchars($t['Space_ID']) ?></td>
        <td><?= htmlspecialchars($t['LotName']) ?></td>
        <td><?= $t['EntryTime'] ?></td>
        <td><?= $t['ExitTime'] ?></td>
        <td><?= $t['Fee'] ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include '../footer.php'; ?>
