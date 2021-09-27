<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 26/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\events\listener;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use ReflectionClass;
use ReflectionException;
use zomarrd\core\network\Network;

final class LPlayer implements Listener
{
    /** @var array  */
    private array $login, $join, $move;

    /**
     * @throws ReflectionException
     */
    public function onDataReceive(DataPacketReceiveEvent $ev): void
    {
        $pk = $ev->getPacket();
        $pl = $ev->getPlayer();

        switch (true) {
            case $pk instanceof LoginPacket:
                $class = new ReflectionClass($pl);

                /* Check if the player has the true ip and not the proxy.  */
                if (isset($pk->clientData["Waterdog_IP"])) {
                    $property = $class->getProperty("ip");
                    $property->setAccessible(true);
                    $property->setValue($pl, $pk->clientData["Waterdog_IP"]);
                }

                /* Check the XUID of the proxied player. */
                if (isset($pk->clientData["Waterdog_XUID"])) {
                    $property = $class->getProperty("xuid");
                    $property->setAccessible(true);
                    $property->setValue($pl, $pk->clientData["Waterdog_XUID"]);
                    $pk->xuid = $pk->clientData["Waterdog_XUID"];
                }
                break;
            case $pk instanceof EmotePacket:
                $emoteId = $pk->getEmoteId();
                (new Network())->getServerPM()->broadcastPacket($pl->getViewers(), EmotePacket::create($pl->getId(), $emoteId, 1 << 0));
                break;
        }
    }
}