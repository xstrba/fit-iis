<?php declare(strict_types=1);

namespace App\Enums;

use App\Parents\Enum;
use Monarobase\CountryList\CountryList;

/**
 * Class CountriesEnum
 *
 * @package App\Enums
 */
final class CountriesEnum extends Enum
{
    /**
     * @var \Monarobase\CountryList\CountryList $countryList
     */
    private CountryList $countryList;

    /**
     * CountriesEnum constructor.
     *
     * @param \Monarobase\CountryList\CountryList $countryList
     */
    protected function __construct(CountryList $countryList)
    {
        parent::__construct();
        $this->countryList = $countryList;
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        return \array_keys($this->getList());
    }

    /**
     * @return \App\Enums\CountriesEnum
     */
    public static function instance(): CountriesEnum
    {
        return new self(new CountryList());
    }

    /**
     * @param string $locale
     * @return string[]
     */
    public function getList(string $locale = 'cs'): array
    {
        $countries = $this->countryList->getList($locale);
        foreach (['XA', 'XB'] as $toRemove) {
            if (isset($countries[$toRemove])) {
                unset($countries[$toRemove]);
            }
        }
        return $countries;
    }
}
