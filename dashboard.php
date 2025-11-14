<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['Role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['Role'];
$username = $_SESSION['Username'];
?>
<?php include 'header.php'; ?>

<div class="card shadow p-4 border-0">
  <h2 class="text-center mb-3">Welcome, <?= htmlspecialchars($role) ?></h2>
  <p class="text-center">Logged in as: <b><?= htmlspecialchars($username) ?></b></p>
  <hr>

  <?php if ($role == 'Admin') { ?>
    <div class="row">
      <!-- Manage Parking Lots -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Manage Parking Lots</h5>
          <p class="text-muted">Create or edit parking lot details and capacity.</p>
          <a href="admin/manage_lots.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- Manage Parking Spaces -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Manage Parking Spaces</h5>
          <p class="text-muted">Add, update, or remove spaces with location mapping.</p>
          <a href="admin/parking_spaces.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- View Reports -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>View Reports</h5>
          <p class="text-muted">Check reservations, occupancy, and revenue reports.</p>
          <a href="admin/view_reports.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- Manage Staff -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Manage Staff</h5>
          <p class="text-muted">Add or remove staff and assign duties.</p>
          <a href="admin/manage_staff.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- Create Parking Ticket -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Create Parking Ticket</h5>
          <p class="text-muted">Issue Ticket for the existing Vehicles.</p>
          <a href="admin/create_ticket.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- Release Ticket -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Release Ticket</h5>
          <p class="text-muted">Release Ticket for the Exiting Vehichle.</p>
          <a href="admin/release_ticket.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>
    </div>
    </div>

  <?php } elseif ($role == 'Owner') { ?>
    <div class="row">
      <!-- My Vehicles -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>My Vehicles</h5>
          <p class="text-muted">View and manage registered vehicles.</p>
          <a href="owner/vehicles.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- Reservations -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Reservations</h5>
          <p class="text-muted">Book and manage reservations.</p>
          <a href="owner/reservations.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- Parking Space View -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Parking Space Availability</h5>
          <p class="text-muted">View live parking space status in your lot.</p>
          <a href="owner/parking_spaces_availability.php" class="btn btn-sm btn-primary">View</a>
        </div>
      </div>

      <!-- Calculate Fee -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Calculate Fee</h5>
          <p class="text-muted">Calculate the Fee before parking.</p>
          <a href="owner/calculate_fee.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>

      <!-- Payments History -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Payment History</h5>
          <p class="text-muted">View Payment History.</p>
          <a href="owner/payments_history.php" class="btn btn-sm btn-primary">View</a>
        </div>
      </div>

      <!-- Payments History -->
      <div class="col-md-4">
        <div class="card p-3 mb-3 shadow-sm">
          <h5>Payments</h5>
          <p class="text-muted">View and Pay unpaid Tickets.</p>
          <a href="owner/payments.php" class="btn btn-sm btn-primary">Open</a>
        </div>
      </div>
    </div>
  <?php } ?>

  <div class="text-center mt-4">
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>
</div>

<?php include 'footer.php'; ?>
