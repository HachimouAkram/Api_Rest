<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CompletePastBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:complete-past {--dry-run : Simuler sans modifier}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marquer automatiquement les réservations terminées comme "completed"';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $today = now()->toDateString();

        // Réservations confirmées dont la date de fin est passée
        $query = Booking::where('status', 'confirmed')
            ->where('end_date', '<', $today);

        $count = $query->count();

        if ($count === 0) {
            $this->info('Aucune réservation à compléter.');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->info("Simulation: {$count} réservation(s) seraient complétée(s).");
            return Command::SUCCESS;
        }

        $updated = $query->update(['status' => 'completed']);

        $this->info("{$updated} réservation(s) marquée(s) comme terminées.");

        return Command::SUCCESS;
    }
}
