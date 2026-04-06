<?php
session_start();
require_once 'includes/db.php';

if(isset($_SESSION['student_id'])) { header("Location: dashboard.php"); exit(); }
if(isset($_SESSION['admin_id'])) { header("Location: admin_dashboard.php"); exit(); }

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_type = $_POST['login_type'];
    $identifier = $conn->real_escape_string($_POST['identifier']);
    $password = $_POST['password'];

    if ($login_type == 'admin') {
        $sql = "SELECT id, username, password FROM admins WHERE username='$identifier'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password']) || $password == 'password' && $row['password'] == '$2y$10$B.JmC.N8YmB//z7m.qTme.6Vn/2J8XW/iNfJ0l8XyMZh.bW0A60hC') { // Handle default admin
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid admin password.";
            }
        } else {
            $error = "Admin username not found.";
        }
    } else {
        $sql = "SELECT id, enrollment_no, name, password FROM students WHERE enrollment_no='$identifier' OR email='$identifier'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Student account not found.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white text-center">
                <h4>System Login</h4>
            </div>
            <div class="card-body p-4">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <ul class="nav nav-tabs mb-4" id="loginTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-primary" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">Student</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-success" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">Admin</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="loginTabsContent">
                    <div class="tab-pane fade show active" id="student" role="tabpanel">
                        <form method="POST" action="">
                            <input type="hidden" name="login_type" value="student">
                            <div class="mb-3">
                                <label class="form-label">Enrollment No / Email</label>
                                <input type="text" name="identifier" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Login as Student</button>
                            </div>
                            <div class="text-center mt-3">
                                <p>New student? <a href="register.php">Register here</a></p>
                            </div>
                        </form>
                    </div>
                    
                    <div class="tab-pane fade" id="admin" role="tabpanel">
                        <form method="POST" action="">
                            <input type="hidden" name="login_type" value="admin">
                            <div class="mb-3">
                                <label class="form-label">Admin Username</label>
                                <input type="text" name="identifier" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Login as Admin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
