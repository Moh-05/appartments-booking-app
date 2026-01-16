<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #343a40;
            color: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar .notifications {
            position: relative;
            cursor: pointer;
        }

        .navbar .notifications .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background: red;
            color: #fff;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 12px;
        }

        .container {
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        table th {
            background: #007bff;
            color: #fff;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            margin: 2px;
        }

        .btn-primary {
            background: #007bff;
            color: #fff;
        }

        .btn-info {
            background: #17a2b8;
            color: #fff;
        }

        .btn-warning {
            background: #ffc107;
            color: #fff;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .search-bar {
            margin-bottom: 15px;
        }

        .search-bar input {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close {
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2>Admin Dashboard</h2>
        <div class="notifications" onclick="openModal('notificationsModal')">
            🔔 <span class="badge" id="notifBadge">0</span>
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
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Actions</th>
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
                                <button class="btn btn-primary"
                                    onclick="window.location.href='/user/{{ $user->username }}/bookings'">Bookings</button>
                                <button class="btn btn-info"
                                    onclick="window.location.href='/user/{{ $user->username }}/appartements'">Appartements</button>
                                <button class="btn btn-warning"
                                    onclick="window.location.href='/user/{{ $user->username }}/details'">More
                                    Details</button>
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('⚠️ Are you sure you want to delete this user account? This action cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

 <!-- Notifications Modal -->
<div id="notificationsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Notifications</h3>
            <span class="close" onclick="closeModal('notificationsModal')">&times;</span>
        </div>
        <div class="modal-body" id="notifBody"></div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.toast {
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 12px;
    position: fixed;
    z-index: 9999;
    left: 50%;
    bottom: 30px;
    font-size: 14px;
}
.toast.show {
    visibility: visible;
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
}
@keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30px; opacity: 1;}
}
@keyframes fadeout {
    from {bottom: 30px; opacity: 1;}
    to {bottom: 0; opacity: 0;}
}
</style>

<script>
    function showToast(message) {
        const toast = document.getElementById("toast");
        toast.textContent = message;
        toast.className = "toast show";
        setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 3000);
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'flex';
        let body = modal.querySelector('.modal-body');
        body.innerHTML = "";

        if (id === 'notificationsModal') {
            fetch('/admin/notifications')
                .then(res => res.json())
                .then(data => {
                    if (!data.notifications || data.notifications.length === 0) {
                        body.innerHTML = "<p>No notifications found.</p>";
                    } else {
                        data.notifications.forEach(n => {
                            let actionHtml = "";

                            if (n.status === "approved") {
                                actionHtml = `<p>✅ Appartement (${n.title ?? 'N/A'}) was approved</p>`;
                            } else if (n.status === "rejected") {
                                actionHtml = `<p>❌ Appartement (${n.title ?? 'N/A'}) was rejected</p>`;
                            } else {
                                actionHtml = `
                                    <button class="btn btn-primary" onclick="approveAppartement(${n.appartement_id})">Approve</button>
                                    <button class="btn btn-danger" onclick="rejectAppartement(${n.appartement_id})">Reject</button>
                                `;
                            }

                            body.innerHTML += `
                                <div style="border-bottom:1px solid #ddd; padding:8px;">
                                    <p><strong>${n.message}</strong></p>
                                    <p>🏠 Title: ${n.title ?? 'N/A'}</p>
                                    <p>👤 Owner: ${n.owner ?? 'N/A'}</p>
                                    <p>Status: <span id="status-${n.appartement_id}">${n.status}</span></p>
                                    <p>📅 Date: ${n.created_at}</p>
                                    ${actionHtml}
                                </div>
                            `;
                        });
                    }
                    document.getElementById('notifBadge').textContent = data.notifications.length;
                })
                .catch(err => {
                    body.innerHTML = "<p>Error loading notifications.</p>";
                });
        }
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Approve function
    function approveAppartement(id) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/admin/appartements/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json'
            }
        })
        .then(() => {
            document.getElementById(`status-${id}`).textContent = 'approved';
            const parentDiv = document.getElementById(`status-${id}`).parentElement;
            parentDiv.querySelectorAll('button').forEach(btn => btn.remove());
            parentDiv.insertAdjacentHTML('beforeend', `<p>✅ Appartement was approved</p>`);
            showToast("✅ Appartement was approved");
        });
    }

    // Reject function
    function rejectAppartement(id) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/admin/appartements/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json'
            }
        })
        .then(() => {
            document.getElementById(`status-${id}`).textContent = 'rejected';
            const parentDiv = document.getElementById(`status-${id}`).parentElement;
            parentDiv.querySelectorAll('button').forEach(btn => btn.remove());
            parentDiv.insertAdjacentHTML('beforeend', `<p>❌ Appartement was rejected</p>`);
            showToast("❌ Appartement was rejected");
        });
    }

    // تحديث عدد الإشعارات عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', () => {
        fetch('/admin/notifications')
            .then(res => res.json())
            .then(data => {
                document.getElementById('notifBadge').textContent = data.notifications.length;
            });
    });
</script>po