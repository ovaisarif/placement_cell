<?php
session_start();
require_once 'includes/db.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];

// Handle Add Company
$company_msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_company'])) {
    $name = $conn->real_escape_string($_POST['company_name']);
    $desc = $conn->real_escape_string($_POST['company_desc']);
    $website = $conn->real_escape_string($_POST['company_web']);
    
    $sql = "INSERT INTO companies (name, description, website) VALUES ('$name', '$desc', '$website')";
    if($conn->query($sql)) {
        $company_msg = "<div class='alert alert-success'>Company added successfully!</div>";
    } else {
        $company_msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Add Job
$job_msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_job'])) {
    $company_id = (int)$_POST['company_id'];
    $title = $conn->real_escape_string($_POST['job_title']);
    $desc = $conn->real_escape_string($_POST['job_desc']);
    $req = $conn->real_escape_string($_POST['job_req']);
    $salary = $conn->real_escape_string($_POST['job_salary']);
    $deadline = $conn->real_escape_string($_POST['job_deadline']);
    
    $sql = "INSERT INTO jobs (company_id, title, description, requirements, salary, deadline) 
            VALUES ($company_id, '$title', '$desc', '$req', '$salary', '$deadline')";
    if($conn->query($sql)) {
        $job_msg = "<div class='alert alert-success'>Job posted successfully!</div>";
    } else {
        $job_msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Application Status Update
$status_msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $app_id = (int)$_POST['app_id'];
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    $sql = "UPDATE applications SET status='$new_status' WHERE id=$app_id";
    if($conn->query($sql)) {
        $status_msg = "<div class='alert alert-success'>Application status updated!</div>";
    } else {
        $status_msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Fetch basic stats
$students_count = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$companies_count = $conn->query("SELECT COUNT(*) as c FROM companies")->fetch_assoc()['c'];
$jobs_count = $conn->query("SELECT COUNT(*) as c FROM jobs")->fetch_assoc()['c'];
$apps_count = $conn->query("SELECT COUNT(*) as c FROM applications")->fetch_assoc()['c'];
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white text-center shadow">
            <div class="card-body">
                <h3><?php echo $students_count; ?></h3>
                <p class="mb-0">Students</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white text-center shadow">
            <div class="card-body">
                <h3><?php echo $companies_count; ?></h3>
                <p class="mb-0">Companies</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white text-center shadow">
            <div class="card-body">
                <h3><?php echo $jobs_count; ?></h3>
                <p class="mb-0">Active Jobs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark text-center shadow">
            <div class="card-body">
                <h3><?php echo $apps_count; ?></h3>
                <p class="mb-0">Total Applications</p>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-pills mb-4 shadow-sm bg-white p-2 rounded" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="companies-tab" data-bs-toggle="tab" data-bs-target="#companies" type="button" role="tab">Companies & Jobs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">Applications</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">Students</button>
    </li>
</ul>

<div class="tab-content" id="adminTabsContent">
    <!-- Companies and Jobs Tab -->
    <div class="tab-pane fade show active" id="companies" role="tabpanel">
        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">Add New Company</div>
                    <div class="card-body">
                        <?php echo $company_msg; ?>
                        <form method="POST">
                            <input type="hidden" name="add_company" value="1">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="company_desc" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input type="url" name="company_web" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Company</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">Post Job Opportunity</div>
                    <div class="card-body">
                        <?php echo $job_msg; ?>
                        <form method="POST">
                            <input type="hidden" name="add_job" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company</label>
                                    <select name="company_id" class="form-select" required>
                                        <option value="">Select Company</option>
                                        <?php
                                        $companies = $conn->query("SELECT id, name FROM companies ORDER BY name");
                                        while($comp = $companies->fetch_assoc()) {
                                            echo "<option value='{$comp['id']}'>{$comp['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Job Title</label>
                                    <input type="text" name="job_title" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Salary Package</label>
                                    <input type="text" name="job_salary" class="form-control" placeholder="e.g. 5 LPA" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Deadline</label>
                                    <input type="date" name="job_deadline" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="job_desc" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Requirements</label>
                                    <textarea name="job_req" class="form-control" rows="2" placeholder="e.g. B.Tech CS, 8+ CGPA" required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary w-100">Post Job</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Tab -->
    <div class="tab-pane fade" id="applications" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Manage Applications</div>
            <div class="card-body">
                <?php echo $status_msg; ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Job & Company</th>
                                <th>Resume</th>
                                <th>Date Applied</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $apps_query = "SELECT a.*, s.name as student_name, s.resume_path, j.title, c.name as company_name 
                                           FROM applications a 
                                           JOIN students s ON a.student_id = s.id 
                                           JOIN jobs j ON a.job_id = j.id 
                                           JOIN companies c ON j.company_id = c.id 
                                           ORDER BY a.applied_at DESC";
                            $apps_result = $conn->query($apps_query);
                            
                            while($app = $apps_result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($app['title']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($app['company_name']); ?></small>
                                </td>
                                <td>
                                    <?php if(!empty($app['resume_path'])): ?>
                                        <a href="<?php echo htmlspecialchars($app['resume_path']); ?>" target="_blank" class="btn btn-sm btn-outline-info">View</a>
                                    <?php else: ?>
                                        <span class="text-muted small">None</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d', strtotime($app['applied_at'])); ?></td>
                                <td>
                                    <span class="badge <?php echo ($app['status']=='Selected'?'bg-success':($app['status']=='Rejected'?'bg-danger':'bg-warning text-dark')); ?>">
                                        <?php echo $app['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="update_status" value="1">
                                        <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                        <select name="new_status" class="form-select form-select-sm me-2">
                                            <option value="Pending" <?php echo ($app['status']=='Pending'?'selected':''); ?>>Pending</option>
                                            <option value="Selected" <?php echo ($app['status']=='Selected'?'selected':''); ?>>Selected</option>
                                            <option value="Rejected" <?php echo ($app['status']=='Rejected'?'selected':''); ?>>Rejected</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Tab -->
    <div class="tab-pane fade" id="students" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Registered Students</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Enrollment No</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Resume</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $students_res = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
                            while($stu = $students_res->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stu['enrollment_no']); ?></td>
                                <td><?php echo htmlspecialchars($stu['name']); ?></td>
                                <td><?php echo htmlspecialchars($stu['course']); ?></td>
                                <td><?php echo htmlspecialchars($stu['graduation_year']); ?></td>
                                <td><?php echo htmlspecialchars($stu['email']); ?></td>
                                <td><?php echo htmlspecialchars($stu['phone']); ?></td>
                                <td>
                                    <?php if(!empty($stu['resume_path'])): ?>
                                        <a href="<?php echo htmlspecialchars($stu['resume_path']); ?>" target="_blank" class="badge bg-primary text-decoration-none">PDF</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
