<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Models\User;
use App\Queries\UsersQueryBuilder;
use Illuminate\Support\Collection;

/**
 * Class UsersRepository
 *
 * @package App\Repositories
 */
final class UsersRepository implements UsersRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function get(int $id): User
    {
        /** @var User $user */
        $user = $this->query()->getById($id);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return $this->query()->get();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): User
    {
        $user = new User();
        $user->compactFill($data);
        $this->save($user);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function save(User $user): void
    {
        $user->save();
    }

    /**
     * @inheritDoc
     */
    public function update(array $data, User $user): void
    {
        $user->compactFill($data);
        $this->save($user);
    }

    /**
     * @inheritDoc
     */
    public function delete(User $user): void
    {
        if ($user->deleted_at) {
            $user->forceDelete();
        } else {
            $user->delete();
        }
    }

    /**
     * @inheritDoc
     */
    public function restore(User $user): void
    {
        $user->restore();
        $this->save($user);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email): ?User
    {
        /** @var User|null $user */
        $user = $this->query()->whereEmail($email)->getFirst();
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function findByNickname(string $nickname): ?User
    {
        /** @var User|null $user */
        $user = $this->query()->whereNickname($nickname)->getFirst();
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function query(): UsersQueryBuilder
    {
        return (new UsersQueryBuilder(new User()));
    }
}
