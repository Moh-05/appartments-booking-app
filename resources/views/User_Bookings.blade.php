<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookings for {{ $username }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        h2 { text-align: center; margin-bottom: 30px; font-size: 28px; color: #2c3e50; }
        .section-title { font-size: 22px; margin: 20px 0 10px; color: #34495e; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
        .booking-list { max-width: 900px; margin: 0 auto; }
        .booking-card { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .booking-card h3 { margin: 0 0 10px; font-size: 20px; color: #2c3e50; }
        .booking-card p { margin: 5px 0; font-size: 15px; }
        .status { font-weight: bold; margin-top: 8px; }
        .status.ongoing { color: #27ae60; }
        .status.past { color: #e74c3c; }
    </style>
</head>
<body>
    <h2>Bookings for {{ $username }}</h2>

    <div class="booking-list">
        <div class="section-title">On‑Going Bookings</div>
        @forelse($ongoingBookings as $b)
            <div class="booking-card">
                <h3>{{ $b->appartement->title }}</h3>
                <p>📍 City/Area: {{ $b->appartement->city }}, {{ $b->appartement->area }}</p>
                <p>📅 From: {{ $b->start_date }} → To: {{ $b->end_date }}</p>
                <p>💰 Total Price: {{ $b->total_price }} $</p>
                <p class="status ongoing">Status: {{ ucfirst($b->status) }}</p>
            </div>
        @empty
            <p>No ongoing bookings found.</p>
        @endforelse

        <div class="section-title">Past Bookings</div>
        @forelse($pastBookings as $b)
            <div class="booking-card">
                <h3>{{ $b->appartement->title }}</h3>
                <p>📍 City/Area: {{ $b->appartement->city }}, {{ $b->appartement->area }}</p>
                <p>📅 From: {{ $b->start_date }} → To: {{ $b->end_date }}</p>
                <p>💰 Total Price: {{ $b->total_price }} $</p>
                <p class="status past">Status: {{ ucfirst($b->status) }}</p>
            </div>
        @empty
            <p>No past bookings found.</p>
        @endforelse
    </div>
</body>
</html>