<?php declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\QuestionSolutionRequestFilter;
use App\Models\File;
use App\Models\QuestionStudent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class QuestionsService
 *
 * @package App\Services
 */
final class QuestionsService
{
    /**
     * @var \App\Services\FileService $fileService
     */
    private FileService $fileService;

    /**
     * GroupService constructor.
     *
     * @param \App\Services\FileService $fileService
     */
    public function __construct(\App\Services\FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @param \App\Models\QuestionStudent $solution
     * @param mixed[] $filesData
     */
    public function saveSolutionFiles(QuestionStudent $solution, array $filesData): void
    {
        /**
         * Get first then delete one by one to also delete files from storage
         */
        $toDelete = $solution->files()->whereNotIn(File::ATTR_ID, \array_map(
            static function (array $item): int {
                return (int)$item[File::ATTR_ID];
            },
            \array_filter(
            $filesData,
            static function (array $item): bool {
                return Arr::exists($item, File::ATTR_ID);
            }
            )
        ))->get();

        foreach ($toDelete as $deleted) {
            $deleted->forceDelete();
        }

        foreach ($filesData as $fileData) {
            if (!Arr::exists($fileData, File::ATTR_ID)) {
                $base64 = $fileData[QuestionSolutionRequestFilter::FIELD_FILE_BASE64];
                $f = \finfo_open();
                $path = 'solutions/' . $solution->getKey() . '/' . ($fileData[File::ATTR_NAME] ?? (string)\time());
                $url = $this->fileService->saveBase64($path, Str::after($base64, ','));
                if ($url) {
                    $file = new File([
                        File::ATTR_FILABLE_TYPE => QuestionStudent::class,
                        File::ATTR_FILABLE_ID => $solution->getKey(),
                    ]);

                    $file->compactFill([
                        File::ATTR_NAME => ($fileData[File::ATTR_NAME] ?? (string)\time()),
                        File::ATTR_MIME_TYPE => \finfo_buffer($f, base64_decode(Str::after($base64, ',')), FILEINFO_MIME_TYPE),
                        File::ATTR_FILE_URL => $url,
                        File::ATTR_SIZE => $fileData[File::ATTR_SIZE] ?? 0,
                        File::ATTR_PATH => $path,
                    ]);

                    $file->save();
                }
            }
        }
    }
}
