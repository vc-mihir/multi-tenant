<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDatabase extends Model
{
    protected $fillable = [
        'company_id',
        'db_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'db_username' => 'encrypted',
            'db_password' => 'encrypted',
        ];
    }

    /**
     * Get the company that owns the database.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
