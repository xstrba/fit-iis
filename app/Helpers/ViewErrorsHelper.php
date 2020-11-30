<?php declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\ViewErrorBag;

/**
 * Class ViewErrorsHelper
 *
 * @package App\Helpers
 */
final class ViewErrorsHelper
{
    /**
     * Check if error bag has one of given errors
     *
     * @param \Illuminate\Support\ViewErrorBag $errors
     * @param string[] $keys
     * @return bool
     */
    public static function hasOne(ViewErrorBag $errors, array $keys): bool
    {
        foreach ($keys as $key) {
            if ($errors->has($key)) {
                return true;
            }
        }
        return false;
    }
}
