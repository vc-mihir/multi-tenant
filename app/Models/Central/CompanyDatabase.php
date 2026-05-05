<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class CompanyDatabase extends Model
{
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
}
