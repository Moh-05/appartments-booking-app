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
    <tr>
        <td>1</td><td>Mohamad Mohamad</td><td>mohamad</td><td>099999999</td><td>2025-12-27</td>
        <td>
            <button class="btn btn-primary" onclick="openModal('bookingsModal')">Bookings</button>
            <button class="btn btn-info" onclick="openModal('appartementsModal')">Appartements</button>
            <button class="btn btn-warning" onclick="openModal('detailsModal')">More Details</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
    <tr>
        <td>2</td><td>Mohamad 2</td><td>moh</td><td>098888888</td><td>2025-12-26</td>
        <td>
            <button class="btn btn-primary" onclick="openModal('bookingsModal')">Bookings</button>
            <button class="btn btn-info" onclick="openModal('appartementsModal')">Appartements</button>
            <button class="btn btn-warning" onclick="openModal('detailsModal')">More Details</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
    <tr>
        <td>3</td><td>Amer Alzaibak</td><td>amer</td><td>097777777</td><td>2025-12-25</td>
        <td>
            <button class="btn btn-primary" onclick="openModal('bookingsModal')">Bookings</button>
            <button class="btn btn-info" onclick="openModal('appartementsModal')">Appartements</button>
            <button class="btn btn-warning" onclick="openModal('detailsModal')">More Details</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
    <tr>
        <td>4</td><td>Aya</td><td>aya</td><td>096666666</td><td>2025-12-24</td>
        <td>
            <button class="btn btn-primary" onclick="openModal('bookingsModal')">Bookings</button>
            <button class="btn btn-info" onclick="openModal('appartementsModal')">Appartements</button>
            <button class="btn btn-warning" onclick="openModal('detailsModal')">More Details</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
    <tr>
        <td>5</td><td>Leen</td><td>leen</td><td>095555555</td><td>2025-12-23</td>
        <td>
            <button class="btn btn-primary" onclick="openModal('bookingsModal')">Bookings</button>
            <button class="btn btn-info" onclick="openModal('appartementsModal')">Appartements</button>
            <button class="btn btn-warning" onclick="openModal('detailsModal')">More Details</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
    <tr>
        <td>6</td><td>Sami</td><td>sami</td><td>094444444</td><td>2025-12-22</td>
        <td>
            <button class="btn btn-primary" onclick="openModal('bookingsModal')">Bookings</button>
            <button class="btn btn-info" onclick="openModal('appartementsModal')">Appartements</button>
            <button class="btn btn-warning" onclick="openModal('detailsModal')">More Details</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
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
            <div class="modal-body"><p>Full Name: Example User</p><p>Email: example@email.com</p><p>Address: Damascus, Syria</p></div>
        </div>
    </div>
    <div id="notificationsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h3>Notifications</h3><span class="close" onclick="closeModal('notificationsModal')">&times;</span></div>
            <div class="modal-body"></div>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.style.display = 'flex';
            let body = modal.querySelector('.modal-body');
            body.innerHTML = "";

            if (id === 'notificationsModal') {
                body.innerHTML += "<p>🔔 A new appartement was submitted for approval!</p>";
                body.innerHTML += "<p>🔔 User John created a new booking.</p>";
            }
            if (id === 'appartementsModal') {
                body.innerHTML += "<p>🏠 New Appartement: Homs - Waiting for admin approval.</p>";
                body.innerHTML += "<p>🏠 Appartement #2: Aleppo - Pending.</p>";
            }
            if (id === 'bookingsModal') {
                body.innerHTML += "<p>📅 New Booking: Apartment C - Date: 2025-12-30</p>";
                body.innerHTML += "<p>📅 Booking #2: Apartment B - Date: 2025-12-25</p>";
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