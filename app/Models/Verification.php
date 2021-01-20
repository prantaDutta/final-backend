<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Verification
 *
 * @property int $id
 * @property int $user_id
 * @property string $date_of_birth
 * @property string $gender
 * @property string $address
 * @property int $mobile_no
 * @property string $document_type
 * @property string $borrower_type
 * @property string $zila
 * @property int $zip_code
 * @property mixed $verification_photos
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Verification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Verification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Verification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereBorrowerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereMobileNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereVerificationPhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereZila($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereZipCode($value)
 * @mixin \Eloquent
 * @property string $division
 * @method static \Illuminate\Database\Eloquent\Builder|Verification whereDivision($value)
 */
class Verification extends Model
{
    use HasFactory;

    protected $guarded =[];

    # this is for fetching the user with verification
    # verification and user has one to one relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
