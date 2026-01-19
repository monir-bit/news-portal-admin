<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Admin credentials
$adminName = 'admin';
$adminEmail = 'admin@agamirsomoy.mmonlinemedia.org';
$adminPassword = 'Admin@123456'; // Change this if needed
$adminRole = 'admin';

try {
    // Hash password using Laravel's Hash
    $hashedPassword = Hash::make($adminPassword);
    
    // Check if admin exists
    $existingUser = DB::table('users')
        ->where('name', $adminName)
        ->orWhere('email', $adminEmail)
        ->first();
    
    if ($existingUser) {
        // Update existing user
        DB::table('users')
            ->where('id', $existingUser->id)
            ->update([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => $hashedPassword,
                'role' => $adminRole,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        echo "Admin user updated successfully!\n";
    } else {
        // Create new user
        $now = date('Y-m-d H:i:s');
        DB::table('users')->insert([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => $hashedPassword,
            'role' => $adminRole,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        echo "Admin user created successfully!\n";
    }
    
    echo "\nLogin Credentials:\n";
    echo "==================\n";
    echo "Username/Email: $adminEmail\n";
    echo "Password: $adminPassword\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
