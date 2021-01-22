<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\Models\LoanUser
 *
 * @property int $id
 * @property int $loan_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $borrowers
 * @property-read int|null $borrowers_count
 * @property-read Collection|User[] $lenders
 * @property-read int|null $lenders_count
 * @method static Builder|LoanUser newModelQuery()
 * @method static Builder|LoanUser newQuery()
 * @method static Builder|LoanUser query()
 * @method static Builder|LoanUser whereCreatedAt($value)
 * @method static Builder|LoanUser whereId($value)
 * @method static Builder|LoanUser whereLoanId($value)
 * @method static Builder|LoanUser whereUpdatedAt($value)
 * @method static Builder|LoanUser whereUserId($value)
 * @mixin Eloquent
 */

class LoanUser extends Pivot
{
    use HasFactory;

    # getting the lenders
    public function lenders()
    {
        return $this->belongsToMany(User::class)->wherePivot('role', 'lender');
    }

    # getting the borrowers
    public function borrowers()
    {
        return $this->belongsToMany(User::class)->wherePivot('role', 'borrower');
    }
}
