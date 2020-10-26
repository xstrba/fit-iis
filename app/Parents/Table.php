<?php declare(strict_types=1);

namespace App\Parents;

use App\Tables\Columns\Column;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;

/**
 * Class Table
 *
 * @package App\Parents
 */
abstract class Table
{
    protected const QUERY_PARAM_SEARCH = 'search';
    protected const QUERY_PARAM_SORT = 'sort';
    protected const QUERY_SORT_DELIMITER = ',';
    protected const QUERY_SORT_TYPE_DELIMITER = '|';

    /**
     * @var int $perPage
     */
    protected int $perPage = 15;

    /**
     * @var string[][] $columns
     */
    protected array $columns = [];

    /**
     * @var \Illuminate\Contracts\Translation\Translator $translator
     */
    protected Translator $translator;

    /**
     * @var \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    protected UrlGenerator $urlGenerator;

    /**
     * @var string|null $defaultSort
     */
    protected ?string $defaultSort = null;

    /**
     * @var string $defaultSortDirection
     */
    protected string $defaultSortDirection = 'asc';

    /**
     * Table constructor.
     *
     * @param \Illuminate\Contracts\Translation\Translator $translator
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    public function __construct(
        \Illuminate\Contracts\Translation\Translator $translator,
        \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
    ) {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->initializeColumns();
    }

    /**
     * Initialize table
     *
     * @return \App\Parents\Table
     */
    public function initialize(): Table
    {
        $this->initializeColumns();
        return $this;
    }

    /**
     * Get number of items per page
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get columns
     *
     * @return \string[][]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return string
     * @throws \JsonException
     */
    public function getColumnsJson(): string
    {
        return \json_encode($this->getColumns(), JSON_THROW_ON_ERROR);
    }

    /**
     * Get sort columns from request
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getSortColumns(Request $request): array
    {
        $sortString = $request->get(self::QUERY_PARAM_SORT);
        if (!$sortString) {
            return [
                [
                    $this->defaultSort,
                    $this->defaultSortDirection,
                ],
            ];
        }

        $columns = \array_map(static function (array $column): string {
            return $column[Column::PROPERTY_NAME];
        }, $this->getColumns());

        try {
            return \array_filter(
                \array_map(
                    static function (string $sort): array {
                        return \explode(self::QUERY_SORT_TYPE_DELIMITER, $sort);
                    },
                    \explode(self::QUERY_SORT_DELIMITER, $sortString)
                ),
                static function (array $sort) use ($columns): bool {
                    return \in_array($sort[0], $columns, true);
                }
            );
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return string[]
     */
    public function getSearchColumns(): array
    {
        return \array_map(
            static function (array $item): string {
                return $item[Column::PROPERTY_SEARCH_FIELD];
            },
            \array_filter($this->getColumns(), static function (array $item): bool {
                return (bool)($item[Column::PROPERTY_SEARCH_FIELD] ?? false);
            })
        );
    }

    /**
     * Get api endpoint from which table gets data
     *
     * @return string
     */
    abstract public function getBaseApiUrl(): string;

    /**
     * Get table data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    abstract public function getData(Request $request): LengthAwarePaginator;

        /**
     * Columns definitions
     *
     * @return void
     */
    abstract protected function initializeColumns(): void;

    /**
     * Add column to columns
     *
     * @param \App\Tables\Columns\Column $column
     */
    protected function addColumn(Column $column): void
    {
        $this->columns[] = $column->getData();
    }
}
