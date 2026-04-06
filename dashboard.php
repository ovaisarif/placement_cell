<?php
session_start();
require_once 'includes/db.php';

if(!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

// Handle Resume Upload
$upload_msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['resume'])) {
    $target_dir = "images/resumes/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);
    $new_filename = "student_" . $student_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if pdf
    if(strtolower($file_extension) != "pdf") {
        $upload_msg = "<div class='alert alert-danger'>Only PDF files are allowed.</div>";
    } else {
        if(move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
            $sql = "UPDATE students SET resume_path='$target_file' WHERE id=$student_id";
            if($conn->query($sql)) {
                $upload_msg = "<div class='alert alert-success'>Resume uploaded successfully.</div>";
            }
        } else {
            $upload_msg = "<div class='alert alert-danger'>Sorry, there was an error uploading your file.</div>";
        }
    }
}

// Handle Job Application
$apply_msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job_id'])) {
    $job_id = (int)$_POST['apply_job_id'];
    
    // Check if resume exists
    $check_resume = "SELECT resume_path FROM students WHERE id=$student_id";
    $resume_result = $conn->query($check_resume);
    $resume_row = $resume_result->fetch_assoc();
    
    if(empty($resume_row['resume_path'])) {
         $apply_msg = "<div class='alert alert-warning'>Please upload your resume before applying for jobs.</div>";
    } else {
        $sql = "INSERT INTO applications (student_id, job_id) VALUES ($student_id, $job_id)";
        if($conn->query($sql)) {
            $apply_msg = "<div class='alert alert-success'>Successfully applied for the job!</div>";
        } else {
            if($conn->errno == 1062) { // Duplicate entry
                $apply_msg = "<div class='alert alert-info'>You have already applied for this job.</div>";
            } else {
                $apply_msg = "<div class='alert alert-danger'>Error applying for job: " . $conn->error . "</div>";
            }
        }
    }
}

// Get student details
$student_query = "SELECT * FROM students WHERE id=$student_id";
$student_info = $conn->query($student_query)->fetch_assoc();

?>
<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">My Profile</h5>
            </div>
            <div class="card-body">
                <h4 class="text-center mb-4"><?php echo htmlspecialchars($student_info['name']); ?></h4>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Enrollment No:</strong>
                        <span><?php echo htmlspecialchars($student_info['enrollment_no']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Course:</strong>
                        <span><?php echo htmlspecialchars($student_info['course']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Graduation Year:</strong>
                        <span><?php echo htmlspecialchars($student_info['graduation_year']); ?></span>
                    </li>
                </ul>
                
                <hr>
                
                <h6 class="mb-3">Resume Management</h6>
                <?php echo $upload_msg; ?>
                
                <?php if(!empty($student_info['resume_path'])): ?>
                    <div class="alert alert-info py-2 mb-3">
                        <i class="me-2">📄</i> Resume Uploaded
                        <a href="<?php echo htmlspecialchars($student_info['resume_path']); ?>" target="_blank" class="ms-2 badge bg-primary text-decoration-none">View</a>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" class="form-control form-control-sm" name="resume" accept=".pdf" required>
                        <small class="text-muted">PDF files only (Max 2MB)</small>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">Upload / Update Resume</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <?php echo $apply_msg; ?>
        
        <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs" type="button" role="tab">Available Jobs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">My Applications</button>
            </li>
        </ul>
        
        <div class="tab-content" id="dashboardTabsContent">
            <!-- Available Jobs Tab -->
            <div class="tab-pane fade show active" id="jobs" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Latest Job Opportunities</h5>
                        <?php
                        $jobs_query = "SELECT j.*, c.name as company_name 
                                       FROM jobs j 
                                       JOIN companies c ON j.company_id = c.id 
                                       WHERE j.deadline >= CURDATE()
                                       ORDER BY j.created_at DESC";
                        $jobs_result = $conn->query($jobs_query);
                        
                        if($jobs_result->num_rows > 0):
                        ?>
                            <div class="row g-3">
                            <?php while($job = $jobs_result->fetch_assoc()): ?>
                                <div class="col-md-6">
                                    <div class="card h-100 border-primary border-opacity-25">
                                        <div class="card-body">
                                            <h5 class="text-primary mb-1"><?php echo htmlspecialchars($job['title']); ?></h5>
                                            <h6 class="text-muted mb-3"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                                            <p class="card-text small text-truncate" style="max-width: 100%;"><?php echo htmlspecialchars($job['description']); ?></p>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="badge bg-light text-dark border"><i class="me-1">💰</i><?php echo htmlspecialchars($job['salary']); ?></span>
                                                <span class="badge bg-light text-danger border"><i class="me-1">📅</i><?php echo htmlspecialchars($job['deadline']); ?></span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent border-top-0 pt-0">
                                            <form method="POST">
                                                <input type="hidden" name="apply_job_id" value="<?php echo $job['id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Apply Now</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light text-center">No active job listings available at the moment.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- My Applications Tab -->
            <div class="tab-pane fade" id="applications" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Application History</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Company</th>
                                        <th>Job Title</th>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $apps_query = "SELECT a.*, j.title, c.name as company_name 
                                                   FROM applications a 
                                                   JOIN jobs j ON a.job_id = j.id 
                                                   JOIN companies c ON j.company_id = c.id 
                                                   WHERE a.student_id = $student_id 
                                                   ORDER BY a.applied_at DESC";
                                    $apps_result = $conn->query($apps_query);
                                    
                                    if($apps_result->num_rows > 0):
                                        while($app = $apps_result->fetch_assoc()):
                                            $status_class = 'bg-warning text-dark';
                                            if($app['status'] == 'Selected') $status_class = 'bg-success';
                                            if($app['status'] == 'Rejected') $status_class = 'bg-danger';
                                    ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($app['company_name']); ?></td>
                                            <td><?php echo htmlspecialchars($app['title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                            <td><span class="badge <?php echo $status_class; ?>"><?php echo $app['status']; ?></span></td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr><td colspan="4" class="text-center text-muted">You haven't applied to any jobs yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
