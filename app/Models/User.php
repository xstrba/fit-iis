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
 * @property string first_name
 * @property string|null last_name
 * @property string email
 * @property string nickname
 * @property int role
 * @propert string name
 * @property \Illuminate\Support\Carbon email_verified_at
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
        self::ATTR_CREATED_AT => 'datetime:Y-m-d H:i:s',
        self::ATTR_UPDATED_AT => 'datetime:Y-m-d H:i:s',
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
