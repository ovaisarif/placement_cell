<?php
session_start();
if(isset($_SESSION['student_id'])) { header("Location: dashboard.php"); exit(); }
if(isset($_SESSION['admin_id'])) { header("Location: admin_dashboard.php"); exit(); }
?>
<?php include 'includes/header.php'; ?>
<div class="hero rounded text-center mb-5">
    <h1 class="display-4 fw-bold">Welcome to Placement Cell</h1>
    <p class="lead">Connecting bright minds with great opportunities.</p>
    <div class="mt-4">
        <a href="register.php" class="btn btn-light btn-lg me-2 fw-bold text-primary">Student Registration</a>
        <a href="login.php" class="btn btn-outline-light btn-lg fw-bold">Login</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <h3 class="card-title text-primary mb-3">For Students</h3>
                <p class="card-text">Create your profile, upload your resume, and apply for top companies seamlessly.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <h3 class="card-title text-success mb-3">Top Companies</h3>
                <p class="card-text">Explore opportunities from industry leaders looking for fresh talent like you.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <h3 class="card-title text-info mb-3">Admin Portal</h3>
                <p class="card-text">Streamlined management of drives, applications, and student records.</p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
