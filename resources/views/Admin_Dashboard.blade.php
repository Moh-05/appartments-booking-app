<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background: #343a40; color: #fff; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; }
        .navbar .notifications { position: relative; cursor: pointer; }
        .navbar .notifications .badge { position: absolute; top: -5px; right: -10px; background: red; color: #fff; border-radius: 50%; padding: 3px 7px; font-size: 12px; }
        .container { padding: 20px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        table th { background: #007bff; color: #fff; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; margin: 2px; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-info { background: #17a2b8; color: #fff; }
        .btn-warning { background: #ffc107; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn:hover { opacity: 0.9; }
        .search-bar { margin-bottom: 15px; }
        .search-bar input { padding: 8px; width: 250px; border: 1px solid #ccc; border-radius: 4px; }
        .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: #fff; padding: 20px; border-radius: 8px; width: 500px; max-height: 80vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; }
        .close { cursor: pointer; font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2>Admin Dashboard</h2>
        <div class="notifications" onclick="openModal('notificationsModal')">
            🔔 <span class="badge">3</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <h3>Users Management</h3>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search by username..." onkeyup="searchUser()">
            </div>
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>#</th><th>Full Name</th><th>Username</th><th>Phone</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <button class="btn btn-primary" onclick="openModal('bookingsModal', {{ $user->id }})">Bookings</button>
                            <button class="btn btn-info" onclick="openModal('appartementsModal', {{ $user->id }})">Appartements</button>
                            <button class="btn btn-warning" onclick="openModal('detailsModal', {{ $user->id }})">More Details</button>
                            <button class="btn btn-danger">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals -->
    <div id="bookingsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h3>User Bookings</h3><span class="close" onclick="closeModal('bookingsModal')">&times;</span></div>
            <div class="modal-body"></div>
        </div>
    </div>
    <div id="appartementsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h3>User Appartements</h3><span class="close" onclick="closeModal('appartementsModal')">&times;</span></div>
            <div class="modal-body"></div>
        </div>
    </div>
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h3>User Details</h3><span class="close" onclick="closeModal('detailsModal')">&times;</span></div>
            <div class="modal-body"></div>
        </div>
    </div>
    <div id="notificationsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h3>Notifications</h3><span class="close" onclick="closeModal('notificationsModal')">&times;</span></div>
            <div class="modal-body"></div>
        </div>
    </div>

    <script>
        function openModal(id, userId = null) {
            const modal = document.getElementById(id);
            modal.style.display = 'flex';
            let body = modal.querySelector('.modal-body');
            body.innerHTML = "";

            if (id === 'notificationsModal') {
                body.innerHTML += "<p>🔔 A new appartement was submitted for approval!</p>";
                body.innerHTML += "<p>🔔 User John created a new booking.</p>";
            }
            if (id === 'appartementsModal') {
                body.innerHTML += "<p>🏠 Appartements for user ID: " + userId + "</p>";
                // هنا تقدر تستخدم AJAX أو تمرر بيانات من Laravel لعرض شقق المستخدم
            }
            if (id === 'bookingsModal') {
                body.innerHTML += "<p>📅 Bookings for user ID: " + userId + "</p>";
                // نفس الشيء: تجيب الحجوزات من DB
            }
            if (id === 'detailsModal') {
                body.innerHTML += "<p>Details for user ID: " + userId + "</p>";
            }
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function searchUser() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#usersTable tbody tr");
            rows.forEach(row => {
                let username = row.cells[2].textContent.toLowerCase();
                row.style.display = username.indexOf(input) > -1 ? "" : "none";
            });
        }
    </script>
</body>
</html>