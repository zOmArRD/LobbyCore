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

use Exception;
use pocketmine\utils\Config;
use zomarrd\core\modules\form\lib\SimpleForm;
use zomarrd\core\modules\mysql\AsyncQueue;
use zomarrd\core\modules\mysql\query\UpdateRowQuery;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\IPlayer;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\session\Session;
use zomarrd\core\network\utils\TextUtils;
use const zOmArRD\PREFIX;

final class Lang implements IPlayer
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

    /**
     * @param array $updates
     */
    public function updateDatabase(array $updates): void
    {
        AsyncQueue::submitQuery(new UpdateRowQuery($updates, "player", $this->player->getName(), "settings"));
    }

    /**
     * @param string $language
     * @param bool $safe
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
            if ($data["language"] !== null && $data["language"] !== "null") $this->set($data["language"], false);
        }
    }

    /**
     * Gets the language of the player.
     *
     * @return string
     */
    public function get(): string
    {
        $pn = $this->getPlayer()->getName();
        return self::$users[$pn] ?? "eng";
    }

    /**
     * Gets the Strings of the language.
     *
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

    /**
     * @param string $type
     */
    public function showForm(string $type = "with.back.button"): void
    {
        $player = $this->getPlayer();
        $form = new SimpleForm(function (NetworkPlayer $player, $data) {
            if (isset($data)) {
                if ($data == "back") {
                    /* todo: return to Settings Form */
                } elseif ($data == "close") return;

                if ($this->get() !== $data) {
                    $this->set($data, true);
                    $player->getInventory()->clearAll();
                    $player->setLobbyItems();
                    $player->sendMessage(PREFIX . TextUtils::replaceColor("message.lang.set.done"));
                } else $player->sendMessage(PREFIX . TextUtils::replaceColor("message.lang.set.fail"));
            }
        });

        $form->setTitle(TextUtils::replaceColor($this->getString("form.title.lang.selector")));

        try {
            foreach (Lang::$config->get("languages") as $lang) $form->addButton("§a" . $lang['name'], $form::IMAGE_TYPE_URL, $lang['icon'], $lang['ISOCode']);
        } catch (Exception $ex) {
            if ($player->isOp()) $player->sendMessage("Error in line: {$ex->getLine()}, File: {$ex->getFile()} \n Error: {$ex->getMessage()}");
        }

        if ($type == "with.back.button") {
            $form->addButton($this->getString("form.button.back"), $form::IMAGE_TYPE_PATH, "", "back");
        } else $form->addButton($this->getString("form.button.close"), $form::IMAGE_TYPE_PATH, "textures/gui/newgui/anvil-crossout", "close");

        $player->sendForm($form);
    }
}