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

namespace zomarrd\core\network\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use zomarrd\core\LobbyCore;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;

abstract class ScoreboardAPI
{
    /** @var NetworkPlayer */
    public NetworkPlayer $player;

    /** @var array */
    public array $lines = [], $objectiveName = [];

    /**
     * @param NetworkPlayer $player
     */
    public function setPlayer(NetworkPlayer $player): void
    {
        $this->player = $player;
    }

    /**
     * @return NetworkPlayer
     */
    public function getPlayer(): NetworkPlayer
    {
        return $this->player;
    }

    /**
     * @param string $objectiveName
     */
    public function setObjectiveName(string $objectiveName): void
    {
        $this->objectiveName[$this->getPlayer()->getName()] = $objectiveName;
    }

    /**
     * @return string
     */
    public function getObjectiveName(): string
    {
        return $this->objectiveName[$this->getPlayer()->getName()];
    }

    public function removeObjectiveName(): void
    {
        unset($this->objectiveName[$this->getPlayer()->getName()]);
    }

    /**
     * @return bool
     */
    public function isObjectiveName(): bool
    {
        return isset($this->objectiveName[$this->getPlayer()->getName()]);
    }

    /**
     * @param NetworkPlayer $player
     */
    abstract public function __construct(NetworkPlayer $player);

    /**
     * @param string $objectiveName
     * @param string $displayName
     */
    public function new(string $objectiveName, string $displayName): void
    {
        if ($this->isObjectiveName()) $this->remove();

        $packet = new SetDisplayObjectivePacket();
        $packet->objectiveName = $objectiveName;
        $packet->displayName = $displayName;
        $packet->sortOrder = 0;
        $packet->displaySlot = "sidebar";
        $packet->criteriaName = "dummy";
        $this->setObjectiveName($objectiveName);
        $this->getPlayer()->sendDataPacket($packet);
    }

    /**
     * @param int    $score
     * @param string $message
     */
    public function setLine(int $score, string $message): void
    {
        if (!$this->isObjectiveName()) return;

        if ($score > 15 || $score < 0) {
            LobbyCore::$logger->error("Score must be between the value of 1-15. $score out of range.");
            return;
        }

        $entry = new ScorePacketEntry();
        $entry->objectiveName = $this->getObjectiveName();
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        if (isset($this->lines[$score])) {
            $packet1 = new SetScorePacket();
            $packet1->entries[] = $this->lines[$score];
            $packet1->type = $packet1::TYPE_REMOVE;
            $this->getPlayer()->sendDataPacket($packet1);
            unset($this->lines[$score]);
        }
        $entry->score = $score;

        $entry->scoreboardId = $score;
        $entry->customName = $message;
        $this->lines[$score] = $entry;

        $packet2 = new SetScorePacket();
        $packet2->entries[] = $entry;
        $packet2->type = $packet2::TYPE_CHANGE;
        $this->getPlayer()->sendDataPacket($packet2);
    }

    public function clear(): void
    {
        $packet = new SetScorePacket();
        $packet->entries = $this->lines;
        $packet->type = $packet::TYPE_REMOVE;
        $this->getPlayer()->sendDataPacket($packet);
        $this->lines = [];
    }

    public function remove(): void
    {
        $packet = new RemoveObjectivePacket();
        $packet->objectiveName = $this->getObjectiveName();
        $this->getPlayer()->sendDataPacket($packet);
    }

    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }
}