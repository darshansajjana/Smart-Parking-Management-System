<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['Owner_ID'];

// Add new vehicle
if (isset($_POST['add_vehicle'])) {
    $type = trim($_POST['type']);
    $plate = strtoupper(trim($_POST['plate'])); // ✅ Convert plate to uppercase before saving

    $stmt = $conn->prepare("INSERT INTO Vehicle (Type, Plate_no, Owner_ID) VALUES (?,?,?)");
    $stmt->bind_param('ssi', $type, $plate, $owner_id);
    $stmt->execute();
    $stmt->close();
}

// Delete vehicle
if (isset($_GET['delete'])) {
    $plate = strtoupper(trim($_GET['delete'])); // ✅ Also ensure delete comparison matches uppercase format
    $stmt = $conn->prepare("DELETE FROM Vehicle WHERE Plate_no=? AND Owner_ID=?");
    $stmt->bind_param('si', $plate, $owner_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch vehicles
$result = $conn->query("SELECT * FROM Vehicle WHERE Owner_ID='$owner_id'");
?>

<?php include '../header.php'; ?>

<div class="card p-3 shadow-sm">
  <h3>My Vehicles</h3>

  <table class="table table-bordered table-striped align-middle text-center mt-3">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Plate No</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { 
          $vid = (int)$row['Vehicle_ID'];

          // Check if vehicle is parked
          $status_query = $conn->query("SELECT is_vehicle_parked($vid) AS parked");
          $is_parked = ($status_query && $status_query->num_rows > 0)
                      ? (int)$status_query->fetch_assoc()['parked']
                      : 0;

          $status_text = $is_parked
              ? "<span class='badge bg-success'>Parked</span>"
              : "<span class='badge bg-secondary'>Not Parked</span>";
      ?>
      <tr>
        <td><?= $row['Vehicle_ID'] ?></td>
        <td><?= htmlspecialchars($row['Type']) ?></td>
        <td><?= strtoupper(htmlspecialchars($row['Plate_no'])) ?></td> 
        <td><?= $status_text ?></td>
        <td>
          <a href="?delete=<?= urlencode(strtoupper($row['Plate_no'])) ?>" 
             class="btn btn-danger btn-sm"
             onclick="return confirm('Delete vehicle?')">Delete</a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <h5 class="mt-4">Add Vehicle</h5>
  <form method="post" class="mt-2">
    <input type="text" name="type" placeholder="Type (Car/Bike/etc)" required class="form-control mb-2">
    <input type="text" name="plate" placeholder="Plate No" required class="form-control mb-2" style="text-transform: uppercase;"> <!-- ✅ Auto uppercase in input -->
    <button type="submit" name="add_vehicle" class="btn btn-success">Add Vehicle</button>
  </form>
</div>

<?php include '../footer.php'; ?>
