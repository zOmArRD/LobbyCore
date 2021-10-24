<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 28/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\network\player;

use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\OnScreenTextureAnimationPacket;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\network\mcpe\protocol\types\GameMode;
use pocketmine\Player;
use zomarrd\core\items\ItemsManager;
use zomarrd\core\modules\cosmetics\Cosmetics;
use zomarrd\core\modules\lang\Lang;
use zomarrd\core\network\Network;
use zomarrd\core\network\scoreboard\Scoreboard;
use zomarrd\core\network\utils\TextUtils;
use const zOmArRD\PREFIX;
use const zOmArRD\Spawn_Data;

final class NetworkPlayer extends Player
{
    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }

    /** @var Lang */
    public Lang $langSession;

    /**
     * Sets the Language Session to the player.
     */
    public function setLangSession(): void
    {
        $this->langSession = new Lang($this);
    }

    /**
     * Returns the session of the player's Lang class.
     *
     * @return Lang
     */
    public function getLangSession(): Lang
    {
        return $this->langSession;
    }

    /**
     * @param string $idString
     *
     * @return string
     */
    public function getLangTranslated(string $idString): string
    {
        $session = $this->getLangSession();
        return $this->getNetwork()->getTextUtils()->replaceColor($session->getString($idString));
    }

    public function handleLevelSoundEvent(LevelSoundEventPacket $packet): bool
    {
        return true;
    }

    /** @var Scoreboard */
    public Scoreboard $scoreboardSession;

    public function setScoreboardSession(): void
    {
        $this->scoreboardSession = new Scoreboard($this);
    }

    /**
     * @return Scoreboard
     */
    public function getScoreboardSession(): Scoreboard
    {
        return $this->scoreboardSession;
    }

    /** @var Cosmetics */
    public Cosmetics $cosmeticsSession;

    public function setCosmeticsSession(): void
    {
        $this->cosmeticsSession = new Cosmetics($this);
    }

    /**
     * @return Cosmetics
     */
    public function getCosmeticsSession(): Cosmetics
    {
        return $this->cosmeticsSession;
    }

    public function setItem(int $index, Item $item): void
    {
        $pi = $this->getInventory();
        if (isset($pi)) $pi->setItem($index, $item);
    }

    public function setLobbyItems(): void
    {
        $inventory = $this->getInventory();
        if (isset($inventory)) {
            $inventory->clearAll();

            foreach (["item.navigator" => 0,"item.cosmetics" => 1 , "item.settings" => 8] as $item => $index) $this->setItem($index, ItemsManager::get($item, $this));
        }
    }

    public function teleportToLobby(): void
    {
        $this->setLobbyItems();
        $this->setGamemode(GameMode::ADVENTURE);
        $this->setHealth(20);
        $this->setFood(20);

        if (Spawn_Data['is.enabled']) {
            $spawn = Spawn_Data;
            $yaw = $spawn['player.yaw'] !== null ? $spawn['player.yaw'] : $this->getYaw();
            $pitch = $spawn['player.pitch'] !== null ? $spawn['player.pitch'] : $this->getYaw();
            $this->teleport(new Position($spawn['pos.x'], $spawn['pos.y'], $spawn['pos.z'], $this->getNetwork()->getServerPM()->getLevelByName($spawn['world.name'])), $yaw, $pitch);
        }
    }

    /**
     * Function to transfer the player to another server, it must be proxied.
     *
     * @param string $target
     */
    public function transferServer(string $target): void
    {
        $servers = $this->getNetwork()->getServerManager()->getServers();
        if (count($servers) <= 0) {
            $this->sendMessage(PREFIX . TextUtils::replaceColor("{red}We could not connect to the servers, please try again!"));
            return;
        }

        foreach ($servers as $server) {
            if ($server->getName() == $target) if ($server->isOnline()) if (!$server->isWhitelisted() || $this->isOp()) {
                $this->sendMessage(PREFIX . TextUtils::replaceColor("{green}Connecting to the server..."));
                $pk = new TransferPacket();
                $pk->address = $target;
                $this->directDataPacket($pk);
            } else {
                $this->sendMessage(PREFIX . TextUtils::replaceColor("{red}The server is under maintenance"));
                return;
            } else {
                $this->sendMessage(PREFIX . TextUtils::replaceColor("{red}The server is offline!"));
            } else $this->sendMessage(PREFIX . TextUtils::replaceColor("{red}Could not connect to this server!"));
            return;
        }
    }

    /**
     * @param int $effectId
     */
    public function showScreenAnimation(int $effectId): void
    {
        $pk = new OnScreenTextureAnimationPacket();
        $pk->effectId = $effectId;
        $this->sendDataPacket($pk);
    }
}