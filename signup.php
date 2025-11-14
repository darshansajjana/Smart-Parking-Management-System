<?php
session_start();

// ✅ Root DB connection (has CREATE USER privileges)
$servername = "localhost";
$username = "root";
$password = "darshan@1525";
$dbname = "parking_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SIGNUP logic
if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT Owner_ID FROM Owner WHERE Email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $error_signup = "Email already registered!";
    } else {
        // Insert new owner into Owner table
        $stmt2 = $conn->prepare("INSERT INTO Owner (Name, Phone, Email, Password) VALUES (?,?,?,?)");
        $stmt2->bind_param('ssss', $name, $phone, $email, $hashed_pass);
        $stmt2->execute();

        // ✅ Generate MySQL username
        $mysql_username = strtolower(preg_replace("/[^a-zA-Z0-9_]/", "_", $name));
        $mysql_password = $password;

        // ✅ If admin, skip MySQL user creation
        if (strtolower($email) === 'admin@gmail.com') {
            $success = "Admin registered in Owner table. Admin MySQL user already exists (manual setup).";
        } else {
            // ✅ Create MySQL user for Owner
            $create_user_sql = "CREATE USER IF NOT EXISTS '$mysql_username'@'localhost' IDENTIFIED BY '$mysql_password'";
            if ($conn->query($create_user_sql) === TRUE) {

                // ✅ Grant limited privileges to Owner
                $grant_queries = [
                    "GRANT SELECT ON parking_system.Rate_Config TO '$mysql_username'@'localhost'",
                    "GRANT SELECT ON parking_system.Parking_Space TO '$mysql_username'@'localhost'",
                    "GRANT SELECT ON parking_system.Parking_Lot TO '$mysql_username'@'localhost'",
                    "GRANT SELECT ON parking_system.Payment TO '$mysql_username'@'localhost'",
                    "GRANT INSERT ON parking_system.Payment TO '$mysql_username'@'localhost'",
                    "GRANT SELECT ON parking_system.Parking_Ticket TO '$mysql_username'@'localhost'",
                    "GRANT SELECT ON parking_system.Issued_For TO '$mysql_username'@'localhost'",
                    "GRANT SELECT, INSERT, DELETE ON parking_system.Vehicle TO '$mysql_username'@'localhost'",
                    "GRANT SELECT, INSERT, DELETE ON parking_system.Reservation TO '$mysql_username'@'localhost'",
                    "GRANT SELECT, INSERT, DELETE ON parking_system.Booking TO '$mysql_username'@'localhost'",
                    "GRANT UPDATE ON parking_system.Parking_Space TO '$mysql_username'@'localhost'",
                    "GRANT EXECUTE ON FUNCTION parking_system.calculate_fee_by_type TO '$mysql_username'@'localhost'",
                    "GRANT EXECUTE ON FUNCTION parking_system.is_vehicle_parked TO '$mysql_username'@'localhost'"
                ];

                foreach ($grant_queries as $gq) {
                    $conn->query($gq);
                }

                $conn->query("FLUSH PRIVILEGES");
                $success = "Registration successful! MySQL user '$mysql_username' created successfully.";
            } else {
                $error_signup = "Owner added, but failed to create MySQL user (possibly username already exists).";
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-lg border-0">
      <div class="card-body">
        <h3 class="text-center mb-4">Signup</h3>
        <form method="post">
          <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" name="signup" class="btn btn-success w-100">Sign Up</button>
        </form>

        <?php
        if (isset($error_signup)) echo "<p class='text-danger mt-2'>" . htmlspecialchars($error_signup) . "</p>";
        if (isset($success)) echo "<p class='text-success mt-2'>" . htmlspecialchars($success) . "</p>";
        ?>
        <div class="text-center mt-3">
          <a href="login.php">Already have an account? Login</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
