<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, HasRoles, LogsActivity;

    protected string $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
     * Get the activity log options for the model.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('central_user')
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}
