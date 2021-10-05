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

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use ReflectionClass;
use ReflectionException;
use zomarrd\core\modules\floatingtext\FloatingTextManager;
use zomarrd\core\modules\mysql\AsyncQueue;
use zomarrd\core\modules\mysql\query\InsertQuery;
use zomarrd\core\modules\mysql\query\SelectQuery;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\session\Session;
use const zOmArRD\Spawn_Data;

final class LPlayer implements Listener
{
    /** @var array */
    private array $login, $join, $move;

    public function getNetwork(): Network
    {
        return new Network();
    }

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
                /*if (isset($pk->clientData["Waterdog_XUID"])) {
                    $property = $class->getProperty("xuid");
                    $property->setAccessible(true);
                    $property->setValue($pl, $pk->clientData["Waterdog_XUID"]);
                    $pk->xuid = $pk->clientData["Waterdog_XUID"];
                }*/
                break;
            case $pk instanceof EmotePacket:
                $emoteId = $pk->getEmoteId();
                (new Network())->getServerPM()->broadcastPacket($pl->getViewers(), EmotePacket::create($pl->getId(), $emoteId, 1 << 0));
                break;
        }
    }

    public function onPlayerCreation(PlayerCreationEvent $ev): void
    {
        $ev->setPlayerClass(NetworkPlayer::class);
    }

    /**
     * @todo finalize someone functions.
     *
     * @param PlayerPreLoginEvent $ev
     */
    public function onPreLogin(PlayerPreLoginEvent $ev): void
    {
        $player = $ev->getPlayer();

        if (!$player instanceof NetworkPlayer) return;

        $pn = $player->getName();
        $player->setLangSession();
        $player->setScoreboardSession();
        /*TODO*/

        AsyncQueue::submitQuery(new SelectQuery("SELECT * FROM settings WHERE player='$pn';"), function ($result) use ($pn) {
            $lang = "eng";
            if (sizeof($result) === 0) {
                AsyncQueue::submitQuery(new InsertQuery("INSERT INTO settings(player, language, scoreboard) VALUES ('$pn', '$lang', 1);"));
            }
        });
    }

    public function onLoging(PlayerLoginEvent $ev): void
    {
        $player = $ev->getPlayer();

        if (!$player instanceof NetworkPlayer) return;

        $pn = $player->getName();

        AsyncQueue::submitQuery(new SelectQuery("SELECT * FROM settings WHERE player='$pn';"), function ($result) use ($player, $pn) {
            if (sizeof($result) === 0) {
                $player->kick(TextFormat::RED . "Join again to the server!");
            }
            Session::$playerSettings[$pn] = $result[0];
            $player->getLangSession()->apply();
        });

        $this->login[$pn] = 1;
    }

    public function onPlayerJoin(PlayerJoinEvent $ev): void
    {
        $ev->setJoinMessage(null);
        $player = $ev->getPlayer();
        $player->setImmobile();

        if (!$player instanceof NetworkPlayer) return;
        $pn = $player->getName();

        if ($player->hasPermission("lobby.fly")) $player->setAllowFlight(true);
        $player->teleportToLobby();

        if (isset($this->login[$pn])) {
            unset($this->login[$pn]);
            $this->join[$pn] = 1;
        }

        $player->showScreenAnimation(28);
        $player->sendTitle("§l§6Greek §fNetwork", "§fwelcome §6{$player->getName()}", 20, 30, 20);
        new FloatingTextManager($player);
    }

    public function onPlayerQuit(PlayerQuitEvent $ev): void
    {
        $ev->setQuitMessage(null);
    }

    public function onExhaust(PlayerExhaustEvent $ev): void
    {
        $ev->setCancelled();
    }

    /**
     * @param PlayerMoveEvent $ev
     */
    public function onPlayerMove(PlayerMoveEvent $ev): void
    {
        $player = $ev->getPlayer();

        if (!$player instanceof NetworkPlayer) return;
        $pn = $player->getName();

        if (isset($this->login[$pn]) || isset($this->move[$pn])) $ev->setCancelled();

        if (isset($this->join[$pn])) {
            unset($this->join[$pn]);
            $this->move[$pn] = 1;
            return;
        }

        if (isset($this->move[$pn])) {
            $player->setImmobile(false);
            unset($this->move[$pn]);
        }

        if (Spawn_Data['is.enabled']) {
            if ($player->getY() <= Spawn_Data['world.void.minimum']) {
                $player->teleport(new Position(Spawn_Data['pos.x'], Spawn_Data['pos.y'], Spawn_Data['pos.z'], $this->getNetwork()->getServerPM()->getLevelByName(Spawn_Data['world.name'])), Spawn_Data['player.yaw'], Spawn_Data['player.pitch']);
            }
        }
    }

    public function preventSlotChange(InventoryTransactionEvent $ev): void
    {
        $player = $ev->getTransaction()->getSource();

        if (Spawn_Data['is.enabled']) {
            $level = Spawn_Data['world.name'];
        } else {
            $level = $this->getNetwork()->getServerPM()->getDefaultLevel()->getName();
        }

        if ($player->getLevel()->getName() === $level) {
            if (!$player->isOp()) $ev->setCancelled();
        }
    }

    public function preventPlayerDamage(EntityDamageEvent $ev): void
    {
        $ev->setCancelled();
    }

    public function preventBreak(BlockBreakEvent $ev): void
    {
        $player = $ev->getPlayer();

        if (Spawn_Data['is.enabled']) {
            $level = Spawn_Data['world.name'];
        } else {
            $level = $this->getNetwork()->getServerPM()->getDefaultLevel()->getName();
        }

        if ($player->getLevel()->getName() === $level) {
            if (!$player->isOp()) $ev->setCancelled();
        }
    }

    public function preventPlace(BlockPlaceEvent $ev): void
    {
        $player = $ev->getPlayer();

        if (Spawn_Data['is.enabled']) {
            $level = Spawn_Data['world.name'];
        } else {
            $level = $this->getNetwork()->getServerPM()->getDefaultLevel()->getName();
        }

        if ($player->getLevel()->getName() === $level) {
            if (!$player->isOp()) $ev->setCancelled();
        }
    }
}