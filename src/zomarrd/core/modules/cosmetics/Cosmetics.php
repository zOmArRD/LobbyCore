<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 20/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);


namespace zomarrd\core\modules\cosmetics;

use zomarrd\core\modules\mysql\AsyncQueue;
use zomarrd\core\modules\mysql\query\UpdateRowQuery;
use zomarrd\core\network\player\IPlayer;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\utils\TextUtils;
use const zOmArRD\PREFIX;

/**
 * @todo Finalize the cosmetic system.
 */
final class Cosmetics implements IPlayer
{
    /** @var NetworkPlayer  */
    private NetworkPlayer $player;

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
     * @return string
     */
    public function getPlayerName(): string
    {
        return $this->getPlayer()->getName();
    }

    public function __construct(NetworkPlayer $player)
    {
        $this->setPlayer($player);
    }

    /** @var array */
    public static array $particles = [], $db = [];

    /**
     * @param string $cosmetic
     * @param string $type
     * @param string $cosmeticName
     *
     * @return string
     */
    public function getMessageUpdated(string $cosmetic, string $type = "activate", string $cosmeticName = ""): string
    {
        $msg = $this->getPlayer()->getLangTranslated("cosmetics.message.$type");
        return TextUtils::replaceVars($msg, ["{cosmetic}" => $cosmetic]) . " $cosmeticName";
    }

    /**
     * @param array $data
     */
    public function updateDatabase(array $data): void
    {
        AsyncQueue::submitQuery(new UpdateRowQuery($data, "player", $this->getPlayerName(), "cosmetics"));
    }

    /**
     * @param string $particleId
     * @param bool   $safe
     */
    public function setParticle(string $particleId, bool $safe = true): void
    {
        self::$particles[$this->getPlayerName()] = $particleId;

        if ($safe) {
            Cosmetics::$db[$this->getPlayerName()]["particles"] = $particleId;
            $this->updateDatabase(["particles" => $particleId]);
        }
    }

    /**
     * @param bool $save
     */
    public function removeParticle(bool $save = true): void
    {
        if (isset(self::$particles[$this->getPlayerName()])) unset(self::$particles[$this->getPlayerName()]);
        if ($save) $this->updateDatabase(["particles" => "null"]);
        $this->getPlayer()->sendMessage(PREFIX . $this->getMessageUpdated('Particles', 'deactivate'));
    }


    public function applyCosmetics(): void
    {
        if (isset(self::$db[$this->getPlayerName()])) {
            $data = Cosmetics::$db[$this->getPlayerName()];
            if ($data["particles"] !== null && $data["particles"] !== "null") $this->setParticle($data["particles"], false);
        } else $this->getPlayer()->sendMessage(PREFIX . $this->getPlayer()->getLangTranslated("cosmetics.message.error"));
    }
}