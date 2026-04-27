<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC - Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --sidebar-bg: #0f172a;
            --bg-light: #f1f5f9;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-main); }

        /* --- Sidebar --- */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; padding: 2rem 1.5rem; position: sticky; top: 0; height: 100vh; }
        .logo-area { display: flex; align-items: center; gap: 12px; margin-bottom: 3rem; }
        .logo-icon { width: 32px; height: 32px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 0.5rem; transition: 0.2s; }
        .nav-item:hover, .nav-item.active { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-item.active { background: var(--primary); }

        /* --- Main Content --- */
        .content { flex: 1; padding: 2rem 3rem; overflow-y: auto; }

        .profile-header {
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }

        .avatar-box {
            position: relative;
            width: 80px;
            height: 80px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
        }

        .edit-avatar {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--white);
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            border: 1px solid var(--border);
            cursor: pointer;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
        }

        /* --- Tabs --- */
        .settings-tabs { display: flex; flex-direction: column; gap: 10px; }
        .tab-btn {
            padding: 12px 15px;
            background: none;
            border: none;
            text-align: left;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 8px;
            transition: 0.2s;
        }
        .tab-btn.active { background: var(--white); color: var(--primary); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* --- Form Cards --- */
        .settings-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            font-size: 0.95rem;
        }
        input:focus { border-color: var(--primary); }

        .btn-save {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .danger-zone {
            margin-top: 2rem;
            border-top: 1px solid var(--border);
            padding-top: 2rem;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-landmark"></i></div>
            <h2 style="font-size: 1.1rem;">KMC PORTAL</h2>
        </div>
        <nav>
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="applications.php" class="nav-item"><i class="fas fa-file-invoice"></i> Applications</a>
            <a href="payments.php" class="nav-item"><i class="fas fa-wallet"></i> Payments</a>
            <a href="notifications.php" class="nav-item"><i class="fas fa-bell"></i> Notifications</a>
            <a href="#" class="nav-item active"><i class="fas fa-user-cog"></i> Settings</a>
        </nav>
    </aside>

    <main class="content">
        <header class="profile-header">
            <div class="avatar-box">
                JD
                <div class="edit-avatar"><i class="fas fa-camera"></i></div>
            </div>
            <div>
                <h1 style="font-size: 1.5rem;">John Doe</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Resident ID: #KMC-8829</p>
            </div>
        </header>

        <div class="settings-grid">
            <div class="settings-tabs">
                <button class="tab-btn active"><i class="fas fa-user"></i> Profile Info</button>
                <button class="tab-btn"><i class="fas fa-lock"></i> Security</button>
                <button class="tab-btn"><i class="fas fa-bell"></i> Email Preferences</button>
            </div>

            <div class="settings-card">
                <h2 style="margin-bottom: 2rem; font-size: 1.2rem;">Account Information</h2>
                <div class="form-row">
                    <div>
                        <label>First Name</label>
                        <input type="text" value="John">
                    </div>
                    <div>
                        <label>Last Name</label>
                        <input type="text" value="Doe">
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label>Email Address</label>
                        <input type="email" value="john.doe@example.com">
                    </div>
                    <div>
                        <label>Phone Number</label>
                        <input type="tel" value="+260 977 123456">
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label>Physical Address</label>
                    <input type="text" value="Plot 402, Bwacha, Kabwe">
                </div>

                <button class="btn-save" onclick="saveSettings()">Save Changes</button>

                <div class="danger-zone">
                    <h3 style="color: var(--danger); font-size: 1rem; margin-bottom: 10px;">Danger Zone</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px;">Once you delete your portal account, there is no going back. Please be certain.</p>
                    <button style="color: var(--danger); background: none; border: 1px solid var(--danger); padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 600;">Delete Account</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function saveSettings() {
            const btn = document.querySelector('.btn-save');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            setTimeout(() => {
                btn.innerHTML = 'Saved!';
                btn.style.background = '#10b981';
                setTimeout(() => {
                    btn.innerHTML = 'Save Changes';
                    btn.style.background = '#2563eb';
                }, 2000);
            }, 1000);
        }
    </script>
</body>
</html>