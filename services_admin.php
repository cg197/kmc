<?php

// 1. Link to functions file
require_once 'functions.php'; 

// Database Connection
$conn = new mysqli("localhost", "choolweg_kabwe_council_db", "gambwe1997", "choolweg_kabwe_council_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// --- LOGIC HANDLING ---

// 1. Add New Service
if (isset($_POST['add_service'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['desc']);
    $fee = floatval($_POST['fee']);
    
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO services (service_name, description, fee, is_active) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("ssd", $name, $desc, $fee);
        $stmt->execute();
        $stmt->close();
        header("Location: services_admin.php?msg=added");
        exit();
    }
}

// 2. Update Existing Service
if (isset($_POST['update_service'])) {
    $id = intval($_POST['edit_id']);
    $name = trim($_POST['edit_name']);
    $fee = floatval($_POST['edit_fee']);
    
    $stmt = $conn->prepare("UPDATE services SET service_name = ?, fee = ? WHERE service_id = ?");
    $stmt->bind_param("sdi", $name, $fee, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: services_admin.php?msg=updated");
    exit();
}

// 3. Toggle Availability
if (isset($_GET['toggle_id'])) {
    $id = intval($_GET['toggle_id']);
    $conn->query("UPDATE services SET is_active = 1 - is_active WHERE service_id = $id");
    header("Location: services_admin.php");
    exit();
}

// 4. Delete Service
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: services_admin.php?msg=deleted");
    exit();
}

// 5. Fetch Services (Do this AFTER all logic so data is fresh)
$services = $conn->query("SELECT * FROM services ORDER BY service_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services | KMC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #0f172a; --blue: #2563eb; --bg: #f8fafc; --success: #10b981; --danger: #ef4444; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 260px; background: var(--sidebar); color: white; height: 100vh; padding: 20px; position: sticky; top: 0; }
        .main { flex: 1; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #edf2f7; color: #64748b; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; }
        .form-group { margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-add { background: var(--blue); color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .active { background: #dcfce7; color: #166534; }
        .inactive { background: #fee2e2; color: #991b1b; }
        .alert { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; }
        .action-links a { margin-right: 15px; text-decoration: none; cursor: pointer; }
    </style>
</head>
<body>

<nav class="sidebar">
    <h2>KMC ADMIN</h2><br>
    <a href="admin_dashboard.php" style="color:#94a3b8; text-decoration:none;">Dashboard</a><br><br>
    <a href="services_admin.php" style="color:white; text-decoration:none; font-weight:bold;">Manage Services</a>
</nav>

<main class="main">
    <h1>Service Catalog</h1>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert">
            <i class="fas fa-check-circle"></i> 
            <?php 
                if($_GET['msg'] == 'added') echo "Service created successfully!";
                if($_GET['msg'] == 'deleted') echo "Service has been removed.";
                if($_GET['msg'] == 'updated') echo "Service updated successfully!";
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3><i class="fas fa-plus-circle"></i> Add New Service</h3>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="name" placeholder="Service Name (e.g. Business Permit)" required>
            </div>
            <div class="form-group">
                <textarea name="desc" placeholder="Brief Description" rows="2"></textarea>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" name="fee" placeholder="Fee (K)" required>
            </div>
            <button type="submit" name="add_service" class="btn-add">Create Service</button>
        </form>
    </div>

    <div class="card">
        <h3>Existing Services</h3>
        <table>
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Fee (ZMW)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($s = $services->fetch_assoc()): 
                    $rowId = $s['service_id'] ?? $s['id'] ?? 0;
                    $isActive = $s['is_active'] ?? 0;
                ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($s['service_name']); ?></strong><br>
                        <small style="color: #64748b;"><?php echo htmlspecialchars($s['description']); ?></small>
                    </td>
                    <td>K <?php echo number_format($s['fee'], 2); ?></td>
                    <td>
                        <span class="badge <?php echo $isActive ? 'active' : 'inactive'; ?>">
                            <?php echo $isActive ? 'ACTIVE' : 'DISABLED'; ?>
                        </span>
                    </td>
                    <td class="action-links">
                        <a onclick="editService(<?php echo $rowId; ?>, '<?php echo addslashes($s['service_name']); ?>', <?php echo $s['fee']; ?>)" 
                           style="color: orange;" title="Edit Service">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="?toggle_id=<?php echo $rowId; ?>" title="Toggle Status" style="color: var(--blue);">
                            <i class="fas fa-sync-alt"></i>
                        </a>

                        <a href="?delete_id=<?php echo $rowId; ?>" 
                           style="color: var(--danger);" 
                           onclick="return confirm('Permanently delete this service?');"
                           title="Delete Service">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function editService(id, name, fee) {
    const newName = prompt("Enter new Service Name:", name);
    const newFee = prompt("Enter new Fee (K):", fee);
    
    if (newName && newFee) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="edit_id" value="${id}">
            <input type="hidden" name="edit_name" value="${newName}">
            <input type="hidden" name="edit_fee" value="${newFee}">
            <input type="hidden" name="update_service" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

</body>
</html>