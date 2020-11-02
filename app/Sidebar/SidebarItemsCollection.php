<?php declare(strict_types=1);

namespace App\Sidebar;

use Illuminate\Support\Collection;

/**
 * Class SidebarItemsCollection
 *
 * @package App\Sidebar
 */
final class SidebarItemsCollection extends Collection
{
    /**
     * @return \App\Sidebar\SidebarItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
