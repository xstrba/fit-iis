<?php declare(strict_types=1);

namespace App\Contracts\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface HasFileTraitInterface
 *
 * @package App\Contracts\Traits
 */
interface HasFileTraitInterface
{
    public const RELATION_FILES = 'files';

    /**
     * Model using this interface must implement morphMany function
     *
     * @param $related
     * @param $name
     * @param null $type
     * @param null $id
     * @param null $localKey
     * @return mixed
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Check if model is force deleting
     *
     * @return mixed
     */
    public function isForceDeleting();

    /**
     * Model using this interface has many files
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files(): MorphMany;

    /**
     * Model using this trait must implement deleting hook to
     * delete files
     *
     * @param $callback
     * @return mixed
     */
    public static function deleting($callback);
}
