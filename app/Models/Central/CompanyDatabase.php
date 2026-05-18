<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CompanyDatabase extends Model
{
    use LogsActivity;

    /**
     * Force the model to always use the central connection.
     *
     * @var string
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'company_id',
        'db_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
    ];

    /**
     * Get the company that owns the database.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('company_database')
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Company database has been {$eventName}");
    }
}
