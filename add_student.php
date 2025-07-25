<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    try {
        // Sanitize input data
        $student_data = [
            'name' => sanitizeInput($_POST['name']),
            'email' => sanitizeInput($_POST['email']),
            'date_of_birth' => sanitizeInput($_POST['date_of_birth']),
            'course' => sanitizeInput($_POST['course']),
            'grade' => sanitizeInput($_POST['grade'])
        ];
        
        // Add student
        if (addStudent($student_data)) {
            header('Location: view_students.php?success=student_added');
            exit();
        } else {
            throw new Exception("Failed to add student");
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get available courses
$courses = getAvailableCourses();

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Add New Student
                </h4>
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data" id="addStudentForm" onsubmit="return validateForm('addStudentForm')">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Full Name *
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a valid name.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Address *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                       required onblur="validateEmail(this)">
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date of Birth *
                                </label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : ''; ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Please select a valid date of birth.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="course" class="form-label">
                                    <i class="fas fa-book me-1"></i>Course *
                                </label>
                                <select class="form-select" id="course" name="course" required>
                                    <option value="">Select a course</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo htmlspecialchars($course); ?>" 
                                                <?php echo (isset($_POST['course']) && $_POST['course'] === $course) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a course.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grade" class="form-label">
                                    <i class="fas fa-star me-1"></i>Grade (%) *
                                </label>
                                <input type="number" class="form-control" id="grade" name="grade" 
                                       value="<?php echo isset($_POST['grade']) ? htmlspecialchars($_POST['grade']) : ''; ?>" 
                                       min="0" max="100" step="0.01" required onblur="validateGrade(this)">
                                <div class="invalid-feedback">
                                    Grade must be a number between 0 and 100.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">
                                    <i class="fas fa-image me-1"></i>Profile Picture
                                </label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" 
                                       accept="image/*">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Allowed formats: JPG, JPEG, PNG, GIF (Max: 5MB)
                                </div>
                                <div class="invalid-feedback">
                                    Please select a valid image file.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="view_students.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Students
                                </a>
                                <button type="submit" name="add_student" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Add Student
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Form Preview -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>Form Preview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div id="imagePreview" class="profile-picture bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h5 id="previewName">Student Name</h5>
                        <p class="text-muted mb-1">
                            <i class="fas fa-envelope me-1"></i>
                            <span id="previewEmail">email@example.com</span>
                        </p>
                        <p class="text-muted mb-1">
                            <i class="fas fa-calendar me-1"></i>
                            <span id="previewDOB">Date of Birth</span>
                        </p>
                        <p class="text-muted mb-1">
                            <i class="fas fa-book me-1"></i>
                            <span id="previewCourse">Course</span>
                        </p>
                        <p class="text-muted mb-0">
                            <i class="fas fa-star me-1"></i>
                            Grade: <span id="previewGrade" class="badge bg-primary">0%</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time form preview
document.getElementById('name').addEventListener('input', function() {
    document.getElementById('previewName').textContent = this.value || 'Student Name';
});

document.getElementById('email').addEventListener('input', function() {
    document.getElementById('previewEmail').textContent = this.value || 'email@example.com';
});

document.getElementById('date_of_birth').addEventListener('input', function() {
    const date = new Date(this.value);
    const formattedDate = this.value ? date.toLocaleDateString() : 'Date of Birth';
    document.getElementById('previewDOB').textContent = formattedDate;
});

document.getElementById('course').addEventListener('change', function() {
    document.getElementById('previewCourse').textContent = this.value || 'Course';
});

document.getElementById('grade').addEventListener('input', function() {
    const grade = this.value || '0';
    const badge = document.getElementById('previewGrade');
    badge.textContent = grade + '%';
    
    // Update badge color based on grade
    badge.className = 'badge';
    if (grade >= 90) badge.classList.add('bg-success');
    else if (grade >= 80) badge.classList.add('bg-primary');
    else if (grade >= 70) badge.classList.add('bg-warning');
    else badge.classList.add('bg-danger');
});

// Image preview
document.getElementById('profile_picture').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<i class="fas fa-user text-white"></i>';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 