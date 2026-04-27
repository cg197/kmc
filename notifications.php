<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC - Notifications</title>
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
            --unread-bg: #eff6ff;
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
        .content { flex: 1; display: flex; flex-direction: column; height: 100vh; }
        .content-header { padding: 2rem 3rem; background: var(--bg-light); }
        .content-header h1 { font-size: 1.8rem; font-weight: 800; }

        .notification-container {
            display: grid;
            grid-template-columns: 400px 1fr;
            flex: 1;
            background: var(--white);
            margin: 0 3rem 2rem 3rem;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }

        /* --- Notification List --- */
        .notif-list {
            border-right: 1px solid var(--border);
            overflow-y: auto;
        }

        .list-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notif-item {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: 0.2s;
            position: relative;
        }

        .notif-item:hover { background: #f8fafc; }
        .notif-item.unread { background: var(--unread-bg); }
        .notif-item.active { background: #f1f5f9; border-left: 4px solid var(--primary); }

        .notif-item .unread-dot {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
        }

        .notif-meta { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.75rem; color: var(--text-muted); }
        .notif-title { font-weight: 700; font-size: 0.95rem; margin-bottom: 4px; }
        .notif-preview { font-size: 0.85rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* --- Detail View --- */
        .notif-detail { padding: 3rem; overflow-y: auto; background: var(--white); }
        .detail-placeholder { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); }
        
        .detail-header { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border); }
        .detail-body { font-size: 1rem; color: #334155; line-height: 1.8; }

        .tag { padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .tag-status { background: #dcfce7; color: #16a34a; }
        .tag-payment { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-landmark"></i></div>
            <h2 style="font-size: 1.1rem;">KMC PORTAL</h2>
        </div>
        <nav>
            <a href="user_dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="applications.php" class="nav-item"><i class="fas fa-file-invoice"></i> Applications</a>
            <a href="payments.php" class="nav-item"><i class="fas fa-wallet"></i> Payments</a>
            <a href="#" class="nav-item active"><i class="fas fa-bell"></i> Notifications</a>
            <a href="user_dashboard.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> back</a>
        </nav>
    </aside>

    <main class="content">
        <header class="content-header">
            <h1>Notifications</h1>
        </header>

        <div class="notification-container">
            <div class="notif-list">
                <div class="list-header">
                    <span style="font-weight: 700;">Inbox</span>
                    <button style="border: none; background: none; color: var(--primary); font-size: 0.8rem; cursor: pointer;">Mark all as read</button>
                </div>

                <div class="notif-item unread active" onclick="showNotif(1)">
                    <div class="unread-dot"></div>
                    <div class="notif-meta">
                        <span class="tag tag-status">Update</span>
                        <span>10:30 AM</span>
                    </div>
                    <div class="notif-title">Application Approved</div>
                    <div class="notif-preview">Your building permit for Plot 402 has been successfully...</div>
                </div>

                <div class="notif-item unread" onclick="showNotif(2)">
                    <div class="unread-dot"></div>
                    <div class="notif-meta">
                        <span class="tag tag-payment">Payment</span>
                        <span>Yesterday</span>
                    </div>
                    <div class="notif-title">Invoice Overdue</div>
                    <div class="notif-preview">The payment for Q2 Property Rates is now 3 days overdue...</div>
                </div>

                <div class="notif-item" onclick="showNotif(3)">
                    <div class="notif-meta">
                        <span class="tag" style="background: #f1f5f9; color: #475569;">System</span>
                        <span>May 24</span>
                    </div>
                    <div class="notif-title">Scheduled Maintenance</div>
                    <div class="notif-preview">KMC Portal will be offline for 2 hours this Sunday...</div>
                </div>
            </div>

            <div class="notif-detail" id="detailView">
                <div class="detail-header">
                    <h2 id="detailTitle" style="font-size: 1.5rem; margin-bottom: 0.5rem;">Application Approved</h2>
                    <p id="detailDate" style="color: var(--text-muted); font-size: 0.85rem;">Thursday, Feb 26, 2026 • 10:30 AM</p>
                </div>
                <div class="detail-body" id="detailContent">
                    Dear Resident,<br><br>
                    We are pleased to inform you that your application for a <strong>Building Permit (Ref: #KMC-00123)</strong> has been approved by the planning department.<br><br>
                    You can now download your digital certificate from the 'Applications' tab or visit the Civic Center to collect your printed permit. Please ensure your construction stays within the approved structural guidelines.<br><br>
                    Best Regards,<br>
                    <strong>Kabwe Municipal Council</strong>
                </div>
            </div>
        </div>
    </main>

    <script>
        const notifications = {
            1: {
                title: "Application Approved",
                date: "Thursday, Feb 26, 2026 • 10:30 AM",
                body: "Dear Resident,<br><br>We are pleased to inform you that your application for a <strong>Building Permit (Ref: #KMC-00123)</strong> has been approved by the planning department.<br><br>You can now download your digital certificate from the 'Applications' tab or visit the Civic Center to collect your printed permit.<br><br>Regards,<br>KMC Planning Dept."
            },
            2: {
                title: "Invoice Overdue",
                date: "Wednesday, Feb 25, 2026 • 09:15 AM",
                body: "Our records indicate that <strong>Invoice #INV-9902</strong> for Property Rates remains unpaid.<br><br>Please settle this balance through the 'Payments' section of the portal to avoid late penalty fees. If you have already made payment, please ignore this notice.<br><br>Thank you for your cooperation."
            },
            3: {
                title: "Scheduled Maintenance",
                date: "Sunday, May 24, 2026 • 02:00 PM",
                body: "The KMC Portal will undergo routine maintenance this Sunday between 12:00 AM and 02:00 AM CAT.<br><br>During this time, payment services and application status checks may be unavailable. We apologize for any inconvenience caused."
            }
        };

        function showNotif(id) {
            const data = notifications[id];
            document.getElementById('detailTitle').innerText = data.title;
            document.getElementById('detailDate').innerText = data.date;
            document.getElementById('detailContent').innerHTML = data.body;

            document.querySelectorAll('.notif-item').forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');
            event.currentTarget.classList.remove('unread');
        }
    </script>
</body>
</html>