<?php

namespace App\Models\Tenant;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use App\Notifications\VerifyTenantUserEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, HasUuids, SoftDeletes, Notifiable, LogsActivity;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'tenant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'name_hash',
        'email_hash',
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
     * Decrypt and return the stored name.
     *
     * @param string|null $value
     * @return string|null
     */
    public function getNameAttribute(?string $value): ?string
    {
        return $value ? decrypt($value) : null;
    }

    /**
     * Encrypt the name and keep name_hash in sync for DB indexing.
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name']      = encrypt($value);
        $this->attributes['name_hash'] = hash('sha256', strtolower($value));
    }

    /**
     * Decrypt and return the stored email.
     *
     * @param string|null $value
     * @return string|null
     */
    public function getEmailAttribute(?string $value): ?string
    {
        return $value ? decrypt($value) : null;
    }

    /**
     * Encrypt the email and keep email_hash in sync for DB lookups.
     *
     * @param string $value
     * @return void
     */
    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email']      = encrypt($value);
        $this->attributes['email_hash'] = hash('sha256', strtolower($value));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyTenantUserEmail());
    }

    /**
     * Send the email changed verification notification.
     *
     * @return void
     */
    public function sendEmailChangedVerificationNotification(): void
    {
        $this->notify(new VerifyTenantUserEmail(emailChanged: true));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tenant_user')
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}
