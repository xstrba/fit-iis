<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Parents\FrontEndController;
use App\Services\FileService;

/**
 * Class MediaController
 *
 * @package App\Http\Controllers
 */
final class MediaController extends FrontEndController
{
    public function show(FileService $fileService, string $path)
    {
    }
}
