<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\Repositories\GroupsRepositoryInterface;
use App\Enums\CountriesEnum;
use App\Enums\GendersEnum;
use App\Enums\LanguagesEnum;
use App\Enums\RolesEnum;
use App\Models\Group;
use App\Models\Test;
use App\Parents\RequestFilter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * Class GroupRequestFilter
 *
 * @package App\Http\Requests
 */
final class GroupRequestFilter extends RequestFilter
{
    public const FIELD_NAME = Group::ATTR_NAME;
    public const FIELD_TEST_ID = Group::ATTR_TEST_ID;

    /**
     * @var \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
     */
    private GroupsRepositoryInterface $groupsRepository;

    /**
     * GroupRequestFilter constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     * @param \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    public function __construct(
        Factory $validatorFactory,
        \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository,
        Hasher $hasher
    ) {
        $this->usersRepository = $groupsRepository;
        $this->hasher = $hasher;
        parent::__construct($validatorFactory);
    }

    /**
     * Get validate data from request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Group|null $group
     * @return mixed[]
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request, ?Group $group = null): array
    {
        $data = $request->all();
        $rules = $this->getRules($group);
        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->getFilteredData($data, $rules);
    }

    /**
     * Get validation rules for user request
     *
     * @param \App\Models\Group|null $group
     * @return mixed[]
     */
    private function getRules(?Group $group): array
    {
        $required = $this->getSometimesRequiredRule($group);
        return [
            self::FIELD_NAME => [$required, 'string', 'max:255'],
            self::FIELD_TEST_ID => [$required, 'numeric', 'exists:' . Test::table() . ',id'],
        ];
    }
}
