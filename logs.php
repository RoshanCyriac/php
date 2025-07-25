<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

// Handle log file operations
$log_files = [
    'activity' => 'Activity Log',
    'error' => 'Error Log'
];

$selected_log = isset($_GET['log']) && array_key_exists($_GET['log'], $log_files) ? $_GET['log'] : 'activity';
$log_content = '';
$log_file_path = LOGS_DIR . $selected_log . '.log';

// Read log file
if (file_exists($log_file_path)) {
    $log_content = file_get_contents($log_file_path);
    if ($log_content === false) {
        $error_message = "Unable to read log file: $log_file_path";
    }
} else {
    $log_content = "Log file not found: $log_file_path";
}

// Handle log clearing
if (isset($_GET['action']) && $_GET['action'] === 'clear' && isset($_GET['log'])) {
    $log_to_clear = $_GET['log'];
    if (array_key_exists($log_to_clear, $log_files)) {
        $clear_file_path = LOGS_DIR . $log_to_clear . '.log';
        if (file_put_contents($clear_file_path, '') !== false) {
            logActivity('Log cleared', "Log file: $log_to_clear");
            header('Location: logs.php?log=' . $log_to_clear . '&success=log_cleared');
            exit();
        } else {
            $error_message = "Unable to clear log file";
        }
    }
}

// Handle log download
if (isset($_GET['action']) && $_GET['action'] === 'download' && isset($_GET['log'])) {
    $log_to_download = $_GET['log'];
    if (array_key_exists($log_to_download, $log_files)) {
        $download_file_path = LOGS_DIR . $log_to_download . '.log';
        if (file_exists($download_file_path)) {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . $log_to_download . '_log.txt"');
            header('Content-Length: ' . filesize($download_file_path));
            readfile($download_file_path);
            exit();
        }
    }
}

require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>
                <i class="fas fa-file-alt me-2"></i>System Logs
            </h2>
            <div class="btn-group" role="group">
                <a href="logs.php?action=download&log=<?php echo $selected_log; ?>" class="btn btn-outline-primary">
                    <i class="fas fa-download me-1"></i>Download
                </a>
                <a href="logs.php?action=clear&log=<?php echo $selected_log; ?>" 
                   class="btn btn-outline-danger"
                   onclick="return confirmDelete('Are you sure you want to clear this log file?')">
                    <i class="fas fa-trash me-1"></i>Clear
                </a>
            </div>
        </div>
        <p class="text-muted">View and manage system log files</p>
    </div>
</div>

<!-- Log File Selection -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Select Log File
                </h5>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <?php foreach ($log_files as $log_key => $log_name): ?>
                        <a href="?log=<?php echo $log_key; ?>" 
                           class="btn <?php echo $selected_log === $log_key ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <i class="fas fa-file-alt me-1"></i><?php echo $log_name; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Content -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-text me-2"></i><?php echo $log_files[$selected_log]; ?>
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-secondary me-2">
                    <?php echo file_exists($log_file_path) ? number_format(filesize($log_file_path)) . ' bytes' : '0 bytes'; ?>
                </span>
                <button class="btn btn-sm btn-outline-secondary" onclick="refreshLog()">
                    <i class="fas fa-refresh"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($log_content)): ?>
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Log file is empty</h4>
                <p class="text-muted">No entries found in the selected log file.</p>
            </div>
        <?php else: ?>
            <div class="log-container">
                <pre class="log-content"><?php echo htmlspecialchars($log_content); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Log Statistics -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Log Statistics
                </h6>
            </div>
            <div class="card-body">
                <?php
                $total_entries = 0;
                $today_entries = 0;
                $today = date('Y-m-d');
                
                if (!empty($log_content)) {
                    $lines = explode("\n", $log_content);
                    $total_entries = count(array_filter($lines)); // Count non-empty lines
                    
                    foreach ($lines as $line) {
                        if (strpos($line, $today) === 0) {
                            $today_entries++;
                        }
                    }
                }
                ?>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary"><?php echo number_format($total_entries); ?></h4>
                        <small class="text-muted">Total Entries</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success"><?php echo number_format($today_entries); ?></h4>
                        <small class="text-muted">Today's Entries</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Log Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <p class="mb-2">
                            <strong>File:</strong> <?php echo $log_file_path; ?>
                        </p>
                        <p class="mb-2">
                            <strong>Last Modified:</strong> 
                            <?php echo file_exists($log_file_path) ? date('Y-m-d H:i:s', filemtime($log_file_path)) : 'N/A'; ?>
                        </p>
                        <p class="mb-0">
                            <strong>File Size:</strong> 
                            <?php echo file_exists($log_file_path) ? number_format(filesize($log_file_path)) . ' bytes' : 'N/A'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Summary -->
<?php if ($selected_log === 'activity' && !empty($log_content)): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-clock me-2"></i>Recent Activity Summary
            </h6>
        </div>
        <div class="card-body">
            <?php
            $lines = array_filter(explode("\n", $log_content));
            $recent_lines = array_slice($lines, -10); // Get last 10 entries
            ?>
            <div class="list-group list-group-flush">
                <?php foreach (array_reverse($recent_lines) as $line): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <small class="text-muted">
                                    <?php 
                                    $parts = explode(' - ', $line, 2);
                                    if (count($parts) >= 2) {
                                        echo htmlspecialchars($parts[0]);
                                    }
                                    ?>
                                </small>
                                <p class="mb-0">
                                    <?php 
                                    if (count($parts) >= 2) {
                                        echo htmlspecialchars($parts[1]);
                                    } else {
                                        echo htmlspecialchars($line);
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
.log-container {
    max-height: 500px;
    overflow-y: auto;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
}

.log-content {
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}

@media (prefers-color-scheme: dark) {
    .log-container {
        background-color: #343a40;
        border-color: #495057;
    }
    
    .log-content {
        color: #e9ecef;
    }
}
</style>

<script>
function refreshLog() {
    location.reload();
}

// Auto-refresh every 30 seconds
setInterval(function() {
    refreshLog();
}, 30000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        refreshLog();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 