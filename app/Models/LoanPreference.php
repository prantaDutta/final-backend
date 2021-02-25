<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LoanPreference
 *
 * @property int $id
 * @property int $user_id
 * @property mixed $distributed_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference whereDistributedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference whereUserId($value)
 * @mixin \Eloquent
 * @property string $distributed_amounts
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference whereDistributedAmounts($value)
 * @property string $latest_deposited_amount
 * @method static \Illuminate\Database\Eloquent\Builder|LoanPreference whereLatestDepositedAmount($value)
 */
class LoanPreference extends Model
{
    use HasFactory;

    protected $guarded = [];

    # Loan Preference and user has one to one relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
