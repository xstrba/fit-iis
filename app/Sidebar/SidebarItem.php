<?php declare(strict_types=1);

namespace App\Sidebar;

/**
 * Class SidebarItem
 *
 * @package App\Sidebar
 */
final class SidebarItem
{
    /**
     * @var string $key
     */
    private string $key;

    /**
     * @var string $icon
     */
    private string $icon;

    /**
     * @var string $link
     */
    private string $link;

    /**
     * @var bool $isActive
     */
    private bool $isActive;

    /**
     * SidebarItem constructor.
     *
     * @param string $key
     * @param string $icon
     * @param string $link
     * @param bool $isActive
     */
    public function __construct(string $key, string $icon, string $link, bool $isActive = false)
    {
        $this->key = $key;
        $this->icon = $icon;
        $this->link = $link;
        $this->isActive = $isActive;
    }

    /**
     * Set icon of sidebar item
     *
     * @param string $icon
     * @return $this
     */
    public function setIcon(string $icon): SidebarItem
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set link of sidebar item
     *
     * @param string $link
     * @return $this
     */
    public function setLink(string $link): SidebarItem
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Set if sidebar item is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setActive(bool $isActive = true): SidebarItem
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get sidebar item key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get sidebar item key for translation
     *
     * @return string
     */
    public function getTrKey(): string
    {
        return 'sidebar.' . $this->getKey();
    }

    /**
     * Get sidebar item icon
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get sidebar item link
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Get if sidebar is active
     *
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->isActive;
    }
}
