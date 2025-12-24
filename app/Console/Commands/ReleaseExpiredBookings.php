<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class ReleaseExpiredBookings extends Command
{
    protected $signature = 'bookings:release-expired';
    protected $description = 'Release appartments whose bookings have expired';

    public function handle()
    {
        $today = Carbon::now();


        $expiredBookings = Booking::where('status', 'booked')
            ->where('end_date', '<', $today)
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->status = 'completed'; 
            $booking->save();   

            $appartement = $booking->appartement;
            if ($appartement) {
                $appartement->available = true; 
                $appartement->save();
            }
        }

        $this->info('Expired bookings released successfully.');
    }
}