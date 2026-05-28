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
     * Get the database connection details for the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function database(): \Illuminate\Database\Eloquent\Relations\HasOne
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
