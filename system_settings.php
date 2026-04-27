<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC Admin - System Configuration</title>
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

        /* Sidebar Navigation */
        .admin-sidebar { width: 260px; background: var(--sidebar-dark); color: white; padding: 25px 20px; flex-shrink: 0; position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; }
        .brand { margin-bottom: 35px; font-size: 1.4rem; font-weight: 800; color: var(--council-blue); display: flex; align-items: center; gap: 10px; }
        .nav { list-style: none; flex-grow: 1; }
        .nav li { padding: 12px 15px; margin-bottom: 8px; border-radius: 8px; cursor: pointer; color: #94a3b8; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        .nav li:hover { background: #1e293b; color: white; }
        .nav li.active { background: var(--council-blue); color: white; }

        .admin-main { flex: 1; padding: 40px; overflow-y: auto; }
        
        /* Tabbed Settings Layout */
        .settings-container { display: grid; grid-template-columns: 240px 1fr; gap: 40px; }
        .settings-nav { display: flex; flex-direction: column; gap: 5px; }
        .settings-nav button { text-align: left; padding: 14px 18px; background: none; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; color: var(--text-muted); transition: 0.3s; }
        .settings-nav button.active { background: var(--white); color: var(--council-blue); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        .settings-content { background: var(--white); padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); min-height: 500px; }
        .section-header { border-bottom: 1px solid var(--bg-light); margin-bottom: 30px; padding-bottom: 15px; }
        .section-header h2 { font-size: 1.25rem; }
        
        .form-row { margin-bottom: 25px; }
        label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 10px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        input, select, textarea { width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-size: 0.95rem; background: #f8fafc; transition: 0.2s; }
        input:focus { border-color: var(--council-blue); background: white; }

        /* Officer List UI */
        .officer-list { list-style: none; margin-top: 15px; }
        .officer-item { padding: 15px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 10px; }
        
        .btn-save { background: var(--council-blue); color: white; border: none; padding: 14px 30px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s; }
        .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-delete { color: var(--danger); background: none; border: none; cursor: pointer; font-weight: 600; }

        /* Toggle Switch */
        .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--success); }
        input:checked + .slider:before { transform: translateX(24px); }
    </style>
</head>
<body>

<aside class="admin-sidebar">
    <div class="brand"><i class="fas fa-city"></i> KMC ADMIN</div>
    <ul class="nav">
        <li class="active"><i class="fas fa-cog"></i> System Settings</li>
        <li onclick="location.href='admin_dashboard.php'"><i class="fas fa-th-large"></i> back</li>
    </ul>
</aside>

<div class="admin-main">
    <header style="margin-bottom: 40px;">
        <h1 style="font-size: 1.8rem;">System Configuration</h1>
        <p style="color: var(--text-muted);">Adjust global parameters and manage administrative staff.</p>
    </header>

    <div class="settings-container">
        <nav class="settings-nav">
            <button class="active" onclick="switchTab(event, 'council')"><i class="fas fa-building"></i> Council Profile</button>
            <button onclick="switchTab(event, 'officers')"><i class="fas fa-user-shield"></i> Officers</button>
            <button onclick="switchTab(event, 'portal')"><i class="fas fa-globe"></i> Portal Preferences</button>
        </nav>

        <main class="settings-content">
            <div id="council" class="tab-pane">
                <div class="section-header"><h2>Council Identity</h2></div>
                <div class="form-row">
                    <label>Official Name</label>
                    <input type="text" value="Kabwe Municipal Council">
                </div>
                <div class="form-row">
                    <label>Support Email Address</label>
                    <input type="email" value="support@kmc.gov.zm">
                </div>
                <div class="form-row">
                    <label>Office Address</label>
                    <textarea rows="3">Civic Center, Plot 1, Independence Avenue, Kabwe, Zambia</textarea>
                </div>
                <button class="btn-save" onclick="notifySave()">Save Council Profile</button>
            </div>

            <div id="officers" class="tab-pane" style="display: none;">
                <div class="section-header"><h2>Authorized Officers</h2></div>
                <div style="display: flex; gap: 10px; margin-bottom: 30px;">
                    <input type="text" id="officerName" placeholder="Enter full name of officer...">
                    <button class="btn-save" onclick="addNewOfficer()">Add Officer</button>
                </div>
                <div class="officer-list" id="officerContainer">
                    <div class="officer-item"><span>Officer Mutale</span><button class="btn-delete" onclick="this.parentElement.remove()">Remove</button></div>
                    <div class="officer-item"><span>Officer Banda</span><button class="btn-delete" onclick="this.parentElement.remove()">Remove</button></div>
                </div>
            </div>

            <div id="portal" class="tab-pane" style="display: none;">
                <div class="section-header"><h2>System Controls</h2></div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #fff9eb; border: 1px solid #fef3c7; border-radius: 12px; margin-bottom: 30px;">
                    <div>
                        <strong style="color: #92400e;">Maintenance Mode</strong>
                        <p style="font-size: 0.85rem; color: #b45309; margin-top: 4px;">When active, citizens cannot submit new requests. Admins still have access.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="form-row">
                    <label>Session Timeout (Minutes)</label>
                    <select style="max-width: 200px;">
                        <option>15</option>
                        <option selected>30</option>
                        <option>60</option>
                        <option>120</option>
                    </select>
                </div>
                <button class="btn-save" onclick="notifySave()">Update Preferences</button>
            </div>
        </main>
    </div>
</div>

<script>
    function switchTab(evt, tabName) {
        const panes = document.getElementsByClassName("tab-pane");
        for (let i = 0; i < panes.length; i++) {
            panes[i].style.display = "none";
        }
        const buttons = document.querySelectorAll(".settings-nav button");
        buttons.forEach(btn => btn.classList.remove("active"));

        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.classList.add("active");
    }

    function addNewOfficer() {
        const name = document.getElementById('officerName').value;
        if (name.trim() === "") return;

        const div = document.createElement('div');
        div.className = 'officer-item';
        div.innerHTML = `<span>${name}</span><button class="btn-delete" onclick="this.parentElement.remove()">Remove</button>`;
        document.getElementById('officerContainer').appendChild(div);
        document.getElementById('officerName').value = "";
    }

    function notifySave() {
        alert("System settings updated successfully.");
    }
</script>

</body>
</html>