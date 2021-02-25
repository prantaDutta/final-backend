<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $role
 * @property string $verified
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRole($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereVerified($value)
 * @mixin Eloquent
 * @property-read Verification|null $verification
 * @property-read Collection|Loan[] $loans
 * @property-read int|null $loans_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property float|null $balance
 * @method static Builder|User whereBalance($value)
 * @property string $language
 * @method static Builder|User whereLanguage($value)
 * @property-read Util|null $util
 * @property int|null $mobile_no
 * @method static Builder|User whereMobileNo($value)
 * @property Carbon|null $mobile_no_verified_at
 * @method static Builder|User whereMobileNoVerifiedAt($value)
 * @property-read \App\Models\LoanPreference|null $loan_preference
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var array
//     */
//    protected $fillable = [
//        'name',
//        'email',
//        'role',
//    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
//    protected $hidden = [
//        'password',
//        'remember_token',
//    ];

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_no_verified_at' => 'datetime',
    ];

    # User and Verification have a one to one relation
    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    # User and Loan have a many to many relation
    public function loans()
    {
        return $this->belongsToMany(Loan::class);
    }

    # User and Transaction have a one to many relation
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    # User and Util have a one to one relation
    public function util()
    {
        return $this->hasOne(Util::class);
    }

    # User and Loan Preference have a one to one relation
    public function loan_preference()
    {
        return $this->hasOne(LoanPreference::class);
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @param Notification $notification
     * @return string
     */
    public function routeNotificationForNexmo($notification)
    {
        return $this->mobile_no;
    }

    // This one is for sending alternative email to user
//    public function routeNotificationForMail()
//    {
//        if (request()->has('email_override') && request()->has('email')) {
//            return request()->input('email');
//        }
//
//        return $this->email;
//    }
}
