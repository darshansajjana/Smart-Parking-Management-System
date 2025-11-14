<?php
session_start();
include '../db_connection.php';
if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Owner') {
    header("Location: ../login.php");
    exit();
}

include '../header.php';

// Fetch only available parking spaces
$query = "
SELECT 
    s.Space_ID,
    s.Type,
    s.Status,
    p.Name AS LotName,
    p.Location
FROM Parking_Space s
JOIN Parking_Lot p ON s.Lot_ID = p.Lot_ID
WHERE s.Status = 'Available'
ORDER BY p.Name, s.Space_ID
";
$result = $conn->query($query);
?>

<div class="card shadow-sm p-4 border-0">
  <h3 class="mb-3 text-center">Available Parking Spaces</h3>
  <p class="text-muted text-center mb-4">Showing only spaces currently marked as <strong>Available</strong>.</p>

  <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-success">
          <tr>
            <th>Space ID</th>
            <th>Lot Name</th>
            <th>Location</th>
            <th>Type</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['Space_ID']) ?></td>
            <td><?= htmlspecialchars($row['LotName']) ?></td>
            <td><?= htmlspecialchars($row['Location']) ?></td>
            <td><?= htmlspecialchars($row['Type']) ?></td>
            <td><span class="badge bg-success"><?= htmlspecialchars($row['Status']) ?></span></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">
      No available spaces found at the moment.
    </div>
  <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
