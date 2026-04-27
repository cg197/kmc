<?php
session_start();
require_once 'config.php'; 

// Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) { 
    header("Location: log_in.php"); 
    exit(); 
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['final_submit'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security violation: Invalid token.");
    }

    try {
        $pdo->beginTransaction();
// 1. Insert Service Request
// Note: We do NOT include request_id here. The database creates it.
$stmt = $pdo->prepare("INSERT INTO service_requests (user_id, service_type, description) VALUES (?, ?, ?)");

// Prepare the description string from the form inputs
$desc = "NRC: " . $_POST['nrc'] . " | Location: " . $_POST['location'] . " | Phone: " . $_POST['phone'];

// This array MUST have exactly 3 items to match the 3 '?' above.
$stmt->execute([
    $_SESSION['user_id'],    // Matches 1st '?'
    $_POST['service_type'], // Matches 2nd '?'
    $desc                   // Matches 3rd '?'
]);

// 2. Get the generated ID
$request_id = $pdo->lastInsertId();

// 3. Document Insert (Make sure the 'documents' table also has a 'request_id' column!)
if (isset($target_file)) {
    $doc_stmt = $pdo->prepare("INSERT INTO documents (user_id, request_id, file_name, file_path, doc_type, status) VALUES (?, ?, ?, ?, 'NRC', 'Pending')");
    $doc_stmt->execute([$_SESSION['user_id'], $request_id, $file_name, $target_file]);
}
      
        $pdo->commit();
        header("Location: user_dashboard.php?success=1");
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "Submission failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMC - New Application</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2563eb; --bg-light: #f8fafc; --white: #ffffff; --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0; --success: #10b981; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-light); color: var(--text-main); line-height: 1.6; padding: 20px; }
        .container { max-width: 800px; margin: 40px auto; }
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
        .error-msg { color: red; margin-bottom: 10px; font-weight: bold; }
        #reviewData { background: #f1f5f9; padding: 20px; border-radius: 12px; margin-top: 15px; border-left: 4px solid var(--primary); }
    </style>
</head>
<body>

<div class="container">
    <?php if($message): ?> <p class="error-msg"><?php echo $message; ?></p> <?php endif; ?>

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
                        <input type="tel" name="phone" placeholder="+260..." required>
                    </div>
                    <div class="form-group full-width">
                        <label>Physical Address</label>
                        <textarea name="location" rows="3" required></textarea>
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
                <p>Please confirm all details are correct before submitting to the Kabwe Municipal Council.</p>
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

<script>
    let step = 1;

    function move(n) {
        const currentSection = document.getElementById('section' + step);

        // Validation for "Next" button
        if (n === 1) {
            const inputs = currentSection.querySelectorAll('input[required], select[required], textarea[required]');
            for (let input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return;
                }
            }
        }

        // Generate Review Data when moving to Step 3
        if (n === 1 && step === 2) {
            const service = document.querySelector('select[name="service_type"]').value;
            const nrc = document.getElementById('nrc_input').value;
            document.getElementById('reviewData').innerHTML = `
                <p><strong>Selected Service:</strong> ${service}</p>
                <p><strong>NRC Number:</strong> ${nrc}</p>
                <p style="color:var(--success); margin-top:10px;"><i class="fas fa-check-circle"></i> Document attached ready for upload.</p>
            `;
        }

        // Final Submit Logic
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