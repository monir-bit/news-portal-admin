<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Admin user credentials
$adminUsername = 'admin';
$adminEmail = 'admin@agamirsomoy.mmonlinemedia.org';
$adminPassword = 'Admin@123456'; // Change this to your desired password
$adminRole = 'admin';

// Check if admin user exists
$admin = User::where('name', $adminUsername)
    ->orWhere('email', $adminEmail)
    ->first();

if ($admin) {
    // Update existing admin user
    $admin->name = $adminUsername;
    $admin->email = $adminEmail;
    $admin->password = Hash::make($adminPassword);
    $admin->role = $adminRole;
    $admin->save();
    
    echo "Admin user updated successfully!\n";
    echo "Username: $adminUsername\n";
    echo "Email: $adminEmail\n";
    echo "Password: $adminPassword\n";
} else {
    // Create new admin user
    $admin = User::create([
        'name' => $adminUsername,
        'email' => $adminEmail,
        'password' => Hash::make($adminPassword),
        'role' => $adminRole,
    ]);
    
    echo "Admin user created successfully!\n";
    echo "Username: $adminUsername\n";
    echo "Email: $adminEmail\n";
    echo "Password: $adminPassword\n";
}
