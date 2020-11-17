<?php declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\GroupRequestFilter;
use App\Models\File;
use App\Models\Group;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class GroupService
 *
 * @package App\Services
 */
final class GroupService
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
     * Save array of questions data
     *
     * @param \App\Models\Group $group
     * @param mixed[] $questionsData
     */
    public function saveQuestions(Group $group, array $questionsData): void
    {
        $savedQuestionIds = [];

        foreach ($questionsData as $questionData) {
            if (\is_array($questionData)) {
                if (Arr::exists($questionData, Question::ATTR_ID)) {
                    /** @var Question $question */
                    $question = $group->questions()->findOrFail($questionData[Question::ATTR_ID]);
                } else {
                    /** @var Question $question */
                    $question = $group->questions()->newModelInstance([
                        Question::ATTR_GROUP_ID => $group->getKey(),
                    ]);
                }

                $question->compactFill($questionData);
                $question->save();

                if (Arr::exists($questionData, Question::RELATION_FILES) && \is_array($questionData[Question::RELATION_FILES])) {
                    $this->saveQuestionFiles($question, $questionData[Question::RELATION_FILES]);
                }

                if (Arr::exists($questionData, Question::RELATION_OPTIONS) && \is_array($questionData[Question::RELATION_OPTIONS])) {
                    $this->saveOptions($question, $questionData[Question::RELATION_OPTIONS]);
                }

                $savedQuestionIds[] = $question->getKey();
            }
        }

        /** @var Question[] $toDelete */
        $toDelete = $group->questions()->whereNotIn(Question::ATTR_ID, $savedQuestionIds)->get();
        foreach ($toDelete as $deleteQuestion) {
            $deleteQuestion->forceDelete();
        }
    }

    /**
     * Save files of question
     *
     * @param \App\Models\Question $question
     * @param mixed[] $filesData
     */
    private function saveQuestionFiles(Question $question, array $filesData): void
    {
        $savedFileIds = [];

        foreach ($filesData as $fileData) {
            if (Arr::exists($fileData, File::ATTR_ID)) {
                $savedFileIds[] = $fileData[File::ATTR_ID];
            } else if (Arr::exists($fileData, GroupRequestFilter::FIELD_FILE_BASE64))  {
                $base64 = $fileData[GroupRequestFilter::FIELD_FILE_BASE64];
                $f = \finfo_open();
                $path = 'questions/' . $question->getKey() . '/' . ($fileData[File::ATTR_NAME] ?? (string)\time());
                $url = $this->fileService->saveBase64($path, Str::after($base64, ','));
                if ($url) {
                    $file = new File([
                        File::ATTR_FILABLE_TYPE => Question::class,
                        File::ATTR_FILABLE_ID => $question->getKey(),
                    ]);
                    $file->compactFill([
                        File::ATTR_NAME => ($fileData[File::ATTR_NAME] ?? (string)\time()),
                        File::ATTR_MIME_TYPE => \finfo_buffer($f, base64_decode(Str::after($base64, ',')), FILEINFO_MIME_TYPE),
                        File::ATTR_FILE_URL => $url,
                        File::ATTR_SIZE => $fileData[File::ATTR_SIZE] ?? 0,
                        File::ATTR_PATH => $path,
                    ]);
                    $file->save();
                    $savedFileIds[] = $file->getKey();
                }
            }
        }

        /** @var File[] $toDelete */
        $toDelete = $question->files()->whereNotIn(File::ATTR_ID, $savedFileIds)->get();
        foreach ($toDelete as $fileToDelete) {
            $fileToDelete->forceDelete();
        }
    }

    /**
     * Save question options
     *
     * @param \App\Models\Question $question
     * @param array $optionsData
     */
    private function saveOptions(Question $question, array $optionsData): void
    {
        $savedOptionIds = [];

        foreach ($optionsData as $optionData) {
            if (Arr::exists($optionData, Option::ATTR_ID)) {
                /** @var Option $option */
                $option = $question->options()->findOrFail($optionData[Option::ATTR_ID]);
            } else {
                $option = new Option([
                    Option::ATTR_QUESTION_ID => $question->getKey(),
                ]);
            }

            $option->compactFill($optionData);
            $option->save();
            $savedOptionIds[] = $option->getKey();
        }

        $question->options()->whereNotIn(Option::ATTR_ID, $savedOptionIds)->forceDelete();
    }
}
