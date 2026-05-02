<?php

namespace App\Models;

use App\Services\BrevoTransactionalMailer;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    public const ROLE_RESIDENT = 'resident';
    public const ROLE_ADMIN = 'admin';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'postcode',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function sendEmailVerificationNotification(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes($this->verificationLinkExpiryMinutes()),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ],
        );

        app(BrevoTransactionalMailer::class)->sendEmailVerification(
            $this->email,
            $this->name,
            $verificationUrl,
        );
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ]);

        app(BrevoTransactionalMailer::class)->sendPasswordReset(
            $this->email,
            $this->name,
            $resetUrl,
            $this->passwordResetLinkExpiryMinutes(),
        );
    }

    protected function verificationLinkExpiryMinutes(): int
    {
        /** @var CarbonInterface $expiry */
        $expiry = now()->addMinutes(config('auth.verification.expire', 60));

        return max(1, (int) now()->diffInMinutes($expiry));
    }

    protected function passwordResetLinkExpiryMinutes(): int
    {
        return (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);
    }
}
