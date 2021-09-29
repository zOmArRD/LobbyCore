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

namespace zomarrd\core\modules\lang;

use pocketmine\utils\Config;
use zomarrd\core\modules\form\lib\SimpleForm;
use zomarrd\core\modules\mysql\AsyncQueue;
use zomarrd\core\modules\mysql\query\UpdateRowQuery;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\session\Session;

final class LangManager
{
    /** @var NetworkPlayer */
    private NetworkPlayer $player;

    /** @var array */
    public static array $lang = [], $users = [];

    /** @var Config */
    public static Config $config;

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

    public function __construct(NetworkPlayer $player)
    {
        $this->setPlayer($player);
    }

    /**
     * @param array $updates
     */
    public function updateDatabase(array $updates): void
    {
        AsyncQueue::submitQuery(new UpdateRowQuery($updates, "player", $this->player->getName(), "settings"));
    }

    /**
     * @param string $language
     * @param bool   $safe
     */
    public function set(string $language, bool $safe): void
    {
        $pn = $this->getPlayer()->getName();
        self::$users[$pn] = $language;
        if ($safe) {
            Session::$playerSettings[$pn]["language"] = $language;
            $this->updateDatabase(["language" => "$language"]);
        }
    }

    /**
     * Apply the language to the player.
     */
    public function apply(): void
    {
        $pn = $this->getPlayer()->getName();
        if (isset(Session::$playerSettings[$pn])) {
            $data = Session::$playerSettings[$pn];
            if ($data["language"] !== null && $data["language"] !== "null") {
                $this->set($data["language"], false);
            }
        }
    }

    /**
     * Gets the language of the player.
     * @return string
     */
    public function get(): string
    {
        $pn = $this->getPlayer()->getName();
        return self::$users[$pn] ?? "en_ENG";
    }

    /**
     * Gets the Strings of the language.
     * @param string $id
     *
     * @return string
     */
    public function getString(string $id): string
    {
        $strings = self::$lang[$this->get()]->get("strings");
        return $strings["$id"] ?? $this->getNetwork()->getTextUtils()->replaceColor($strings["message.error"]);
    }

    public function getNetwork(): Network
    {
        return new Network();
    }

    public function showForm(): void
    {
        $player = $this->getPlayer();
        $form = new SimpleForm(function (NetworkPlayer $player, $data) {
            if (isset($data)) {

            }
        });

        $form->setTitle("");
    }
}