<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;
use App\Queries\FileQueryBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class File
 *
 * @property string $filable_type
 * @property int $filable_id
 * @property string $file_url
 * @property string $mime_type
 * @property string $name
 * @property int $size
 * @property string $path
 * @package App\Models
 */
final class File extends Model
{
    public const ATTR_FILABLE_TYPE = 'filable_type';
    public const ATTR_FILABLE_ID = 'filable_id';
    public const ATTR_FILE_URL = 'file_url';
    public const ATTR_MIME_TYPE = 'mime_type';
    public const ATTR_NAME = 'name';
    public const ATTR_SIZE = 'size';
    public const ATTR_PATH = 'path';

    public const MORPH_FILABLE = 'filable';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_FILABLE_TYPE,
        self::ATTR_FILABLE_ID,
        self::ATTR_FILE_URL,
        self::ATTR_MIME_TYPE,
        self::ATTR_NAME,
        self::ATTR_SIZE,
        self::ATTR_PATH,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_FILABLE_TYPE => 'string',
        self::ATTR_FILABLE_ID => 'int',
        self::ATTR_FILE_URL => 'string',
        self::ATTR_MIME_TYPE => 'string',
        self::ATTR_NAME => 'string',
        self::ATTR_SIZE => 'int',
        self::ATTR_PATH => 'string',
    ];

    /**
     * Set delete file event handler
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::deleting(static function (File $file): void {
            if ($file->isForceDeleting()) {
                Storage::disk('local')->delete($file->path);
            }
        });
    }

    /**
     * Get query builder for model
     *
     * @return \App\Queries\FileQueryBuilder
     */
    public function newModelQuery(): FileQueryBuilder
    {
        return new FileQueryBuilder($this);
    }

    /**
     * File morphs to many different models
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function filable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
