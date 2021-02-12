<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\TransactionDetail
 *
 * @property int $id
 * @property int $transactions_id
 * @property string|null $card_type
 * @property string|null $card_no
 * @property string|null $bank_tran_id
 * @property string|null $error
 * @property string|null $card_issuer
 * @property string|null $card_brand
 * @property string|null $risk_level
 * @property string|null $risk_title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereBankTranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereCardBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereCardIssuer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereCardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereCardType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereRiskTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereTransactionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $transaction_id
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionDetail whereTransactionId($value)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 */
class TransactionDetail extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['card_type','card_no','bank_tran_id','error','card_issuer','card_brand','risk_level','risk_title'];

    # Transaction and Transaction Details have a one to one relation
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
