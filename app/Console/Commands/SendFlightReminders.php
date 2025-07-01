<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Flight;
use Carbon\Carbon;


class SendFlightReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-flight-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to passengers 24 hours before their flight';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $targetTime = Carbon::now()->addDay()->format('Y-m-d H:i:00');

        // $flights = Flight::with('passengers')
        //     ->where('departure_time', $targetTime)
        //     ->get();


        $start = Carbon::now('UTC')->addDay()->subMinutes(30);  // 24 hours from now minus 30 minutes
        $end = Carbon::now('UTC')->addDay()->addHours(2);  // 24 hours from now plus 30 minutes


        $flights = Flight::with('passengers')
            ->whereBetween('departure_time', [$start, $end])
            ->get();


        foreach ($flights as $flight) {
            foreach ($flight->passengers as $passenger) {
                if (!$passenger->reminder_sent_at) {
                    $passenger->notify(new \App\Notifications\FlightReminderNotification($flight));
                    $passenger->reminder_sent_at = now();
                    $passenger->save();
                }
            }
        }

        $this->info('Flight reminders sent!');
    }
}
