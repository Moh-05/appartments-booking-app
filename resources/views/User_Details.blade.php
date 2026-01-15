<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - {{ $user->username }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; }
        h2 { text-align: center; margin-bottom: 30px; font-size: 28px; color: #2c3e50; }
        .card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .profile-header { display: flex; align-items: center; gap: 20px; }
        .profile-header img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .profile-info h3 { margin: 0; font-size: 24px; color: #34495e; }
        .profile-info p { margin: 5px 0; font-size: 15px; }
        .id-card img { width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .details-table th { background: #007bff; color: #fff; }
        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 6px; }
        .back-btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile - {{ $user->username }}</h2>

        <div class="card">
            <div class="profile-header">
                {{-- صورة البروفايل --}}
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image">
                <div class="profile-info">
                    <h3>{{ $user->full_name }}</h3>
                    <p>👤 Username: {{ $user->username }}</p>
                    <p>📞 Phone: {{ $user->phone }}</p>
                    <p>📅 Birth Date: {{ $user->user_date }}</p>
                </div>
            </div>
        </div>

        <div class="card id-card">
            <h3>ID Image</h3>
            {{-- صورة الهوية --}}
            <img src="{{ asset('storage/' . $user->id_image) }}" alt="ID Image">
        </div>

        <div class="card">
            <h3>Additional Details</h3>
            <table class="details-table">
                <tr>
                    <th>ID</th>
                    <td>{{ $user->id }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $user->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $user->updated_at }}</td>
                </tr>
            </table>
        </div>

        <a href="/admin/dashboard" class="back-btn">⬅ Back to Dashboard</a>
    </div>
</body>
</html>