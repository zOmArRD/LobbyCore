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

namespace zomarrd\core\events\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use zomarrd\core\items\ItemsManager;
use zomarrd\core\modules\form\NavigatorForm;
use zomarrd\core\network\player\NetworkPlayer;

final class InteractListener implements Listener
{
    /** @var array */
    private array $itemCountDown;

    public function legacyInteract(PlayerInteractEvent $ev): void
    {
        $player = $ev->getPlayer();
        $item = $ev->getItem();
        $countdown = 1.5;
        $pn = $player->getName();

        if (!$player->isOp()) {
            $ev->setCancelled();
        }

        if (!$player instanceof NetworkPlayer) return;
        if (!isset($this->itemCountDown[$pn]) or time() - $this->itemCountDown[$pn] >= $countdown) {
            switch (true) {
                case $item->equals(ItemsManager::get("item.navigator", $player)):
                    new NavigatorForm($player);
                    break;
            }
            $this->itemCountDown[$pn] = time();
        }
    }
}