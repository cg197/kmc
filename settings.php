<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC - Account Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --success: #10b981;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-light); color: var(--text-main); padding-bottom: 50px; }

        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }

        /* --- Header --- */
        .settings-header { margin-bottom: 2rem; }
        .settings-header h1 { font-size: 2rem; font-weight: 800; color: #0f172a; }
        .settings-header p { color: var(--text-muted); }

        /* --- Settings Card --- */
        .settings-section {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .section-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-header i { color: var(--primary); font-size: 1.2rem; }
        .section-header h2 { font-size: 1.1rem; font-weight: 700; }

        .section-body { padding: 2rem; }

        /* --- Input Styles --- */
        .setting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .setting-row:last-child { border-bottom: none; }

        .setting-info h4 { font-size: 0.95rem; font-weight: 600; margin-bottom: 4px; }
        .setting-info p { font-size: 0.85rem; color: var(--text-muted); }

        /* --- Toggle Switch --- */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1; transition: .4s; border-radius: 24px;
        }
        .slider:before {
            position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
            background-color: white; transition: .4s; border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--primary); }
        input:checked + .slider:before { transform: translateX(20px); }

        /* --- Action Buttons --- */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid var(--border);
            background: var(--white);
        }
        .btn-primary { background: var(--primary); color: white; border: none; }
        .btn-danger { color: var(--danger); border-color: #fecaca; }
        .btn-danger:hover { background: #fef2f2; }

        /* --- Security Badge --- */
        .security-badge {
            background: #dcfce7;
            color: #166534;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="settings-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Settings</h1>
            <div class="security-badge">
                <i class="fas fa-shield-alt"></i> Account Secure
            </div>
        </div>
        <p>Manage your account preferences and security protocols.</p>
    </div>

    <div class="settings-section">
        <div class="section-header">
            <i class="fas fa-lock"></i>
            <h2>Security & Privacy</h2>
        </div>
        <div class="section-body">
            <div class="setting-row">
                <div class="setting-info">
                    <h4>Two-Factor Authentication</h4>
                    <p>Add an extra layer of security to your KMC account.</p>
                </div>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="setting-row">
                <div class="setting-info">
                    <h4>Password</h4>
                    <p>Last changed 3 months ago.</p>
                </div>
                <button class="btn">Update Password</button>
            </div>
            <div class="setting-row">
                <div class="setting-info">
                    <h4>Active Sessions</h4>
                    <p>Logged in on: Windows PC (Lusaka).</p>
                </div>
                <button class="btn">Log out all devices</button>
            </div>
        </div>
    </div>

    <div class="settings-section">
        <div class="section-header">
            <i class="fas fa-bell"></i>
            <h2>Communication</h2>
        </div>
        <div class="section-body">
            <div class="setting-row">
                <div class="setting-info">
                    <h4>Email Notifications</h4>
                    <p>Receive updates on application status via email.</p>
                </div>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="setting-row">
                <div class="setting-info">
                    <h4>SMS Alerts</h4>
                    <p>Receive urgent payment reminders via SMS.</p>
                </div>
                <label class="switch">
                    <input type="checkbox">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="settings-section">
        <div class="section-header">
            <i class="fas fa-globe"></i>
            <h2>Regional & Language</h2>
        </div>
        <div class="section-body">
            <div class="setting-row">
                <div class="setting-info">
                    <h4>Language</h4>
                    <p>English (UK)</p>
                </div>
                <button class="btn">Change</button>
            </div>
            <div class="setting-row">
                <div class="setting-info">
                    <h4>Timezone</h4>
                    <p>(GMT+02:00) Central Africa Time</p>
                </div>
                <button class="btn">Change</button>
            </div>
        </div>
    </div>

    <div class="settings-section" style="border-color: #fecaca;">
        <div class="section-header" style="background: #fff5f5; border-bottom-color: #fecaca;">
            <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
            <h2 style="color: var(--danger);">Danger Zone</h2>
        </div>
        <div class="section-body">
            <div class="setting-row">
                <div class="setting-info">
                    <h4>Deactivate Account</h4>
                    <p>Temporarily disable your access to the KMC Portal.</p>
                </div>
                <button class="btn btn-danger">Deactivate</button>
                 <a href="user_dashboard.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> back</a>
            </div>
        </div>
    </div>
</div>

<script>
    // logic for toggles
    document.querySelectorAll('input[type="checkbox"]').forEach(toggle => {
        toggle.addEventListener('change', function() {
            console.log("Setting changed: " + this.checked);
        });
    });
</script>

</body>
</html>