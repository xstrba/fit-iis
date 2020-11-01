<?php declare(strict_types=1);

namespace App\Tables\Columns;

/**
 * Class Column
 *
 * @package App\Tables\Columns
 */
final class Column
{
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_TITLE = 'title';
    public const PROPERTY_SORT_FIELD = 'sortField';
    public const PROPERTY_DATA_CLASS = 'dataClass';
    public const PROPERTY_TITLE_CLASS = 'titleClass';
    public const PROPERTY_SEARCH_FIELD = 'searchField';
    public const PROPERTY_WIDTH = 'width';

    /**
     * @var string $name
     */
    private string $name;

    /**
     * @var string $title
     */
    private string $title;

    /**
     * @var bool $sortable
     */
    private bool $sortable;

    /**
     * @var bool $searchable
     */
    private bool $searchable;

    /**
     * @var string|null $class
     */
    private ?string $class;

    /**
     * @var string|null
     */
    private ?string $width;

    /**
     * Column constructor.
     *
     * @param string $name
     * @param string $title
     */
    private function __construct(string $name, string $title)
    {
        $this->name = $name;
        $this->title = $title;
        $this->sortable = false;
        $this->searchable = false;
        $this->class = null;
        $this->width = null;
    }

    /**
     * Initialize column
     *
     * @param string $name
     * @param string $title
     * @return \App\Tables\Columns\Column
     */
    public static function init(string $name, string $title): Column
    {
        return (new self($name, $title));
    }

    /**
     * Make column sortable
     *
     * @return $this
     */
    public function sortable(): self
    {
        $this->sortable = true;
        return $this;
    }

    /**
     * Make column's data centered
     *
     * @return $this
     */
    public function centered(): self
    {
        $this->class = 'text-center';
        return $this;
    }

    /**
     * Make column searchable
     *
     * @return $this
     */
    public function searchable(): self
    {
        $this->searchable = true;
        return $this;
    }

    /**
     * Make column's data aligned right
     *
     * @return $this
     */
    public function right(): self
    {
        $this->class = 'text-right';
        return $this;
    }

    public function width(string $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get column as array
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        $data = [
            self::PROPERTY_NAME => $this->name,
            self::PROPERTY_TITLE => $this->title,
        ];

        if ($this->sortable) {
            $data[self::PROPERTY_SORT_FIELD] = $this->name;
        }

        if ($this->searchable) {
            $data[self::PROPERTY_SEARCH_FIELD] = $this->name;
        }

        if ($this->class) {
            $data[self::PROPERTY_DATA_CLASS] = $this->class;
            $data[self::PROPERTY_TITLE_CLASS] = $this->class;
        }

        if ($this->width) {
            $data[self::PROPERTY_WIDTH] = $this->width;
        }

        return $data;
    }
}
