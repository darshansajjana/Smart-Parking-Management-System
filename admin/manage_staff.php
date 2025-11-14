<?php
session_start();
include '../db_connection.php';
if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Add staff
if (isset($_POST['add_staff'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $lot_id = intval($_POST['lot_id']);

    $stmt = $conn->prepare("INSERT INTO Staff (Name, Role, Phone, Lot_ID) VALUES (?,?,?,?)");
    $stmt->bind_param('sssi', $name, $role, $phone, $lot_id);
    $stmt->execute();
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Staff WHERE Staff_ID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

$lots = $conn->query("SELECT Lot_ID, Name FROM Parking_Lot");
$result = $conn->query("SELECT s.*, p.Name AS LotName FROM Staff s LEFT JOIN Parking_Lot p ON s.Lot_ID = p.Lot_ID");
?>
<?php include '../header.php'; ?>
<div class="card p-3">
  <h3>Manage Staff</h3>

  <table class="table table-bordered">
    <thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Role</th><th>Phone</th><th>Lot</th><th>Action</th></tr></thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= $row['Staff_ID'] ?></td>
        <td><?= htmlspecialchars($row['Name']) ?></td>
        <td><?= htmlspecialchars($row['Role']) ?></td>
        <td><?= htmlspecialchars($row['Phone']) ?></td>
        <td><?= htmlspecialchars($row['LotName']) ?></td>
        <td><a href="?delete=<?= $row['Staff_ID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete staff?')">Delete</a></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <h5 class="mt-4">Add Staff</h5>
  <form method="post">
    <input type="text" name="name" placeholder="Full name" required class="form-control mb-2">
    <input type="text" name="role" placeholder="Role (Attendant/Manager)" required class="form-control mb-2">
    <input type="text" name="phone" placeholder="Phone" required class="form-control mb-2">
    <select name="lot_id" class="form-control mb-2" required>
      <option value="">Assign Lot</option>
      <?php

      $lres = $conn->query("SELECT Lot_ID, Name FROM Parking_Lot");
      while ($l = $lres->fetch_assoc()) { ?>
        <option value="<?= $l['Lot_ID'] ?>"><?= htmlspecialchars($l['Name']) ?></option>
      <?php } ?>
    </select>
    <button type="submit" name="add_staff" class="btn btn-success">Add Staff</button>
  </form>
</div>
<?php include '../footer.php'; ?>
