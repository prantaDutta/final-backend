<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Administration
 *
 * @property int $id
 * @property int $user_id
 * @property array|null $penalty_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $admin
 * @method static \Database\Factories\AdministrationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Administration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Administration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Administration query()
 * @method static \Illuminate\Database\Eloquent\Builder|Administration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administration wherePenaltyData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administration whereUserId($value)
 * @mixin \Eloquent
 */
class Administration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'penalty_data' => 'array',
    ];

    # User(admin) and administration has a one to one relation
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->where('role', 'admin');
    }
}
