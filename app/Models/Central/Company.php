<?php

namespace App\Models\Central;

use App\Notifications\VerifyCompanyEmail;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, HasUuids, MustVerifyEmail, Notifiable, LogsActivity, SoftDeletes;

    /**
     * Force the model to always use the central connection.
     *
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

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
        'company_email_hash',
        'license_number_hash',
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
     * Decrypt and return the stored company email.
     *
     * @param string|null $value
     * @return string|null
     */
    public function getCompanyEmailAttribute(?string $value): ?string
    {
        return $value ? decrypt($value) : null;
    }

    /**
     * Encrypt the company email and keep company_email_hash in sync for DB lookups.
     *
     * @param string $value
     * @return void
     */
    public function setCompanyEmailAttribute(string $value): void
    {
        $this->attributes['company_email']      = encrypt($value);
        $this->attributes['company_email_hash'] = hash('sha256', strtolower($value));
    }

    /**
     * Decrypt and return the stored license number.
     *
     * @param string|null $value
     * @return string|null
     */
    public function getLicenseNumberAttribute(?string $value): ?string
    {
        return $value ? decrypt($value) : null;
    }

    /**
     * Encrypt the license number and keep license_number_hash in sync for DB lookups.
     *
     * @param string $value
     * @return void
     */
    public function setLicenseNumberAttribute(string $value): void
    {
        $this->attributes['license_number']      = encrypt($value);
        $this->attributes['license_number_hash'] = hash('sha256', strtolower($value));
    }

    /**
     * Get the database connection details for the company.
     *
     * @return HasOne
     */
    public function database(): HasOne
    {
        return $this->hasOne(CompanyDatabase::class);
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

    /**
     * Get the log options for the model.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('company')
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Company has been {$eventName}");
    }
}
