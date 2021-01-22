<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Verification newModelQuery()
 * @method static Builder|Verification newQuery()
 * @method static Builder|Verification query()
 * @method static Builder|Verification whereAddress($value)
 * @method static Builder|Verification whereBorrowerType($value)
 * @method static Builder|Verification whereCreatedAt($value)
 * @method static Builder|Verification whereDateOfBirth($value)
 * @method static Builder|Verification whereDocumentType($value)
 * @method static Builder|Verification whereGender($value)
 * @method static Builder|Verification whereId($value)
 * @method static Builder|Verification whereMobileNo($value)
 * @method static Builder|Verification whereUpdatedAt($value)
 * @method static Builder|Verification whereUserId($value)
 * @method static Builder|Verification whereVerificationPhotos($value)
 * @method static Builder|Verification whereZila($value)
 * @method static Builder|Verification whereZipCode($value)
 * @mixin Eloquent
 * @property string $division
 * @method static Builder|Verification whereDivision($value)
 */
class Verification extends Model
{
    use HasFactory;

    protected $guarded = [];

    # this is for fetching the user with verification
    # verification and user has one to one relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
