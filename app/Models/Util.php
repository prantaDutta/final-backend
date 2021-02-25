<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Models\Util
 *
 * @property int $id
 * @property int $email_verified
 * @property int $mobile_no_verified
 * @property string|null $email_verified_at
 * @property string|null $mobile_no_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Util newModelQuery()
 * @method static Builder|Util newQuery()
 * @method static Builder|Util query()
 * @method static Builder|Util whereCreatedAt($value)
 * @method static Builder|Util whereEmailVerified($value)
 * @method static Builder|Util whereEmailVerifiedAt($value)
 * @method static Builder|Util whereId($value)
 * @method static Builder|Util whereMobileNoVerified($value)
 * @method static Builder|Util whereMobileNoVerifiedAt($value)
 * @method static Builder|Util whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $user_id
 * @property string|null $email_verify_token
 * @property string|null $mobile_no_verify_token
 * @method static Builder|Util whereEmailVerifyToken($value)
 * @method static Builder|Util whereMobileNoVerifyToken($value)
 * @method static Builder|Util whereUserId($value)
 * @property string|null $email_verify_otp
 * @property string|null $mobile_no_verify_otp
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static Builder|Util whereEmailVerifyOtp($value)
 * @method static Builder|Util whereMobileNoVerifyOtp($value)
 * @property string|null $temp_email
 * @method static Builder|Util whereTempEmail($value)
 */
class Util extends Model
{
    use  HasFactory, Notifiable;

//    protected $fillable = ['email_verified', 'mobile_no_verified'];
    protected $guarded = [];

    # Util and user has one to one relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
