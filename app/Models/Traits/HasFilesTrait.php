<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasFilesTrait
 *
 * @package App\Models\Traits
 */
trait HasFilesTrait
{
    /**
     * @param $related
     * @param $name
     * @param null $type
     * @param null $id
     * @param null $localKey
     * @return mixed
     */
    abstract public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * @param mixed $callback
     * @return mixed
     */
    abstract public static function deleting($callback);

    /**
     * Check if model is force deleting
     *
     * @return mixed
     */
    abstract public function isForceDeleting();

    /**
     * Model using this trait has many files
     *
     * @return MorphMany
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, File::MORPH_FILABLE);
    }

    /**
     * Boot trait when booting model
     *
     * @return void
     */
    protected static function bootHasFilesTrait(): void
    {
        static::deleting(/**
         * @param \App\Models\Traits\HasFilesTrait $model
         */ function ($model): void {
            if ($model->isForceDeleting()) {
                $model->files()->forceDelete();
            }
        });
    }
}
