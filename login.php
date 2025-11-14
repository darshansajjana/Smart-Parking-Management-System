<?php
session_start();

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $servername = "localhost";
    $root_user = "root";
    $root_pass = "darshan@1525";
    $dbname = "parking_system";

    // ✅ Step 1: Admin check
    if (strtolower($email) === 'admin@gmail.com') {
        // Try connecting as admin directly
        $admin_conn = @new mysqli($servername, 'admin', $password, $dbname);

        if ($admin_conn->connect_error) {
            $error = "Invalid admin password or privileges issue.";
        } else {
            // ✅ Admin login successful
            $_SESSION['Username']  = "Admin";
            $_SESSION['Email']     = $email;
            $_SESSION['Role']      = "Admin";
            $_SESSION['Owner_ID']  = 0;  // dummy ID for compatibility
            $_SESSION['DB_User']   = "admin";
            $_SESSION['DB_Pass']   = $password;

            $admin_conn->close();
            header("Location: dashboard.php");
            exit();
        }
    } else {
        // ✅ Step 2: Root connection for verifying Owner credentials
        $root_conn = new mysqli($servername, $root_user, $root_pass, $dbname);
        if ($root_conn->connect_error) {
            die("Root connection failed: " . $root_conn->connect_error);
        }

        $stmt = $root_conn->prepare("SELECT Owner_ID, Name, Password, Email FROM Owner WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
            $mysql_username = strtolower(preg_replace("/[^a-zA-Z0-9_]/", "_", $user['Name']));

            // Check password (hashed or plain fallback)
            if (password_verify($password, $user['Password']) || $password === $user['Password']) {

                // ✅ Step 3: Try connecting with owner's MySQL user
                $user_conn = @new mysqli($servername, $mysql_username, $password, $dbname);

                if ($user_conn->connect_error) {
                    $error = "Access denied for MySQL user '$mysql_username'. Check privileges or password.";
                } else {
                    // ✅ Success
                    $_SESSION['Username']  = $user['Name'];
                    $_SESSION['Email']     = $user['Email'];
                    $_SESSION['Role']      = "Owner";
                    $_SESSION['Owner_ID']  = (int)$user['Owner_ID'];
                    $_SESSION['DB_User']   = $mysql_username;
                    $_SESSION['DB_Pass']   = $password;

                    $user_conn->close();
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
        }

        $root_conn->close();
    }
}
?>

<?php include 'header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-lg border-0">
      <div class="card-body">
        <h3 class="text-center mb-4">Login</h3>
        <form method="post">
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>

        <?php if (isset($error)) echo "<p class='text-danger mt-2 text-center'>" . htmlspecialchars($error) . "</p>"; ?>
        <div class="text-center mt-3">
          <a href="signup.php">Don't have an account? Sign Up</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
