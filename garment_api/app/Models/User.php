<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'api_key',
    'api_calls_today',
    'api_calls_month',
    'api_key_generated_at',
    'phone',
    'company',
    'country',
    'notify_scan_complete',
    'notify_weekly_report',
    'notify_new_features',

])]

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Generate new API key
public function generateApiKey(): string
{
    $key = 'nytt_live_' . bin2hex(random_bytes(24));
    $this->update([
        'api_key' => $key,
        'api_key_generated_at' => now(),
    ]);
    return $key;

    
}

public function garments()
{
    return $this->hasMany(\App\Models\Garment::class);
}

public function scans()
{
    return $this->hasMany(\App\Models\Scan::class);
}

}
