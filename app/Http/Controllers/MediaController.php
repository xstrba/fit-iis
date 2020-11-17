<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\File;
use App\Parents\FrontEndController;
use App\Queries\FileQueryBuilder;
use App\Services\FileService;
use Illuminate\Support\Str;

/**
 * Class MediaController
 *
 * @package App\Http\Controllers
 */
final class MediaController extends FrontEndController
{
    /**
     * @param \App\Services\FileService $fileService
     * @param string $path
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function show(FileService $fileService, string $path): \Illuminate\Http\Response
    {
        /** @var File $file */
        $file = $this->fileQuery()->wherePath($path)->firstOrFail();
        $response = $this->responseFactory->make($fileService->get($file->path), 200);
        $response->header("Content-Type", $file->mime_type);
        return $response;
    }

    /**
     * @return \App\Queries\FileQueryBuilder
     */
    private function fileQuery(): FileQueryBuilder
    {
        return new FileQueryBuilder(new File());
    }
}
