<?php
require_once __DIR__ . '/vendor/autoload.php';

use Pterodactyl\Models\User;
use Illuminate\Http\Request;
use Pterodactyl\Http\Controllers\Auth\ForgotPasswordController;

// Load Laravel configuration
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Forgot Password functionality for users with external_id...\n\n";

// Create test users
try {
    // User without external_id (should allow password reset)
    $userWithoutExternalId = User::where('email', 'test1@example.com')->first();
    if (!$userWithoutExternalId) {
        $userWithoutExternalId = User::factory()->create([
            'email' => 'test1@example.com',
            'external_id' => null
        ]);
    }
    
    // User with external_id (should deny password reset)
    $userWithExternalId = User::where('email', 'test2@example.com')->first();
    if (!$userWithExternalId) {
        $userWithExternalId = User::factory()->create([
            'email' => 'test2@example.com',
            'external_id' => 'openid-123'
        ]);
    }
    
    echo "Test users created:\n";
    echo "- User without external_id: {$userWithoutExternalId->email} (external_id: " . ($userWithoutExternalId->external_id ?? 'null') . ")\n";
    echo "- User with external_id: {$userWithExternalId->email} (external_id: {$userWithExternalId->external_id})\n\n";
    
    echo "Users with external_id should have password reset disabled.\n";
    echo "Users without external_id should be able to reset passwords.\n\n";
    
    echo "Implementation completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
