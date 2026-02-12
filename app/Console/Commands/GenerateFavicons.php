<?php

namespace App\Console\Commands;

use App\Models\CompanyProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class GenerateFavicons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'favicon:generate {--source= : Path to source logo image}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all favicon sizes from Company Profile logo or specified source';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get source logo
        $sourcePath = $this->option('source');

        if (! $sourcePath) {
            // Use logo from Company Profile
            $companyProfile = CompanyProfile::current();

            if ($companyProfile && $companyProfile->logo_path) {
                $sourcePath = storage_path('app/public/'.$companyProfile->logo_path);
                $this->info('Using logo from Company Profile');
            } else {
                // Fallback to default kisantra.png
                $defaultPath = public_path('images/kisantra.png');
                if (File::exists($defaultPath)) {
                    $sourcePath = $defaultPath;
                    $this->warn('No logo in Company Profile. Using default kisantra.png');
                } else {
                    $this->error('No logo found in Company Profile and kisantra.png not found. Please upload a logo first or specify --source option.');
                    return 1;
                }
            }
        }

        if (! File::exists($sourcePath)) {
            $this->error("Source logo not found at: {$sourcePath}");

            return 1;
        }

        $this->info("Generating favicons from: {$sourcePath}");

        try {
            // Ensure public directory exists
            $publicPath = public_path();

            // Define favicon sizes
            $sizes = [
                'favicon-16x16.png' => 16,
                'favicon-32x32.png' => 32,
                'favicon-96x96.png' => 96,
                'apple-touch-icon.png' => 180,
                'apple-touch-icon-120x120.png' => 120,
                'apple-touch-icon-152x152.png' => 152,
                'android-chrome-192x192.png' => 192,
                'android-chrome-512x512.png' => 512,
                'mstile-150x150.png' => 150,
                'mstile-310x310.png' => 310,
            ];

            foreach ($sizes as $filename => $size) {
                $this->line("Generating {$filename} ({$size}x{$size})...");

                $image = Image::read($sourcePath);
                $image->cover($size, $size);

                $outputPath = $publicPath.'/'.$filename;
                $image->save($outputPath);

                $this->info("✓ Generated: {$filename}");
            }

            // Generate favicon.ico (16x16, 32x32, 48x48 multi-resolution)
            $this->line('Generating favicon.ico (multi-resolution)...');
            $image = Image::read($sourcePath);
            $image->cover(32, 32);
            $image->save($publicPath.'/favicon.ico');
            $this->info('✓ Generated: favicon.ico');

            // Generate manifest.json
            $this->generateManifest();

            // Generate browserconfig.xml
            $this->generateBrowserConfig();

            $this->newLine();
            $this->info('🎉 All favicons generated successfully!');
            $this->newLine();
            $this->line('Generated files:');
            $this->line('  - favicon.ico');
            $this->line('  - favicon-16x16.png, favicon-32x32.png, favicon-96x96.png');
            $this->line('  - apple-touch-icon.png (+ 120x120, 152x152)');
            $this->line('  - android-chrome-192x192.png, android-chrome-512x512.png');
            $this->line('  - mstile-150x150.png, mstile-310x310.png');
            $this->line('  - manifest.json');
            $this->line('  - browserconfig.xml');
            $this->newLine();
            $this->comment('Make sure to add favicon meta tags in your layout file!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to generate favicons: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Generate Web App Manifest (for Android)
     */
    private function generateManifest(): void
    {
        $companyProfile = CompanyProfile::current();
        $appName = $companyProfile ? $companyProfile->name : config('app.name');

        $manifest = [
            'name' => $appName,
            'short_name' => $appName,
            'icons' => [
                [
                    'src' => '/android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => '/android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
            'theme_color' => '#2563eb',
            'background_color' => '#ffffff',
            'display' => 'standalone',
            'start_url' => '/',
        ];

        File::put(
            public_path('manifest.json'),
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('✓ Generated: manifest.json');
    }

    /**
     * Generate Browser Config (for Windows)
     */
    private function generateBrowserConfig(): void
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
    <msapplication>
        <tile>
            <square150x150logo src="/mstile-150x150.png"/>
            <square310x310logo src="/mstile-310x310.png"/>
            <TileColor>#2563eb</TileColor>
        </tile>
    </msapplication>
</browserconfig>
XML;

        File::put(public_path('browserconfig.xml'), $xml);

        $this->info('✓ Generated: browserconfig.xml');
    }
}
