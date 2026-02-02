<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserLocales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locales:update-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all users with invalid locales to default (id)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $availableLocales = config('app.available_locales', ['id', 'zh']);

        // Find all users with invalid locales
        $invalidUsers = User::whereNotIn('locale', $availableLocales)
            ->orWhereNull('locale')
            ->get();

        if ($invalidUsers->isEmpty()) {
            $this->info('✓ No users with invalid locales found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$invalidUsers->count()} users with invalid locales.");

        $bar = $this->output->createProgressBar($invalidUsers->count());
        $bar->start();

        foreach ($invalidUsers as $user) {
            $oldLocale = $user->locale ?? 'null';
            $user->update(['locale' => 'id']);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Successfully updated {$invalidUsers->count()} users to locale 'id'.");

        return Command::SUCCESS;
    }
}
