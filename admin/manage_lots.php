<?php
session_start();
include '../db_connection.php';
if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Add new lot
if (isset($_POST['add_lot'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $capacity = intval($_POST['capacity']);
    $stmt = $conn->prepare("INSERT INTO Parking_Lot (Name, Location, Capacity) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $name, $location, $capacity);
    $stmt->execute();
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Parking_Lot WHERE Lot_ID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM Parking_Lot");
?>
<?php include '../header.php'; ?>
<div class="card p-3">
  <h3>Manage Parking Lots</h3>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr><th>ID</th><th>Name</th><th>Location</th><th>Capacity</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= $row['Lot_ID'] ?></td>
        <td><?= htmlspecialchars($row['Name']) ?></td>
        <td><?= htmlspecialchars($row['Location']) ?></td>
        <td><?= $row['Capacity'] ?></td>
        <td>
          <a href="?delete=<?= $row['Lot_ID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this lot?')">Delete</a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <h5 class="mt-4">Add New Parking Lot</h5>
  <form method="post">
    <input type="text" name="name" placeholder="Name" required class="form-control mb-2">
    <input type="text" name="location" placeholder="Location" required class="form-control mb-2">
    <input type="number" name="capacity" placeholder="Capacity" required class="form-control mb-2">
    <button type="submit" name="add_lot" class="btn btn-primary">Add Lot</button>
  </form>
</div>
<?php include '../footer.php'; ?>
