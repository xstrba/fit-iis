<?php declare(strict_types=1);

namespace App\Parents;

use Illuminate\Database\Eloquent\Model as FWModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class Model
 *
 * @package App\Parents
 * @property int|string id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
abstract class Model extends FWModel
{
    public const ATTR_ID = 'id';
    public const ATTR_CREATED_AT = 'created_at';
    public const ATTR_UPDATED_AT = 'updated_at';

    /**
     * @param mixed[] $data
     * @return void
     */
    public function compactFill(array $data): void
    {
        // space for default values

        $this->fill($data);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(Str::studly(class_basename($this)));
    }

    /**
     * @return string|int|null
     */
    public function getId()
    {
        return $this->getAttributeValue(self::ATTR_ID);
    }

    /**
     * @return \Illuminate\Support\Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->getAttributeValue(self::ATTR_CREATED_AT) ?: Carbon::now();
    }

    /**
     * @return \Illuminate\Support\Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->getAttributeValue(self::ATTR_UPDATED_AT) ?: Carbon::now();
    }
}
