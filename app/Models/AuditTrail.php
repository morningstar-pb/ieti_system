<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_trails';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action_type',
        'description',
        'ip_address',
    ];

    /**
     * Get the user that owns this audit trail entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
