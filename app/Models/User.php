<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;
use App\Queries\UsersQueryBuilder;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @package App\Models
 * @property string $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string $nickname
 * @property int $role
 * @property \Illuminate\Support\Carbon birth
 * @property string $street
 * @property string $house_number
 * @property string $city
 * @property string $country
 * @property string $gender
 * @property string $phone
 * @property string $name
 * @property string $language
 * @property \Illuminate\Support\Carbon $email_verified_at
 */
final class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, Notifiable;

    /**
     * Attributes
     */
    public const ATTR_FIRST_NAME = 'first_name';
    public const ATTR_LAST_NAME = 'last_name';
    public const ATTR_EMAIL = 'email';
    public const ATTR_NICKNAME = 'nickname';
    public const ATTR_ROLE = 'role';
    public const ATTR_EMAIL_VERIFIED_AT = 'email_verified_at';
    public const ATTR_PASSWORD = 'password';
    public const ATTR_REMEMBER_TOKEN = 'remember_token';
    public const ATTR_BIRTH = 'birth';
    public const ATTR_STREET = 'street';
    public const ATTR_HOUSE_NUMBER = 'house_number';
    public const ATTR_CITY = 'city';
    public const ATTR_COUNTRY = 'country';
    public const ATTR_GENDER = 'gender';
    public const ATTR_PHONE = 'phone';
    public const ATTR_LANGUAGE = 'language';

    /**
     * Appended attributes
     */
    public const ATTR_NAME = 'name';

    /**
     * Attributes that are mass assignable
     *
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_FIRST_NAME,
        self::ATTR_LAST_NAME,
        self::ATTR_EMAIL,
        self::ATTR_EMAIL_VERIFIED_AT,
        self::ATTR_NICKNAME,
        self::ATTR_ROLE,
        self::ATTR_PASSWORD,
        self::ATTR_BIRTH,
        self::ATTR_STREET,
        self::ATTR_HOUSE_NUMBER,
        self::ATTR_CITY,
        self::ATTR_COUNTRY,
        self::ATTR_GENDER,
        self::ATTR_PHONE,
        self::ATTR_LANGUAGE,
    ];

    /**
     * Attributes that are hidden from json or user model
     *
     * @var string[] $hidden
     */
    protected $hidden = [
        self::ATTR_PASSWORD,
        self::ATTR_REMEMBER_TOKEN,
    ];

    /**
     * Attributes appended to model
     *
     * @var string[] $appends
     */
    protected $appends = [
        self::ATTR_NAME,
    ];

    /**
     * Castings of attributes
     *
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_FIRST_NAME => 'string',
        self::ATTR_LAST_NAME => 'string',
        self::ATTR_EMAIL => 'string',
        self::ATTR_EMAIL_VERIFIED_AT => 'datetime',
        self::ATTR_NICKNAME => 'string',
        self::ATTR_ROLE => 'int',
        self::ATTR_BIRTH => 'datetime:Y-m-d',
        self::ATTR_STREET => 'string',
        self::ATTR_HOUSE_NUMBER => 'string',
        self::ATTR_CITY => 'string',
        self::ATTR_COUNTRY => 'string',
        self::ATTR_GENDER => 'string',
        self::ATTR_PHONE => 'string',
        self::ATTR_LANGUAGE => 'string',
    ];

    /**
     * Get query builder for model
     *
     * @return \App\Models\User|\App\Queries\UsersQueryBuilder|\Illuminate\Database\Eloquent\Builder
     */
    public function newModelQuery()
    {
        return new UsersQueryBuilder($this);
    }

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
