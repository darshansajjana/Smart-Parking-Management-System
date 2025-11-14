<?php
include '../header.php';
include '../db_connection.php';

// Restrict access to owners only
if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['Owner_ID'];

// ========== BOOK SPACE ==========
if (isset($_POST['book'])) {
    $space_id = intval($_POST['space_id']);
    $start = $_POST['start'];
    $end = $_POST['end'];

    // Create Reservation
    $stmt = $conn->prepare("INSERT INTO Reservation (StartTime, EndTime, Status) VALUES (?, ?, 'Confirmed')");
    $stmt->bind_param('ss', $start, $end);
    $stmt->execute();
    $res_id = $conn->insert_id;

    // Create Booking
    $stmt2 = $conn->prepare("INSERT INTO Booking (Owner_ID, Space_ID, Reservation_ID) VALUES (?, ?, ?)");
    $stmt2->bind_param('iii', $owner_id, $space_id, $res_id);
    $stmt2->execute();

    // Update space to reserved
    $stmt3 = $conn->prepare("UPDATE Parking_Space SET Status='Reserved' WHERE Space_ID=?");
    $stmt3->bind_param('i', $space_id);
    $stmt3->execute();

    header("Location: reservations.php");
    exit();
}

// ========== CANCEL RESERVATION ==========
if (isset($_GET['cancel'])) {
    $rid = intval($_GET['cancel']);

    $get_space = $conn->query("SELECT s.Space_ID 
                               FROM Booking b 
                               JOIN Parking_Space s ON b.Space_ID = s.Space_ID 
                               WHERE b.Reservation_ID = $rid AND b.Owner_ID = $owner_id");

    if ($get_space && $get_space->num_rows > 0) {
        $sid = $get_space->fetch_assoc()['Space_ID'];
        $conn->query("UPDATE Parking_Space SET Status='Available' WHERE Space_ID=$sid");
    }

    $conn->query("DELETE FROM Booking WHERE Reservation_ID=$rid AND Owner_ID=$owner_id");
    $conn->query("DELETE FROM Reservation WHERE Reservation_ID=$rid");

    header("Location: reservations.php");
    exit();
}

// ========== FETCH DATA ==========
$spaces = $conn->query("SELECT s.Space_ID, s.Type, s.Status, p.Name AS LotName
                        FROM Parking_Space s
                        JOIN Parking_Lot p ON s.Lot_ID = p.Lot_ID
                        WHERE s.Status = 'Available'
                        ORDER BY p.Name");

$my = $conn->query("SELECT r.Reservation_ID, r.StartTime, r.EndTime, r.Status,
                           s.Space_ID, s.Type, p.Name AS LotName
                    FROM Booking b
                    JOIN Reservation r ON b.Reservation_ID = r.Reservation_ID
                    JOIN Parking_Space s ON b.Space_ID = s.Space_ID
                    JOIN Parking_Lot p ON s.Lot_ID = p.Lot_ID
                    WHERE b.Owner_ID = $owner_id
                    ORDER BY r.StartTime DESC");
?>

<div class="container mt-4">
  <h2 class="mb-3">Book a Parking Space</h2>
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <?php if ($spaces->num_rows > 0): ?>
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Lot Name</th>
              <th>Type</th>
              <th>Status</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($s = $spaces->fetch_assoc()): ?>
              <tr>
                <form method="POST" action="reservations.php">
                  <input type="hidden" name="space_id" value="<?= $s['Space_ID'] ?>">
                  <td><?= htmlspecialchars($s['LotName']) ?></td>
                  <td><?= htmlspecialchars($s['Type']) ?></td>
                  <td><span class="badge bg-success"><?= htmlspecialchars($s['Status']) ?></span></td>
                  <td><input type="datetime-local" name="start" class="form-control" required></td>
                  <td><input type="datetime-local" name="end" class="form-control" required></td>
                  <td><button type="submit" name="book" class="btn btn-primary btn-sm">Book</button></td>
                </form>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted mb-0">No available spaces at the moment.</p>
      <?php endif; ?>
    </div>
  </div>

  <h2 class="mb-3">My Reservations</h2>
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <?php if ($my->num_rows > 0): ?>
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Reservation ID</th>
              <th>Lot Name</th>
              <th>Type</th>
              <th>Start</th>
              <th>End</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($r = $my->fetch_assoc()): ?>
              <tr>
                <td><?= $r['Reservation_ID'] ?></td>
                <td><?= htmlspecialchars($r['LotName']) ?></td>
                <td><?= htmlspecialchars($r['Type']) ?></td>
                <td><?= htmlspecialchars($r['StartTime']) ?></td>
                <td><?= htmlspecialchars($r['EndTime']) ?></td>
                <td>
                  <span class="badge bg-info text-dark"><?= htmlspecialchars($r['Status']) ?></span>
                </td>
                <td>
                  <a href="reservations.php?cancel=<?= $r['Reservation_ID'] ?>" 
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Cancel this reservation?');">Cancel</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted mb-0">No current reservations.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
