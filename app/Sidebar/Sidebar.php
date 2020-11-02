<?php declare(strict_types=1);

namespace App\Sidebar;

/**
 * Class Sidebar
 *
 * @package App\Sidebar
 */
final class Sidebar
{
    /**
     * @var \App\Sidebar\SidebarItemsCollection $items
     */
    private SidebarItemsCollection $items;

    /**
     * Sidebar constructor.
     */
    public function __construct()
    {
        $this->items = new SidebarItemsCollection();
    }

    /**
     * @param \App\Sidebar\SidebarItem $item
     */
    public function addItem(SidebarItem $item): void
    {
        $this->items->push($item);
    }

    /**
     * @return \App\Sidebar\SidebarItem[]
     */
    public function getItems(): array
    {
        return $this->getCollection()->getItems();
    }

    /**
     * @return \App\Sidebar\SidebarItemsCollection
     */
    public function getCollection(): SidebarItemsCollection
    {
        return $this->items;
    }
}
