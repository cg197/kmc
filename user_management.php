<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC Admin - User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-dark: #0f172a;
            --council-blue: #2563eb;
            --bg-light: #f3f4f6;
            --danger: #ef4444;
            --white: #ffffff;
            --success: #10b981;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; color: #1e293b; }


        .admin-sidebar { width: 260px; background: var(--sidebar-dark); color: white; padding: 25px 20px; flex-shrink: 0; position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; }
        .brand { margin-bottom: 35px; font-size: 1.4rem; font-weight: 800; color: var(--council-blue); display: flex; align-items: center; gap: 10px; }
        .nav { list-style: none; flex-grow: 1; }
        .nav li { padding: 12px 15px; margin-bottom: 8px; border-radius: 8px; cursor: pointer; color: #94a3b8; display: flex; align-items: center; gap: 12px; }
        .nav li.active { background: var(--council-blue); color: white; }

        .admin-main { flex: 1; padding: 30px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; background: var(--white); padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }

        /* User Table Styles */
        .panel { background: var(--white); padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }

        /* Role & Status Badges */
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .role-admin { background: #fee2e2; color: #991b1b; }
        .role-officer { background: #e0e7ff; color: #3730a3; }
        .role-citizen { background: #f3f4f6; color: #374151; }
        .status-dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .active-dot { background: var(--success); }
        .suspended-dot { background: var(--danger); }

        .btn-icon { padding: 8px; background: #f1f5f9; border: none; border-radius: 6px; cursor: pointer; color: var(--text-muted); margin-right: 5px; }
        .btn-icon:hover { color: var(--council-blue); background: #e2e8f0; }
        .btn-add { background: var(--council-blue); color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }

        .user-info { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 35px; height: 35px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 0.8rem; }
    </style>
</head>
<body>

<aside class="admin-sidebar">
    <div class="brand"><i class="fas fa-city"></i> KMC ADMIN</div>
    <ul class="nav">
        <li class="active"><i class="fas fa-users-cog"></i> User Management</li>
        <li onclick="location.href='admin_dashboard.php'"><i class="fas fa-th-large"></i> back</li>
    </ul>
</aside>

<div class="admin-main">
    <header class="topbar">
        <h1>User Management</h1>
        <button class="btn-add" onclick="alert('Open Add User Modal')"><i class="fas fa-plus"></i> Add New User</button>
    </header>

    <section class="panel">
        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <input type="text" placeholder="Search by name, NRC, or email..." id="userSearch" style="flex:1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            <select style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                <option>All Roles</option>
                <option>Admin</option>
                <option>Officer</option>
                <option>Citizen</option>
            </select>
        </div>


        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="avatar" style="background: #2563eb;">BM</div>
                            <div>
                                <strong>Banda Mutale</strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">mutale.b@kmc.gov.zm</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge role-admin">Admin</span></td>
                    <td><span class="status-dot active-dot"></span> Active</td>
                    <td>2 mins ago</td>
                    <td>
                        <button class="btn-icon" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon" title="Reset Password"><i class="fas fa-key"></i></button>
                        <button class="btn-icon" title="Suspend"><i class="fas fa-ban"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="avatar" style="background: #10b981;">JC</div>
                            <div>
                                <strong>John Chilufya</strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">john.c@gmail.com</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge role-citizen">Citizen</span></td>
                    <td><span class="status-dot active-dot"></span> Active</td>
                    <td>Yesterday, 14:20</td>
                    <td>
                        <button class="btn-icon"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon"><i class="fas fa-key"></i></button>
                        <button class="btn-icon"><i class="fas fa-ban"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="avatar" style="background: #ef4444;">SL</div>
                            <div>
                                <strong>Sarah Lungu</strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">sarah.l@kmc.gov.zm</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge role-officer">Officer</span></td>
                    <td><span class="status-dot suspended-dot"></span> Suspended</td>
                    <td>Oct 12, 2025</td>
                    <td>
                        <button class="btn-icon"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon" style="color: var(--success);"><i class="fas fa-undo"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</div>

<script>
    // Search Filter
    document.getElementById('userSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('#userTableBody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
        });
    });
</script>

</body>
</html>