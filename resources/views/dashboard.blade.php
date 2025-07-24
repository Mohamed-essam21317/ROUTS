<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rotous Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --main-dark: #052A43;
            --blue: #0D6AA9;
            --yellow: #FDC70A;
            --light-blue: #A2BDFF;
            --green: #2FD159;
            --red: #D32B2B;
            --orange: #FF784F;
            --mid-blue: #418DF9;
            --sky-blue: #699CFF;
            --dark-blue: #052A43;
        }

        body {
            background-color: #fff;
            /* Background White */
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(to bottom, var(--main-dark), var(--blue));
            color: white;
            position: fixed;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: var(--blue);
        }

        .sidebar .dropdown a {
            padding-left: 30px;
        }

        .content {
            margin-left: 260px;
            padding: 30px;
        }

        /* Cards */
        .card-body h5,
        .card-body p {
            color: white;
        }

        .card {
            background: var(--blue);
        }

        /* Button Customizations */
        .btn-primary {
            background-color: var(--yellow);
            border-color: var(--yellow);
            color: white;
        }

        .btn-danger {
            background-color: var(--red);
            border-color: var(--red);
            color: white;
        }

        .btn-warning {
            background-color: var(--yellow);
            border-color: var(--yellow);
            color: white;
        }

        .btn-dark {
            background-color: var(--main-dark);
            border-color: var(--main-dark);
            color: white;
        }

        .form-control:focus {
            border-color: var(--mid-blue);
            box-shadow: 0 0 0 0.2rem rgba(65, 141, 249, 0.25);
        }

        /* Table Styling */
        table {
            border-color: var(--light-blue);
        }

        /* Background Gradient for Cards */
        .bg-primary {
            background: linear-gradient(to right, var(--blue), var(--sky-blue)) !important;
        }

        .bg-success {
            background-color: var(--green) !important;
        }

        .bg-info {
            background-color: var(--sky-blue) !important;
        }

        .bg-dark {
            background-color: var(--main-dark) !important;
        }

        /* Hover effects for Buttons */
        .btn-primary:hover,
        .btn-danger:hover,
        .btn-warning:hover,
        .btn-dark:hover {
            background-color: var(--sky-blue);
            border-color: var(--sky-blue);
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2 class="text-center p-3">ROUTUS</h2>
        <div class="dropdown">
            <a class="dropdown-toggle px-3 py-2" data-bs-toggle="collapse" href="#dashboardSub" role="button">
                Dashboard
            </a>
            <div class="collapse px-2" id="dashboardSub">
                <a href="#" onclick="showSection('supervisorsSection')">Supervisors</a>
                <a href="#" onclick="showSection('schoolsSection')">Schools</a>
                <a href="#" onclick="showSection('driversSection')">Drivers</a>
                <a href="#" onclick="showSection('transactionSection')">transactions</a>
            </div>
        </div>
        <a href="#" onclick="showSection('tripsSection')">Trips</a>
        <a href="#" onclick="showSection('settingsSection')">Settings</a>
    </div>

    <div class="content">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Students</h5>
                        <p>{{ $studentsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Schools</h5>
                        <p>{{ $schoolsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Supervisors</h5>
                        <p>{{ $supervisorsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Drivers</h5>
                        <p>{{ $driversCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisors Section -->
        <div id="supervisorsSection">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">🧑‍💼 Manage users</div>
                <div class="card-body">
                    <form id="createSupervisorForm">
                        <input type="text" name="name" placeholder="Name" class="form-control mb-2" required>
                        <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
                        <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control mb-2" required>

                        <input type="text" name="phone_number" placeholder="Phone Number" class="form-control mb-2" required>
                        <input type="number" name="school_id" placeholder="School ID" class="form-control mb-2" required>

                        <select name="role" class="form-control mb-2">
                            <option value="supervisor" selected>Supervisor</option>
                            <option value="admin">Admin</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                    <p id="supervisorFormStatus" class="text-danger mt-2"></p>
                    <hr>
                    <h5>Users List</h5>
                    <table class="table table-sm table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody id="supervisorsTable"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Schools Section -->
        <div id="schoolsSection" style="display: none;">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">🏫 Manage Schools</div>
                <div class="card-body">
                    <form id="createSchoolForm">
                        <input type="text" name="school_name" placeholder="School Name" class="form-control mb-2" required>
                        <input type="text" name="address" placeholder="Address" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-primary">Create School</button>
                    </form>
                    <hr>
                    <h5>Schools List</h5>
                    <table class="table table-sm table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="schoolsTable"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Drivers Section -->
        <div id="driversSection" style="display: none;">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">🚗 Manage Drivers</div>
                <div class="card-body">
                    <form id="createDriverForm">
                        <input type="text" name="name" placeholder="Name" class="form-control mb-2" required>
                        <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
                        <input type="text" name="license_number" placeholder="License Number" class="form-control mb-2">
                        <button type="submit" class="btn btn-primary">Create Driver</button>
                    </form>
                    <hr>
                    <h5>Drivers List</h5>
                    <table class="table table-sm table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>License</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="driversTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Transactions Section -->
        <div id="transactionSection" style="display: none;">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">💳 Transactions</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Success</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTable"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Trips Section -->
        <div id="tripsSection" style="display: none;">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">🚌 Recent Trips</div>
                <div class="card-body">
                    <ul id="recentTripsList" class="list-group"></ul>
                </div>
            </div>
        </div>

        <!-- Settings Section -->
        <div id="settingsSection" style="display: none;">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">⚙️ System Settings</div>
                <div class="card-body">
                    <form id="systemSettingsForm">
                        <label for="system_name" class="form-label">System Name</label>
                        <input type="text" name="system_name" id="system_name" class="form-control mb-2" placeholder="e.g. ROTOUS" required>
                        <button type="submit" class="btn btn-primary">💾 Save</button>
                    </form>
                    <hr class="my-4">

                    <!-- Password Change -->
                    <h6>🔐 Change Admin Password</h6>
                    <form id="passwordChangeForm" class="mb-3">
                        <input type="password" name="old_password" class="form-control mb-2" placeholder="Old Password" required>
                        <input type="password" name="new_password" class="form-control mb-2" placeholder="New Password" required>
                        <input type="password" name="confirm_password" class="form-control mb-2" placeholder="Confirm Password" required>
                        <button type="submit" class="btn btn-danger">Change Password</button>
                        <div id="passwordStatus" class="text-success mt-2"></div>
                    </form>

                    <!-- Notifications -->
                    <h6>🔔 Notifications</h6>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notificationsToggle">
                        <label class="form-check-label" for="notificationsToggle">Enable trip notifications</label>
                    </div>

                    <!-- Theme -->
                    <h6>🎨 Theme Mode</h6>
                    <select id="themeSelect" class="form-select mb-3">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>

                    <div id="settingsStatus" class="text-success mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function showSection(id) {
                const sections = [
                    'supervisorsSection',
                    'schoolsSection',
                    'driversSection',
                    'tripsSection',
                    'settingsSection',
                    'transactionSection'
                ];

                sections.forEach(section => {
                    document.getElementById(section).style.display = 'none';
                });

                document.getElementById(id).style.display = 'block';
            }

            document.addEventListener("DOMContentLoaded", () => {
                showSection('supervisorsSection');
                loadSupervisors();
                loadSchools();
                loadDrivers();
                loadRecentTrips();
                loadTransactions();

                async function loadTransactions() {
                    const res = await fetch("/api/transactions");
                    const data = await res.json();
                    let rows = "";
                    data.transactions.forEach(t => {
                        rows += `
            <tr>
                <td>${t.id}</td>
                <td>${t.user ? t.user.name : ''}</td>
                <td>${t.amount_cents / 100} ${t.currency}</td>
                <td>${t.success ? '✅' : '❌'}</td>
                <td>${t.created_at}</td>
            </tr>
        `;
                    });
                    document.getElementById("transactionsTable").innerHTML = rows;
                }

                document.getElementById("createSupervisorForm").addEventListener("submit", async function(e) {
                    e.preventDefault();
                    const form = this;
                    const status = document.getElementById("supervisorFormStatus");
                    const formData = new FormData(form);
                    const role = formData.get('role');
                    let endpoint = "/api/supervisors";
                    if (role === "admin") {
                        endpoint = "/api/admins";
                    }

                    try {
                        const res = await fetch(endpoint, {
                            method: "POST",
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });

                        if (!res.ok) {
                            const err = await res.text();
                            status.textContent = "❌ Error: " + err;
                            return;
                        }

                        form.reset();
                        loadSupervisors();
                        status.textContent = "✅ User created!";
                    } catch (err) {
                        status.textContent = "❌ JS Error: " + err.message;
                    }
                });
                // ...existing code...

                document.getElementById("createSchoolForm").addEventListener("submit", async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const res = await fetch("/api/schools", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    if (res.ok) {
                        this.reset();
                        loadSchools();
                    }
                });

                document.getElementById("createDriverForm").addEventListener("submit", async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const res = await fetch("/api/drivers", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    if (res.ok) {
                        this.reset();
                        loadDrivers();
                    }
                });
            });

            async function loadSupervisors() {
                const res = await fetch("/api/supervisors");
                const data = await res.json();
                let rows = "";
                data.supervisors.forEach(s => {
                    rows += `<tr>
                    <td>${s.id}</td>
                    <td>${s.first_name} ${s.last_name}</td>
                    <td>${s.email}</td>
                    <td>
                        <button onclick="toggleBlockSupervisor(${s.id})" class="btn btn-warning btn-sm">Block / Unblock</button>
                        <button onclick="deleteSupervisor(${s.id})" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>`;
                });
                document.getElementById("supervisorsTable").innerHTML = rows;
            }

            async function deleteSupervisor(id) {
                if (!confirm("Are you sure?")) return;
                await fetch(`/api/supervisors/${id}`, {
                    method: "DELETE"
                });
                loadSupervisors();
            }

            async function toggleBlockSupervisor(id) {
                await fetch(`/api/supervisors/${id}/toggle-block`, {
                    method: "PATCH"
                });
                loadSupervisors();
            }

            async function loadSchools() {
                const res = await fetch("/api/schools");
                const data = await res.json();
                let rows = "";
                data.schools.forEach(s => {
                    rows += `<tr>
                    <td>${s.id}</td>
                    <td>${s.school_name}</td>
                    <td>${s.address}</td>
                    <td><button onclick="deleteSchool(${s.id})" class="btn btn-danger btn-sm">Delete</button></td>
                </tr>`;
                });
                document.getElementById("schoolsTable").innerHTML = rows;
            }

            async function deleteSchool(id) {
                if (!confirm("Are you sure?")) return;
                await fetch(`/api/schools/${id}`, {
                    method: "DELETE"
                });
                loadSchools();
            }

            async function loadDrivers() {
                const res = await fetch("/api/drivers");
                const data = await res.json();
                let rows = "";
                data.forEach(d => {
                    rows += `<tr>
                    <td>${d.id}</td>
                    <td>${d.name}</td>
                    <td>${d.email}</td>
                    <td>${d.license_number || '-'}</td>
                    <td><button onclick="deleteDriver(${d.id})" class="btn btn-danger btn-sm">Delete</button></td>
                </tr>`;
                });
                document.getElementById("driversTable").innerHTML = rows;
            }

            async function deleteDriver(id) {
                if (!confirm("Are you sure?")) return;
                await fetch(`/api/drivers/${id}`, {
                    method: "DELETE"
                });
                loadDrivers();
            }

            async function loadRecentTrips() {
                try {
                    const res = await fetch("/api/trips/recent");
                    const trips = await res.json();
                    let items = "";
                    trips.forEach(t => {
                        items += `<li class="list-group-item">
                        <strong>${t.route_name}</strong><br>
                        Driver: ${t.driver?.name ?? 'N/A'} |
                        School: ${t.school?.school_name ?? 'N/A'}<br>
                        <small>Departure: ${new Date(t.departure_time).toLocaleString()}</small>
                    </li>`;
                    });
                    document.getElementById("recentTripsList").innerHTML = items;
                } catch (error) {
                    document.getElementById("recentTripsList").innerHTML =
                        `<li class="list-group-item text-danger">⚠️ Failed to load trips</li>`;
                }
            }

            document.getElementById("systemSettingsForm").addEventListener("submit", async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const res = await fetch("/api/settings", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (res.ok) {
                    document.get

                    ElementById("settingsStatus").textContent = "✅ Settings updated successfully!";
                } else {
                    document.getElementById("settingsStatus").textContent = "❌ Failed to update settings.";
                }
            });
        </script>
</body>

</html>