<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->username }}'s Appartements</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #2c3e50;
        }
        .appartement-list {
            max-width: 900px;
            margin: 0 auto;
        }
        .appartement-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
        }
        .appartement-card:hover {
            transform: translateY(-5px);
        }
        .appartement-card h3 {
            margin: 0 0 10px;
            font-size: 22px;
            color: #34495e;
        }
        .appartement-card p {
            margin: 5px 0;
            font-size: 15px;
        }
        .images {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .images img {
            width: 140px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .no-appartements {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <h2>Appartements for {{ $user->username }}</h2>

    <div class="appartement-list">
        @forelse($appartements as $app)
            <div class="appartement-card">
                <h3>{{ $app->title }}</h3>
                <p>{{ $app->description ?? 'No description available' }}</p>
                <p>💰 <strong>Price:</strong> {{ $app->price }} $</p>
                <p>📍 <strong>Location:</strong> {{ $app->city }}, {{ $app->area }}</p>
                <p>🏠 <strong>Details:</strong> {{ $app->rooms }} rooms, {{ $app->space }} m², Floor {{ $app->floor }}</p>
                <p><strong>Status:</strong> {{ ucfirst($app->approval_status) }}</p>
                <div class="images">
                    @if(!empty($app->images))
                        @foreach($app->images as $img)
                            <img src="/storage/{{ $img }}" alt="Appartement Image">
                        @endforeach
                    @endif
                </div>
            </div>
        @empty
            <p class="no-appartements">No appartements found for {{ $user->username }}.</p>
        @endforelse
    </div>
</body>
</html>