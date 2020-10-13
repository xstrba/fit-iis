<?php declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use App\Parents\QueryBuilder;

/**
 * Class UsersQueryBuilder
 *
 * @package App\Queries
 */
final class UsersQueryBuilder extends QueryBuilder
{
    /**
     * Filter records for first name
     *
     * @param string $firstName
     * @return \App\Queries\UsersQueryBuilder
     */
    public function whereFirstName(string $firstName): UsersQueryBuilder
    {
        return $this->where(User::ATTR_FIRST_NAME, $firstName);
    }

    /**
     * Filter records for last name
     *
     * @param string $lastName
     * @return \App\Queries\UsersQueryBuilder
     */
    public function whereLastName(string $lastName): UsersQueryBuilder
    {
        return $this->where(User::ATTR_LAST_NAME, $lastName);
    }

    /**
     * Filter records for last name
     *
     * @param string $email
     * @return \App\Queries\UsersQueryBuilder
     */
    public function whereEmail(string $email): UsersQueryBuilder
    {
        return $this->where(User::ATTR_EMAIL, $email);
    }

    /**
     * Filter records for nickname
     *
     * @param string $nickname
     * @return \App\Queries\UsersQueryBuilder
     */
    public function whereNickname(string $nickname): UsersQueryBuilder
    {
        return $this->where(User::ATTR_NICKNAME, $nickname);
    }

    /**
     * Filter records for role
     *
     * @param int $role
     * @param string $cmp
     * @return \App\Queries\UsersQueryBuilder
     */
    public function whereRole(int $role, string $cmp = '=='): UsersQueryBuilder
    {
        return $this->where(User::ATTR_ROLE, $cmp, $role);
    }

    /**
     * Filter items by email and nickname
     *
     * @param string $email email or nickname
     * @return \App\Queries\UsersQueryBuilder
     */
    public function whereEmailOrNickname(string $email): UsersQueryBuilder
    {
        return $this->where(User::ATTR_EMAIL, $email)->orWhere(User::ATTR_NICKNAME, $email);
    }

    /**
     * @param string $column
     * @param string $direction
     * @return \App\Queries\UsersQueryBuilder
     */
    public function sortByColumn(string $column, string $direction): UsersQueryBuilder
    {
        return $this->orderBy($column, $direction);
    }
}
