<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 29/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\items;

use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use zomarrd\core\network\player\NetworkPlayer;

final class ItemsManager
{
    /**+
     * @param int    $itemId
     * @param string $customName
     *
     * @return Item
     */
    static public function load(int $itemId, string $customName): Item
    {
        return ItemFactory::get($itemId)->setCustomName($customName);
    }

    /**
     * @param string        $itemId
     * @param NetworkPlayer $player
     *
     * @return Item
     */
    static public function get(string $itemId, NetworkPlayer $player): Item
    {
        return match ($itemId) {
            "item.navigator" => self::load(ItemIds::COMPASS, $player->getLangTranslated("item.navigator")),
            "item.settings" => self::load(ItemIds::MOB_HEAD, $player->getLangTranslated("item.settings")),
            "item.cosmetics" => self::load(BlockIds::ENDER_CHEST, $player->getLangTranslated("item.cosmetics")),
            default => Item::get(BlockIds::AIR),
        };
    }
}