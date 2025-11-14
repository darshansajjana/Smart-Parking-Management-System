<?php
include '../header.php';
include '../db_connection.php';

if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Add a new parking space
if (isset($_POST['add_space'])) {
    $lot_id = intval($_POST['lot_id']);
    $type = trim($_POST['type']);
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("INSERT INTO Parking_Space (Lot_ID, Type, Status) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $lot_id, $type, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: parking_spaces.php");
    exit();
}

// Edit existing space
if (isset($_POST['edit_space'])) {
    $space_id = intval($_POST['space_id']);
    $lot_id = intval($_POST['lot_id']);
    $type = trim($_POST['type']);
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("UPDATE Parking_Space SET Lot_ID = ?, Type = ?, Status = ? WHERE Space_ID = ?");
    $stmt->bind_param('issi', $lot_id, $type, $status, $space_id);
    $stmt->execute();
    $stmt->close();

    header("Location: parking_spaces.php");
    exit();
}

// Delete space
if (isset($_GET['delete'])) {
    $space_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Parking_Space WHERE Space_ID = ?");
    $stmt->bind_param('i', $space_id);
    $stmt->execute();
    $stmt->close();

    header("Location: parking_spaces.php");
    exit();
}

// Fetch lots for dropdown
$lots = $conn->query("SELECT Lot_ID, Name FROM Parking_Lot ORDER BY Name");

// Fetch spaces (with Location)
$spaces = $conn->query("SELECT s.Space_ID, s.Type, s.Status, s.Lot_ID, p.Name AS LotName, p.Location
                       FROM Parking_Space s
                       LEFT JOIN Parking_Lot p ON s.Lot_ID = p.Lot_ID
                       ORDER BY p.Name, s.Space_ID");
?>

<div class="card shadow-sm p-3">
  <h3>Manage Parking Spaces</h3>
  <p class="text-muted">View, create, edit or remove parking spaces. These are used by owners for reservations.</p>

  <!-- SPACES LIST FULL WIDTH -->
  <div class="card p-3 mb-4">
    <h5>Spaces List</h5>
    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <thead class="table-dark">
          <tr>
            <th>Space ID</th>
            <th>Lot Name</th>
            <th>Location</th>
            <th>Type</th>
            <th>Status</th>
            <th style="width:140px">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($s = $spaces->fetch_assoc()): ?>
          <tr>
            <td><?= intval($s['Space_ID']) ?></td>
            <td><?= htmlspecialchars($s['LotName']) ?></td>
            <td><?= htmlspecialchars($s['Location']) ?></td>
            <td><?= htmlspecialchars($s['Type']) ?></td>
            <td>
              <?php
                $badge = 'secondary';
                if ($s['Status'] == 'Available') $badge = 'success';
                elseif ($s['Status'] == 'Reserved') $badge = 'warning text-dark';
                elseif ($s['Status'] == 'Occupied') $badge = 'danger';
                elseif ($s['Status'] == 'Maintenance') $badge = 'secondary';
              ?>
              <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($s['Status']) ?></span>
            </td>
            <td>
              <a href="parking_spaces.php?edit=<?= intval($s['Space_ID']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <a href="parking_spaces.php?delete=<?= intval($s['Space_ID']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete space?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ADD NEW SPACE + QUICK STATS SIDE BY SIDE -->
  <div class="row">
    <div class="col-md-7">
      <div class="card p-3 mb-3">
        <h5>Add New Space</h5>
        <form method="post" class="row g-2">
          <div class="col-12">
            <label class="form-label">Parking Lot</label>
            <select name="lot_id" class="form-select" required>
              <option value="">Select Lot</option>
              <?php
              $lots->data_seek(0);
              while ($l = $lots->fetch_assoc()) {
                  echo '<option value="'.intval($l['Lot_ID']).'">'.htmlspecialchars($l['Name']).'</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Type</label>
            <input name="type" class="form-control" placeholder="e.g., Car / Bike / Compact" required>
          </div>
          <div class="col-12">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="Available">Available</option>
              <option value="Reserved">Reserved</option>
              <option value="Occupied">Occupied</option>
              <option value="Maintenance">Maintenance</option>
            </select>
          </div>
          <div class="col-12">
            <button name="add_space" class="btn btn-primary">Add Space</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card p-3 mb-3">
        <h5>Quick Stats</h5>
        <?php
        $total_spaces = $conn->query("SELECT COUNT(*) AS c FROM Parking_Space")->fetch_assoc()['c'];
        $available = $conn->query("SELECT COUNT(*) AS c FROM Parking_Space WHERE Status='Available'")->fetch_assoc()['c'];
        $occupied = $conn->query("SELECT COUNT(*) AS c FROM Parking_Space WHERE Status='Occupied'")->fetch_assoc()['c'];
        ?>
        <ul class="list-unstyled">
          <li>Total spaces: <strong><?= intval($total_spaces) ?></strong></li>
          <li>Available: <strong><?= intval($available) ?></strong></li>
          <li>Occupied: <strong><?= intval($occupied) ?></strong></li>
        </ul>
      </div>
    </div>
  </div>

  <?php
  // If editing, show edit form at the bottom
  if (isset($_GET['edit'])):
      $edit_id = intval($_GET['edit']);
      $es = $conn->prepare("SELECT Space_ID, Lot_ID, Type, Status FROM Parking_Space WHERE Space_ID = ?");
      $es->bind_param('i', $edit_id);
      $es->execute();
      $res = $es->get_result();
      $space = $res->fetch_assoc();
      $es->close();
      if ($space):
  ?>
  <hr>
  <h5>Edit Space #<?= intval($space['Space_ID']) ?></h5>
  <form method="post" class="row g-2">
    <input type="hidden" name="space_id" value="<?= intval($space['Space_ID']) ?>">
    <div class="col-md-4">
      <label class="form-label">Parking Lot</label>
      <select name="lot_id" class="form-select" required>
        <option value="">Select Lot</option>
        <?php
        $lots->data_seek(0);
        while ($l = $lots->fetch_assoc()) {
            $sel = ($l['Lot_ID'] == $space['Lot_ID']) ? 'selected' : '';
            echo '<option value="'.intval($l['Lot_ID']).'" '.$sel.'>'.htmlspecialchars($l['Name']).'</option>';
        }
        ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Type</label>
      <input name="type" class="form-control" value="<?= htmlspecialchars($space['Type']) ?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        <?php
        $statuses = ['Available','Reserved','Occupied','Maintenance'];
        foreach ($statuses as $st) {
            $sel = ($st == $space['Status']) ? 'selected' : '';
            echo "<option value=\"$st\" $sel>$st</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-12">
      <button name="edit_space" class="btn btn-success">Save Changes</button>
      <a href="parking_spaces.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
  <?php endif; endif; ?>
</div>

<?php include '../footer.php'; ?>
