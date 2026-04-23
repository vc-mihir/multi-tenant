<?php

namespace App\Models;

use App\Notifications\VerifyCompanyEmail;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Company extends Model implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmail, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_name',
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
        'database_name',
        'database_connection_details',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'database_connection_details',
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
            'database_connection_details' => 'encrypted:array',
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
