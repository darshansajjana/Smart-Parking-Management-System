<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = intval($_POST['ticket_id']);

    // Call stored procedure to close the ticket
    $stmt = $conn->prepare("CALL CloseParkingTicket(?)");
    $stmt->bind_param("i", $ticket_id);

    if ($stmt->execute()) {
        $message = "✅ Ticket #$ticket_id successfully closed and space released!";
    } else {
        $message = "❌ Failed to close Ticket #$ticket_id.";
    }

    $stmt->close();
}

$sql = "
SELECT 
    pt.Ticket_ID,
    v.Plate_no,
    v.Type,
    ps.Space_ID,
    pt.EntryTime
FROM Parking_Ticket pt
JOIN Issued_For i ON pt.Ticket_ID = i.Ticket_ID
JOIN Vehicle v ON i.Vehicle_ID = v.Vehicle_ID
JOIN Parking_Space ps ON i.Space_ID = ps.Space_ID
WHERE pt.ExitTime IS NULL
ORDER BY pt.EntryTime ASC
";

$tickets = $conn->query($sql);
?>

<?php include '../header.php'; ?>

<div class="container mt-4">
  <div class="card shadow p-4">
    <h3 class="text-center mb-4">Release Parking Ticket</h3>

    <?php if ($message): ?>
      <div class="alert alert-info text-center fw-bold">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-striped table-bordered text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Ticket ID</th>
            <th>Plate No</th>
            <th>Type</th>
            <th>Space ID</th>
            <th>Entry Time</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($tickets && $tickets->num_rows > 0): ?>
            <?php while ($t = $tickets->fetch_assoc()) { ?>
              <tr>
                <td><?= htmlspecialchars($t['Ticket_ID']) ?></td>
                <td><?= htmlspecialchars($t['Plate_no']) ?></td>
                <td><?= htmlspecialchars($t['Type']) ?></td>
                <td><?= htmlspecialchars($t['Space_ID']) ?></td>
                <td><?= htmlspecialchars($t['EntryTime']) ?></td>
                <td>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="ticket_id" value="<?= $t['Ticket_ID'] ?>">
                    <button 
                      type="submit" 
                      name="release"
                      class="btn btn-danger btn-sm"
                      onclick="return confirm('Are you sure you want to release this ticket?')">
                      Release
                    </button>
                  </form>
                </td>
              </tr>
            <?php } ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-muted">No active tickets found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../footer.php'; ?>
