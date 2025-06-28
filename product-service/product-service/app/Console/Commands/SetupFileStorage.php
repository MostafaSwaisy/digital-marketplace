<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetupFileStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup file storage directories for product files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Setting up file storage directories...');

        // Create storage directories
        $directories = [
            'products',
            'previews',
            'temp',
            'thumbnails'
        ];

        foreach ($directories as $dir) {
            if (!Storage::disk('private')->exists($dir)) {
                Storage::disk('private')->makeDirectory($dir);
                $this->info("✓ Created directory: storage/app/private/{$dir}");
            } else {
                $this->line("✓ Directory already exists: storage/app/private/{$dir}");
            }
        }

        // Check permissions
        $storagePath = storage_path('app/private');
        if (!is_writable($storagePath)) {
            $this->error("✗ Storage directory is not writable: {$storagePath}");
            $this->warn("Please run: chmod -R 755 {$storagePath}");
            return 1;
        }

        $this->info('✓ Storage directory is writable');

        // Create .gitignore files to keep directories in git but ignore contents
        foreach ($directories as $dir) {
            $gitignorePath = storage_path("app/private/{$dir}/.gitignore");
            if (!file_exists($gitignorePath)) {
                file_put_contents($gitignorePath, "*\n!.gitignore\n");
                $this->info("✓ Created .gitignore for {$dir}");
            }
        }

        $this->info('');
        $this->info('🎉 File storage setup completed successfully!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Test file upload in creator dashboard');
        $this->info('2. Check that files are saved in storage/app/private/');
        $this->info('3. Verify download functionality');

        return 0;
    }
}