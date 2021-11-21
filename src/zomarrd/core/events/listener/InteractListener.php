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

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use zomarrd\core\items\ItemsManager;
use zomarrd\core\modules\form\CosmeticsForm;
use zomarrd\core\modules\form\NavigatorForm;
use zomarrd\core\modules\form\SettingsForm;
use zomarrd\core\modules\form\UHCSelectorForm;
use zomarrd\core\modules\npc\entity\HumanEntity;
use zomarrd\core\modules\npc\Human;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;

final class InteractListener implements Listener
{
    /** @var array */
    private array $itemCountDown, $hitNpc;

    public function legacyInteract(PlayerInteractEvent $ev): void
    {
        $player = $ev->getPlayer();

        if (!$player->isOp()) $ev->setCancelled();
    }

    public function interactHuman(DataPacketReceiveEvent $event)
    {
        $player = $event->getPlayer();
        $pk = $event->getPacket();

        if (!$player instanceof NetworkPlayer) return;
        if (!$pk instanceof InventoryTransactionPacket) return;

        if ($pk->trData instanceof UseItemTransactionData) {
            switch ($pk->trData->getActionType()) {
                case UseItemTransactionData::ACTION_CLICK_AIR:
                case UseItemTransactionData::ACTION_CLICK_BLOCK:
                case UseItemOnEntityTransactionData::ACTION_INTERACT:
                    $item = $player->getInventory()->getItemInHand();
                    $countdown = 1.5;
                    if (!isset($this->itemCountDown[$player->getName()]) or time() - $this->itemCountDown[$player->getName()] >= $countdown) {
                        switch (true) {
                            case $item->equals(ItemsManager::get("item.navigator", $player)):
                                new NavigatorForm($player);
                                break;
                            case $item->equals(ItemsManager::get("item.cosmetics", $player)):
                                new CosmeticsForm($player);
                                break;
                            case $item->equals(ItemsManager::get("item.settings", $player)):
                                new SettingsForm($player);
                                break;
                        }
                        $this->itemCountDown[$player->getName()] = time();
                    }
                    break;
            }
        } elseif ($pk->trData instanceof UseItemOnEntityTransactionData) switch ($pk->trData->getActionType()) {
            case UseItemOnEntityTransactionData::ACTION_ITEM_INTERACT:
            case UseItemOnEntityTransactionData::ACTION_ATTACK:
            case UseItemOnEntityTransactionData::ACTION_INTERACT:
                $target = $player->level->getEntity($pk->trData->getEntityRuntimeId());
                if (!$target instanceof HumanEntity) return;
                $timeToNexHit = 2;
                $server = Human::getId($target);
                if (!isset($this->hitNpc[$player->getName()]) or time() - $this->hitNpc[$player->getName()] >= $timeToNexHit) {
                    $config = (new Network())->getResourceManager()->getArchive("network.data.yml");
                    try {
                        foreach ($config->get("servers.available") as $serverData) {
                            if ($server == $serverData['npc.id']) {
                                if (!$serverData['category'] == "uhc") {
                                    $player->transferServer($serverData['server.name']);
                                } else {
                                    new UHCSelectorForm($player);
                                }
                            }
                        }
                    } catch (Exception $ex) {
                        if ($player->isOp()) $player->sendMessage("Error in line: {$ex->getLine()}, File: {$ex->getFile()} \n Error: {$ex->getMessage()}");
                    }
                    $this->hitNpc[$player->getName()] = time();
                }
                break;
        }
    }
}