<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (deleteStudent($id)) {
        header('Location: view_students.php?success=student_deleted');
        exit();
    } else {
        header('Location: view_students.php?error=delete_failed');
        exit();
    }
}

// Handle edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    try {
        $id = (int)$_POST['student_id'];
        
        // Sanitize input data
        $student_data = [
            'name' => sanitizeInput($_POST['name']),
            'email' => sanitizeInput($_POST['email']),
            'date_of_birth' => sanitizeInput($_POST['date_of_birth']),
            'course' => sanitizeInput($_POST['course']),
            'grade' => sanitizeInput($_POST['grade'])
        ];
        
        // Update student
        if (updateStudent($id, $student_data)) {
            header('Location: view_students.php?success=student_updated');
            exit();
        } else {
            throw new Exception("Failed to update student");
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get students
$students = getAllStudents();
$courses = getAvailableCourses();

// Handle edit mode
$edit_student = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_student = getStudentById((int)$_GET['id']);
    if (!$edit_student) {
        header('Location: view_students.php?error=student_not_found');
        exit();
    }
}

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>
                <i class="fas fa-users me-2"></i>Student Management
            </h2>
            <a href="add_student.php" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add New Student
            </a>
        </div>
    </div>
</div>

<?php if ($edit_student): ?>
<!-- Edit Student Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Student
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" 
                                       value="<?php echo htmlspecialchars($edit_student['name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" 
                                       value="<?php echo htmlspecialchars($edit_student['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_date_of_birth" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" id="edit_date_of_birth" name="date_of_birth" 
                                       value="<?php echo htmlspecialchars($edit_student['date_of_birth']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_course" class="form-label">Course *</label>
                                <select class="form-select" id="edit_course" name="course" required>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo htmlspecialchars($course); ?>" 
                                                <?php echo ($edit_student['course'] === $course) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_grade" class="form-label">Grade (%) *</label>
                                <input type="number" class="form-control" id="edit_grade" name="grade" 
                                       value="<?php echo htmlspecialchars($edit_student['grade']); ?>" 
                                       min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="edit_profile_picture" name="profile_picture" 
                                       accept="image/*">
                                <div class="form-text">Leave empty to keep current image</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="view_students.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update_student" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Students Table -->
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>All Students (<?php echo count($students); ?>)
                </h5>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search students...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($students)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No students found</h4>
                <p class="text-muted">Start by adding your first student.</p>
                <a href="add_student.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Student
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="studentsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date of Birth</th>
                            <th>Course</th>
                            <th>Grade</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($student['profile_picture'])): ?>
                                        <img src="uploads/<?php echo $student['profile_picture']; ?>" 
                                             alt="Profile" class="profile-picture">
                                    <?php else: ?>
                                        <div class="profile-picture bg-secondary d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($student['date_of_birth'])); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($student['course']); ?></span>
                                </td>
                                <td>
                                    <span class="badge <?php echo getGradeColor($student['grade']); ?> grade-badge">
                                        <?php echo formatGrade($student['grade']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="view_students.php?action=edit&id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view_students.php?action=delete&id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" title="Delete"
                                           onclick="return confirmDelete('Are you sure you want to delete <?php echo htmlspecialchars($student['name']); ?>?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('studentsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.includes(searchTerm)) {
                found = true;
                break;
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
});

// Auto-show edit modal if in edit mode
<?php if ($edit_student): ?>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
});
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?> 