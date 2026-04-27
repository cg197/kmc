<?php
// 1. Always start with requirements and sessions
require_once 'functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// 3. Database Connection
$host = "localhost"; $db_user = "choolweg_kabwe_council_db"; $db_pass = "gambwe1997"; $db_name = "choolweg_kabwe_council_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. Handle Approval/Rejection Action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';

    // Update the database
    $stmt = $conn->prepare("UPDATE service_requests SET status = ? WHERE request_id = ?");
    $stmt->bind_param("si", $new_status, $request_id);
    
    if ($stmt->execute()) {
        // TRIGGER ALERTS (This handles Email, SMS, and Database Notifications)
        // Make sure this function exists in your functions.php!
        triggerStatusAlert($conn, $request_id, $new_status);
        
        header("Location: admin_dashboard.php?msg=updated");
        exit();
    }
}

// 5. Fetch Data for the table
$query = "
    SELECT 
        sr.request_id, 
        sr.service_type, 
        sr.status, 
        u.full_name,
        u.user_id,
        u.phone,
        u.email
    FROM service_requests sr
    JOIN users u ON sr.user_id = u.user_id 
    ORDER BY sr.request_id DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #0f172a; --blue: #2563eb; --bg: #f8fafc; --success: #10b981; --danger: #ef4444; --warning: #f59e0b; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; margin: 0; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--sidebar); color: white; padding: 20px; position: sticky; top: 0; height: 100vh; box-sizing: border-box; }
        .nav-link { display: block; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .nav-link.active { background: var(--blue); color: white; }
        .main { flex: 1; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #edf2f7; color: #64748b; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; }
        .status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .pending { background: #fef3c7; color: #92400e; }
        .approved { background: #dcfce7; color: #166534; }
        .rejected { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid var(--success); display: flex; align-items: center; gap: 10px; }
        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; justify-content: flex-end; z-index: 100; }
        .side-panel { width: 400px; background: white; height: 100%; padding: 30px; box-sizing: border-box; display: flex; flex-direction: column; box-shadow: -5px 0 15px rgba(0,0,0,0.1); }
        .btn-group { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }
        .btn { padding: 12px; border: none; border-radius: 8px; color: white; cursor: pointer; text-align: center; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>

<nav class="sidebar">
    <h2>KMC ADMIN</h2><br>

    <a href="user_requests.php" class="nav-link" style="color:var(--danger); margin-top: 20px;"><i class="fas fa-power-off"></i> back</a>
</nav>

<main class="main">
    <h1>Manage Requests</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert-success" id="success-alert">
            <i class="fas fa-check-circle"></i> Request updated. Email and SMS alerts have been dispatched!
        </div>
    <?php endif; ?>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>Applicant</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
			<section class="table-section">
    <h3>Pending Payment Verifications</h3>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference (Phone)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
          
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['request_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['service_type']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo strtoupper($row['status']); ?></span></td>
                    <td>
                        <button onclick='openReview(<?php echo json_encode($row); ?>)' style="cursor:pointer; border:none; background:none; color:var(--blue); font-weight:bold;">
                            <i class="fas fa-eye"></i> View & Process
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<div class="overlay" id="modal" onclick="closeModal()">
    <div class="side-panel" onclick="event.stopPropagation()">
        <h2 id="m-service">Service Name</h2>
        <p id="m-name" style="color:#64748b; margin-bottom: 5px;"></p>
        <p id="m-contact" style="font-size: 13px; color:#94a3b8;"></p>

        <div id="m-status-area" style="margin-top: 10px;"></div>

        <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
        
        <h3>Action Required</h3>
        <p style="font-size: 14px; color: #64748b;">Processing this request will notify the applicant via SMS and Email.</p>
        
        <div class="btn-group" id="m-actions">
            </div>

        <button onclick="closeModal()" style="margin-top:auto; background:none; border:1px solid #ddd; padding:10px; border-radius:8px; color:#64748b; cursor:pointer">back</button>
    </div>
</div>

<script>
function openReview(data) {
    document.getElementById('m-service').innerText = data.service_type;
    document.getElementById('m-name').innerText = "Applicant: " + data.full_name;
    document.getElementById('m-contact').innerText = data.email + " | " + data.phone;

    const statusClass = data.status.toLowerCase();
    document.getElementById('m-status-area').innerHTML = `<span class="status ${statusClass}">CURRENT STATUS: ${data.status.toUpperCase()}</span>`;

    const actions = document.getElementById('m-actions');
    actions.innerHTML = `
        <a href="?action=approve&id=${data.request_id}" class="btn" style="background:var(--success)" onclick="return confirm('Approve this request?')">
            <i class="fas fa-check"></i> Approve & Notify
        </a>
        <a href="?action=reject&id=${data.request_id}" class="btn" style="background:var(--danger)" onclick="return confirm('Reject this request?')">
            <i class="fas fa-times"></i> Reject & Notify
        </a>`;

    document.getElementById('modal').style.display = 'flex';
}

function closeModal() { document.getElementById('modal').style.display = 'none'; }

// Auto-hide alert
setTimeout(() => {
    const alert = document.getElementById('success-alert');
    if (alert) {
        alert.style.transition = "opacity 0.5s";
        alert.style.opacity = "0";
        setTimeout(() => alert.remove(), 500);
    }
}, 4000);
</script>
</body>
</html>