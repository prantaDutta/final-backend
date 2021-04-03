<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Installment
 *
 * @property int $id
 * @property int $user_id
 * @property int $loan_id
 * @property string $unique_installment_id
 * @property int $amount
 * @property string $status
 * @property string $penalty_amount
 * @property int $installment_no
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Installment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereInstallmentNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment wherePenaltyAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereUniqueInstallmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereUserId($value)
 * @mixin \Eloquent
 * @property string $total_amount
 * @property string $due_date
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereTotalAmount($value)
 * @method static \Database\Factories\InstallmentFactory factory(...$parameters)
 */
class Installment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
