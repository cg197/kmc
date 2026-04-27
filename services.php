<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $req_id = intval($_POST['request_id']);
    $status = $_POST['new_status'];
    $note = $_POST['admin_note'];

    // Update the request
    $stmt = $conn->prepare("UPDATE service_requests SET status = ?, admin_notes = ? WHERE request_id = ?");
    $stmt->bind_param("ssi", $status, $note, $req_id);
    if ($stmt->execute()) {
        //  Fetch User for Notification
        $user_sql = "SELECT u.email, u.phone, u.full_name, sr.service_type
                     FROM users u JOIN service_requests sr ON u.user_id = sr.user_id 
                     WHERE sr.request_id = $req_id";
        $user = $conn->query($user_sql)->fetch_assoc();

        //  Trigger Email/SMS
        $msg = "KMC Update: Your application for " . $user['service_type'] . " has been " . strtoupper($status);
        sendCouncilEmail($user['email'], "Status Update", $msg);
        sendCouncilSMS($user['phone'], $msg);
    }
    header("Location: service_management.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC Admin - Service Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-dark: #0f172a;
            --council-blue: #2563eb;
            --bg-light: #f3f4f6;
            --danger: #ef4444;
            --white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; color: #1e293b; }

        /* Sidebar */
        .admin-sidebar { width: 260px; background: var(--sidebar-dark); color: white; padding: 25px 20px; flex-shrink: 0; position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; }
        .brand { margin-bottom: 35px; font-size: 1.4rem; font-weight: 800; color: var(--council-blue); display: flex; align-items: center; gap: 10px; }
        .nav { list-style: none; flex-grow: 1; }
        .nav li { padding: 12px 15px; margin-bottom: 8px; border-radius: 8px; cursor: pointer; color: #94a3b8; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        .nav li.active { background: var(--council-blue); color: white; }

        /* Main Content */
        .admin-main { flex: 1; padding: 30px; overflow-y: auto; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .search-bar { background: white; padding: 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; width: 350px; display: flex; align-items: center; gap: 10px; }
        .search-bar input { border: none; outline: none; width: 100%; font-size: 0.9rem; }

        /* Table Design */
        .panel { background: var(--white); padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        /* Status Badges */
        .status { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .status.pending { background: #fef9c3; color: #854d0e; }
        .status.approved { background: #dcfce7; color: #166534; }
        .status.rejected { background: #fee2e2; color: #991b1b; }

        .btn-manage { padding: 6px 12px; background: #f1f5f9; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; }
        .btn-manage:hover { background: var(--council-blue); color: white; }

        /* Slide over Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); display: none; z-index: 1000; justify-content: flex-end; }
        .side-panel { width: 450px; height: 100%; background: white; box-shadow: -5px 0 25px rgba(0,0,0,0.1); display: flex; flex-direction: column; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

        .panel-header { padding: 30px; border-bottom: 1px solid var(--bg-light); display: flex; justify-content: space-between; align-items: center; }
        .panel-body { padding: 30px; flex: 1; overflow-y: auto; }
        .info-card { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .action-group { padding: 20px 30px; background: #f8fafc; display: flex; gap: 10px; }
        .btn-act { flex: 1; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>

<aside class="admin-sidebar">
    <div class="brand"><i class="fas fa-city"></i> KMC ADMIN</div>
    <ul class="nav">
        <li><i class="fas fa-th-large"></i> Dashboard</li>
        <li class="active"><i class="fas fa-layer-group"></i> Service Requests</li>
        <li onclick="location.href='admin_dashboard.php'"><i class="fas fa-users-cog"></i> back</li>
    </ul>
</aside>

<div class="admin-main">
    <div class="header-actions">
        <h1>Service Management</h1>
        <div class="search-bar">
            <i class="fas fa-search" style="color: #cbd5e1;"></i>
            <input type="text" placeholder="Search by name or reference..." id="reqSearch">
        </div>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Applicant</th>
                    <th>Service Type</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="requestTable">
                <tr>
                    <td>#KMC-001</td>
                    <td><strong>Mulenga Kapwepwe</strong></td>
                    <td>Land Title Deed</td>
                    <td>Feb 24, 2026</td>
                    <td><span class="status pending">Pending Review</span></td>
                    <td><button class="btn-manage" onclick="openPanel('Mulenga Kapwepwe', 'Land Title Deed', '#KMC-001')">Review</button></td>
                </tr>
                <tr>
                    <td>#KMC-002</td>
                    <td><strong>John Zulu</strong></td>
                    <td>Waste Management</td>
                    <td>Feb 25, 2026</td>
                    <td><span class="status approved">Approved</span></td>
                    <td><button class="btn-manage" onclick="openPanel('John Zulu', 'Waste Management', '#KMC-002')">Review</button></td>
                </tr>
            </tbody>
        </table>
    </section>
</div>

<div class="modal-overlay" id="managementPanel">
    <div class="side-panel">
        <div class="panel-header">
            <h3>Request Management</h3>
            <button onclick="closePanel()" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>
        <div class="panel-body">
            <div class="info-card">
                <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 5px;">APPLICANT</p>
                <h2 id="modalName">Name</h2>
                <p id="modalRef" style="color: var(--council-blue); font-weight: 600;">#REF</p>
            </div>

            <div class="info-card">
                <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 5px;">SERVICE TYPE</p>
                <p id="modalService">Service Type</p>
            </div>


            <label style="display:block; margin-bottom: 8px; font-weight: 700; font-size: 0.85rem;">Assign Officer</label>
            <select style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px;">
                <option>Select Officer...</option>
                <option>Officer Mutale</option>
                <option>Officer Banda</option>
            </select>

            <label style="display:block; margin-bottom: 8px; font-weight: 700; font-size: 0.85rem;">Review Note</label>
            <textarea style="width:100%; height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" placeholder="Add a private note regarding this application..."></textarea>
        </div>
        <div class="action-group">
            <button class="btn-act" style="background: var(--success); color: white;" onclick="updateStatus('Approved')">Approve</button>
            <button class="btn-act" style="background: var(--danger); color: white;" onclick="updateStatus('Rejected')">Reject</button>
        </div>
    </div>
</div>

<script>
    function openPanel(name, service, ref) {
        document.getElementById('modalName').innerText = name;
        document.getElementById('modalService').innerText = service;
        document.getElementById('modalRef').innerText = ref;
        document.getElementById('managementPanel').style.display = 'flex';
    }

    function closePanel() {
        document.getElementById('managementPanel').style.display = 'none';
    }

    function updateStatus(status) {
        alert(`Request has been marked as: ${status}`);
        closePanel();
    }

    // Filter Logic
    document.getElementById('reqSearch').addEventListener('input', function(e) {
        let term = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('#requestTable tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
        });
    });
</script>

</body>
</html>