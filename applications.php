<?php
session_start();
require_once 'config.php'; 

// 1. Token Generation for Security
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) { 
    header("Location: log_in.php"); 
    exit(); 
}

$message = "";

// 2. Form Submission Handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['final_submit'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security violation: Invalid token.");
    }

    try {
        $pdo->beginTransaction();

        // File Upload Logic
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = basename($_FILES["nrc_doc"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = uniqid() . "." . $file_ext; 
        $target_file = $target_dir . $new_file_name;

        // Security check: Only allow certain file types
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($file_ext, $allowed)) {
            throw new Exception("Invalid file type. Only JPG, PNG, and PDF allowed.");
        }

        if (!move_uploaded_file($_FILES["nrc_doc"]["tmp_name"], $target_file)) {
            throw new Exception("Failed to upload document.");
        }

        // 3. Insert Service Request
        $stmt = $pdo->prepare("INSERT INTO service_requests (user_id, service_type, description) VALUES (?, ?, ?)");
        $desc = "NRC: " . $_POST['nrc'] . " | Location: " . $_POST['location'] . " | Phone: " . $_POST['phone'];
        $stmt->execute([
            $_SESSION['user_id'], 
            $_POST['service_type'], 
            $desc
        ]);

        $request_id = $pdo->lastInsertId();

        // 4. Document Insert (Linked to Request ID)
        if (isset($target_file)) {
            $doc_stmt = $pdo->prepare("INSERT INTO documents (user_id, request_id, file_name, file_path, doc_type, status) VALUES (?, ?, ?, ?, 'NRC', 'Pending')");
            $doc_stmt->execute([$_SESSION['user_id'], $request_id, $file_name, $target_file]);
        }
          
        $pdo->commit();
        $message = "<div class='alert success'>Application #$request_id submitted successfully!</div>";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "<div class='alert error'>Submission failed: " . $e->getMessage() . "</div>";
    }
}

// 5. Fetch Approved Permits for the user
$stmt_permits = $pdo->prepare("
    SELECT sr.service_type, d.doc_id, d.file_path, sr.created_at 
    FROM service_requests sr
    JOIN documents d ON sr.request_id = d.request_id
    WHERE sr.user_id = ? AND d.status = 'Approved'
    ORDER BY sr.created_at DESC
");
$stmt_permits->execute([$_SESSION['user_id']]);
$approved_permits = $stmt_permits->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMC - Service Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2563eb; --bg-light: #f8fafc; --white: #ffffff; --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0; --success: #10b981; --error: #ef4444; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-light); color: var(--text-main); line-height: 1.6; padding: 20px; }
        .container { max-width: 900px; margin: 20px auto; }
        
        /* Tabs and View Switching */
        .tabs { display: flex; gap: 10px; margin-bottom: 25px; }
        .tab-btn { padding: 12px 24px; background: #e2e8f0; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .tab-btn.active { background: var(--primary); color: white; }
        .view-section { display: none; }
        .view-section.active { display: block; }

        /* Form Card & Stepper */
        .stepper { display: flex; justify-content: space-between; margin-bottom: 40px; position: relative; }
        .stepper::before { content: ''; position: absolute; top: 20px; left: 0; width: 100%; height: 2px; background: var(--border); z-index: 1; }
        .step { position: relative; z-index: 2; background: var(--bg-light); padding: 0 10px; text-align: center; width: 120px; }
        .step-icon { width: 40px; height: 40px; border-radius: 50%; background: var(--white); border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; }
        .step.active .step-icon { border-color: var(--primary); color: var(--primary); }
        .step.completed .step-icon { background: var(--success); border-color: var(--success); color: white; }
        .form-card { background: var(--white); border-radius: 20px; padding: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-section { display: none; }
        .form-section.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 10px; }
        .btn-group { display: flex; justify-content: space-between; margin-top: 30px; }
        .btn { padding: 12px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; border: none; }
        .btn-next { background: var(--primary); color: white; }
        
        /* Alerts */
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Permits Table/List */
        .permit-card { background: white; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid var(--success); }
        .btn-download { background: var(--primary); color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container">
    <?php echo $message; ?>

    <div class="tabs">
        <button class="tab-btn active" onclick="showView('apply-section')">New Application</button>
        <button class="tab-btn" onclick="showView('permits-section')">My Approved Permits (<?php echo count($approved_permits); ?>)</button>
    </div>

    <div id="apply-section" class="view-section active">
        <div class="stepper">
            <div class="step active" id="s1"><div class="step-icon">1</div><div class="step-label">Details</div></div>
            <div class="step" id="s2"><div class="step-icon">2</div><div class="step-label">Documents</div></div>
            <div class="step" id="s3"><div class="step-icon">3</div><div class="step-label">Review</div></div>
        </div>

        <div class="form-card">
            <form id="appForm" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-section active" id="section1">
                    <h2>Service Details</h2>
                    <div class="grid">
                        <div class="form-group full-width">
                            <label>Service Type</label>
                            <select name="service_type" required>
                                <option value="Building Permit">Building Permit Application</option>
                                <option value="Trading License">Trading License Renewal</option>
                                <option value="Waste Collection">Waste Management Request</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>NRC Number</label>
                            <input type="text" name="nrc" id="nrc_input" placeholder="123456/11/1" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" id="phone_input" placeholder="+260..." required>
                        </div>
                        <div class="form-group full-width">
                            <label>Physical Address</label>
                            <textarea name="location" id="location_input" rows="3" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section" id="section2">
                    <h2>Document Upload</h2>
                    <div class="form-group">
                        <label>Identity Document (NRC Scan)</label>
                        <input type="file" name="nrc_doc" required>
                    </div>
                    <div class="form-group">
                        <label>TPIN (Optional)</label>
                        <input type="file" name="tpin_doc">
                    </div>
                </div>

                <div class="form-section" id="section3">
                    <h2>Final Review</h2>
                    <p>Review your information before final submission.</p>
                    <div id="reviewData"></div>
                    <input type="hidden" name="final_submit" value="1">
                </div>

                <div class="btn-group">
                    <button type="button" class="btn" id="prevBtn" onclick="move(-1)" style="display:none;">Back</button>
                    <button type="button" class="btn btn-next" id="nextBtn" onclick="move(1)">Next</button>
                </div>
            </form>
        </div>
    </div>

    <div id="permits-section" class="view-section">
        <h2>Approved Permits</h2>
        <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--border);">
        
        <?php if (count($approved_permits) > 0): ?>
            <?php foreach ($approved_permits as $permit): ?>
                <div class="permit-card">
                    <div>
                        <strong><?php echo htmlspecialchars($permit['service_type']); ?></strong>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Issued: <?php echo date('d M Y', strtotime($permit['created_at'])); ?></p>
                    </div>
                   <a href="download.php?id=<?php echo $permit['doc_id']; ?>" class="btn-download">
    <i class="fas fa-download"></i> Download Permit
</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center; padding: 40px; color: var(--text-muted);">
                <i class="fas fa-info-circle fa-2x"></i>
                <p>No approved permits found yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    let step = 1;

    // View Switching (Apply vs My Permits)
    function showView(viewId) {
        document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(viewId).classList.add('active');
        event.currentTarget.classList.add('active');
    }

    // Stepper Movement
    function move(n) {
        const currentSection = document.getElementById('section' + step);

        // Validation for "Next"
        if (n === 1) {
            const inputs = currentSection.querySelectorAll('input[required], select[required], textarea[required]');
            for (let input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return;
                }
            }
        }

        // Generate Review Data (When entering Step 3)
        if (n === 1 && step === 2) {
            const service = document.querySelector('select[name="service_type"]').value;
            const nrc = document.getElementById('nrc_input').value;
            const phone = document.getElementById('phone_input').value;
            const loc = document.getElementById('location_input').value;
            
            document.getElementById('reviewData').innerHTML = `
                <div style="padding: 15px; border-radius: 8px; background: #fff; border: 1px solid var(--border);">
                    <p><strong>Service:</strong> ${service}</p>
                    <p><strong>NRC:</strong> ${nrc}</p>
                    <p><strong>Phone:</strong> ${phone}</p>
                    <p><strong>Address:</strong> ${loc}</p>
                    <p style="color:var(--success); margin-top:10px;"><i class="fas fa-check-circle"></i> Document ready for upload.</p>
                </div>
            `;
        }

        // Final Submit
        if (n === 1 && step === 3) {
            document.getElementById('appForm').submit();
            return;
        }

        // UI Updates
        document.getElementById('section' + step).classList.remove('active');
        document.getElementById('s' + step).classList.remove('active');
        if(n > 0) document.getElementById('s' + step).classList.add('completed');

        step += n;

        document.getElementById('section' + step).classList.add('active');
        document.getElementById('s' + step).classList.add('active');

        // Toggle Buttons
        document.getElementById('prevBtn').style.display = (step === 1) ? "none" : "inline-block";
        document.getElementById('nextBtn').innerHTML = (step === 3) ? "Confirm & Submit" : "Next";
    }
</script>
</body>
</html>