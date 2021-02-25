<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Models\Loan
 *
 * @property int $id
 * @property int $amount
 * @property string $loan_mode
 * @property int $interest_rate
 * @property int $amount_with_interest
 * @property int $company_fees
 * @property int $amount_with_interest_and_company_fees
 * @property int $monthly_installment
 * @property string $loan_start_date
 * @property string $loan_end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Loan newModelQuery()
 * @method static Builder|Loan newQuery()
 * @method static Builder|Loan query()
 * @method static Builder|Loan whereAmount($value)
 * @method static Builder|Loan whereAmountWithInterest($value)
 * @method static Builder|Loan whereAmountWithInterestAndCompanyFees($value)
 * @method static Builder|Loan whereCompanyFees($value)
 * @method static Builder|Loan whereCreatedAt($value)
 * @method static Builder|Loan whereId($value)
 * @method static Builder|Loan whereInterestRate($value)
 * @method static Builder|Loan whereLoanEndDate($value)
 * @method static Builder|Loan whereLoanMode($value)
 * @method static Builder|Loan whereLoanStartDate($value)
 * @method static Builder|Loan whereMonthlyInstallment($value)
 * @method static Builder|Loan whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $loan_duration
 * @method static Builder|Loan whereLoanDuration($value)
 * @property int $monthly_installment_with_company_fees
 * @method static Builder|Loan whereMonthlyInstallmentWithCompanyFees($value)
 * @property string $loan_amount
 * @method static Builder|Loan whereLoanAmount($value)
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 */
class Loan extends Model
{
    protected $guarded = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'loan_start_date' => 'datetime',
        'loan_end_date' => 'datetime',
    ];

    use HasFactory, Notifiable;

    # User and Loan have a many to many relation
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
