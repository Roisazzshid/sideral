<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure role column and default accounts exist automatically
        try {
            if (Schema::hasTable('users')) {
                if (!Schema::hasColumn('users', 'role')) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->string('role')->default('admin')->after('email');
                    });
                }

                User::updateOrCreate(
                    ['email' => 'admin@sideral.com'],
                    [
                        'name'     => 'Admin SIDERAL',
                        'password' => Hash::make('password'),
                        'role'     => 'admin',
                    ]
                );

                User::updateOrCreate(
                    ['email' => 'teknisi@sideral.com'],
                    [
                        'name'     => 'Teknisi SIDERAL',
                        'password' => Hash::make('password'),
                        'role'     => 'teknisi',
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Silence if DB connection is not initialized during setup
        }

        // Auto-cleanup unused TailAdmin files to optimize loading speed
        try {
            $filesToDelete = [
                resource_path('views/pages/calender.blade.php'),
                resource_path('views/pages/profile.blade.php'),
                resource_path('views/pages/blank.blade.php'),
                resource_path('views/pages/fmlightning/settings.blade.php'),
                resource_path('views/pages/fmlightning/users.blade.php'),
            ];
            foreach ($filesToDelete as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }

            $dirsToDelete = [
                resource_path('views/pages/chart'),
                resource_path('views/pages/form'),
                resource_path('views/pages/tables'),
                resource_path('views/pages/ui-elements'),
            ];
            foreach ($dirsToDelete as $dir) {
                if (is_dir($dir)) {
                    $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
                    $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
                    foreach($files as $file) {
                        if ($file->isDir()){
                            @rmdir($file->getRealPath());
                        } else {
                            @unlink($file->getRealPath());
                        }
                    }
                    @rmdir($dir);
                }
            }
        } catch (\Throwable $e) {
            // Silence
        }
    }
}
