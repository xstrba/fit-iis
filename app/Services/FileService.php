<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\FilesystemManager;

/**
 * Class FileService
 *
 * @package App\Services
 */
final class FileService
{
    /**
     * @var \Illuminate\Filesystem\FilesystemManager $fileSystem
     */
    private FilesystemManager $fileSystem;

    /**
     * @var \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * FileService constructor.
     *
     * @param \Illuminate\Filesystem\FilesystemManager $fileSystem
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    public function __construct(FilesystemManager $fileSystem, UrlGenerator $urlGenerator)
    {
        $this->fileSystem = $fileSystem;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Store file in storage and return url rendering file
     *
     * @param string $path
     * @param string $base64
     * @param string $disk
     * @return string|null
     */
    public function saveBase64(string $path, string $base64, string $disk = 'local'): ?string
    {
        if (!$this->fileSystem->disk($disk)->put($path, $base64)) {
            return null;
        }

        return $this->urlGenerator->route('media.show', $path);
    }
}
