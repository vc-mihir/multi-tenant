<?php

namespace App\Models\Tenant;

use App\Notifications\VerifyCompanyEmail;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmail, Notifiable, HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    protected string $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_name',
        'subdomain',
        'company_email',
        'website',
        'license_number',
        'address',
        'country',
        'state',
        'city',
        'password',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

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

    /**
     * Get the email address that should be used for email verification.
     *
     * @return string
     */
    public function getEmailForVerification(): string
    {
        return $this->company_email;
    }

    /**
     * Get the notification routing information for a notification.
     *
     * @return string
     */
    public function routeNotificationForMail(): string
    {
        return $this->company_email;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyCompanyEmail());
    }
}
