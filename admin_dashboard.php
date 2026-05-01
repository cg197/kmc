<?php
require_once 'functions.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// 2. Database Connection
$host = "localhost"; $db_user = "choolweg_kabwe_council_db"; $db_pass = "gambwe1997"; $db_name = "choolweg_kabwe_council_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 3. HANDLE ACTIONS
// --- Handle Service Request Approval/Rejection ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE service_requests SET status = ? WHERE request_id = ?");
    $stmt->bind_param("si", $new_status, $request_id);
    
    if ($stmt->execute()) {
        if (function_exists('triggerStatusAlert')) {
            triggerStatusAlert($conn, $request_id, $new_status);
        }
        header("Location: admin_dashboard.php?msg=updated");
        exit();
    }
}

// --- Handle Payment Verification (The critical bridge) ---
if (isset($_GET['pay_action']) && isset($_GET['pay_id']) && isset($_GET['req_id'])) {
    $pay_id = intval($_GET['pay_id']);
    $req_id = intval($_GET['req_id']);
    
    if ($_GET['pay_action'] === 'verify') {
        $conn->begin_transaction();
        try {
            // Update Payment record
            $conn->query("UPDATE payments SET status = 'success' WHERE payment_id = $pay_id");
            // Update Service Request
            $conn->query("UPDATE service_requests SET status = 'paid' WHERE request_id = $req_id");
            // Approve Document so download works
            $conn->query("UPDATE documents SET status = 'Approved' WHERE request_id = $req_id");
            
            $conn->commit();
            header("Location: admin_dashboard.php?msg=paid");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            die("Payment verification failed: " . $e->getMessage());
        }
    }
}

// 4. FETCH DATA
$requests_result = $conn->query("SELECT sr.*, u.full_name, u.phone, u.email FROM service_requests sr JOIN users u ON sr.user_id = u.user_id ORDER BY sr.request_id DESC");
$payments_result = $conn->query("SELECT p.*, u.full_name FROM payments p JOIN users u ON p.user_id = u.user_id WHERE p.status = 'pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMC Admin Portal - Master Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #0f172a; --blue: #2563eb; --bg: #f8fafc; --success: #10b981; --danger: #ef4444; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; margin: 0; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar { width: 260px; background: var(--sidebar); color: white; padding: 20px; position: sticky; top: 0; height: 100vh; box-sizing: border-box; }
        .nav-link { display: block; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 5px; font-size: 14px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: var(--blue); color: white; }
        
        .main { flex: 1; padding: 40px; overflow-y: auto; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #edf2f7; color: #64748b; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        
        .status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .pending { background: #fef3c7; color: #92400e; }
        .approved, .paid, .success { background: #dcfce7; color: #166534; }
        .rejected { background: #fee2e2; color: #991b1b; }
        
        .btn-verify { background: var(--success); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 12px; font-weight: bold; }
        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; justify-content: flex-end; z-index: 100; }
        .side-panel { width: 400px; background: white; height: 100%; padding: 30px; display: flex; flex-direction: column; box-shadow: -5px 0 15px rgba(0,0,0,0.1); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid var(--success); background: #dcfce7; color: #166534; }
    </style>
</head>
<body>

<nav class="sidebar">
    <h2>KMC ADMIN</h2><br>
    <a href="admin_dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="services_admin.php" class="nav-link"><i class="fas fa-cogs"></i> Services</a>
    <a href="admin_reports.php" class="nav-link"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="user_managements.php" class="nav-link"><i class="fas fa-users"></i> User Management</a>
    <a href="admin_settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
    <a href="logout.php" class="nav-link" style="color:var(--danger); margin-top: 40px;"><i class="fas fa-power-off"></i> Logout</a>
</nav>

<main class="main">
    <h1>Dashboard Overview</h1>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert" id="success-alert">
            <i class="fas fa-check-circle"></i> 
            <?php echo ($_GET['msg'] == 'updated') ? "Request status updated and user notified." : "Payment verified and permit unlocked."; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3><i class="fas fa-money-bill-wave"></i> Pending Payment Verifications</h3>
        <table>
            <thead>
                <tr>
                    <th>User</th><th>Amount</th><th>Method</th><th>Reference</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($payments_result->num_rows > 0): ?>
                    <?php while($p = $payments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                        <td>K<?php echo number_format($p['amount'], 2); ?></td>
                        <td><?php echo $p['method']; ?></td>
                        <td><strong><?php echo htmlspecialchars($p['reference']); ?></strong></td>
                        <td>
                            <a href="?pay_action=verify&pay_id=<?php echo $p['payment_id']; ?>&req_id=<?php echo $p['request_id']; ?>" 
                               class="btn-verify" onclick="return confirm('Verify this payment?')">Confirm Receipt</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; color:#94a3b8;">No payments awaiting verification.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3><i class="fas fa-file-alt"></i> Recent Service Applications</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Service</th><th>Applicant</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $requests_result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['request_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['service_type']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo strtoupper($row['status']); ?></span></td>
                    <td>
                        <button onclick='openReview(<?php echo json_encode($row); ?>)' style="cursor:pointer; border:none; background:none; color:var(--blue); font-weight:bold;">
                            <i class="fas fa-eye"></i> Review
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
        <h2 id="m-service">Service Details</h2>
        <p id="m-name" style="font-weight:bold; color:var(--sidebar);"></p>
        <p id="m-contact" style="font-size: 13px; color:#64748b;"></p>
        <div id="m-status-area" style="margin: 15px 0;"></div>

        <hr style="border:0; border-top:1px solid #eee; margin: 20px 0;">
        
        <div id="m-actions" style="display:flex; flex-direction:column; gap:10px;">
            </div>

        <button onclick="closeModal()" style="margin-top:auto; padding:12px; border-radius:8px; border:1px solid #ddd; background:none; cursor:pointer;">Close Panel</button>
    </div>
</div>

<script>
function openReview(data) {
    document.getElementById('m-service').innerText = data.service_type;
    document.getElementById('m-name').innerText = data.full_name;
    document.getElementById('m-contact').innerText = data.email + " | " + data.phone;
    
    const statusClass = data.status.toLowerCase();
    document.getElementById('m-status-area').innerHTML = `<span class="status ${statusClass}">Status: ${data.status.toUpperCase()}</span>`;

    const actions = document.getElementById('m-actions');
    actions.innerHTML = `
        <a href="?action=approve&id=${data.request_id}" class="btn-verify" style="background:var(--success); text-align:center; padding:15px;" onclick="return confirm('Approve application?')">Approve & Notify</a>
        <a href="?action=reject&id=${data.request_id}" class="btn-verify" style="background:var(--danger); text-align:center; padding:15px;" onclick="return confirm('Reject application?')">Reject & Notify</a>
    `;

    document.getElementById('modal').style.display = 'flex';
}

function closeModal() { document.getElementById('modal').style.display = 'none'; }

// Auto-hide alerts
setTimeout(() => {
    const alert = document.getElementById('success-alert');
    if (alert) {
        alert.style.transition = "opacity 0.5s";
        alert.style.opacity = "0";
        setTimeout(() => alert.remove(), 500);
    }
}, 3000);
</script>

</body>
</html>