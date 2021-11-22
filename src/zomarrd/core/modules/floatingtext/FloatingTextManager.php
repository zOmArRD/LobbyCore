<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 1/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\modules\floatingtext;

use Exception;
use pocketmine\math\Vector3;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\utils\TextUtils;

final class FloatingTextManager extends FloatingText
{
    /** @var NetworkPlayer */
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

    public function __construct(NetworkPlayer $player)
    {
        $this->setPlayer($player);

        foreach (["hcf", "practice", "uhc"] as $npc) $this->loadNpcText($npc);
        $this->loadTextLobby();
    }

    /**
     * @param string $name
     */
    private function loadNpcText(string $name): void
    {
        $player = $this->getPlayer();
        switch ($name) {
            case "hcf":
                if (self::getNpcPosition($name, "X") !== null) {
                    $text = $this->create(new Vector3((float)self::getNpcPosition($name, "X"), (float)self::getNpcPosition($name, "Y") + 2.15, (float)self::getNpcPosition($name, "Z")));
                    $this->send($text, $player, "§l§6G§fN: §r§6HCF");

                    $text = $this->create(new Vector3((float)self::getNpcPosition($name, "X"), (float)self::getNpcPosition($name, "Y") + 2.50, (float)self::getNpcPosition($name, "Z")));
                    $this->send($text, $player, "§k§6!!§r §bJOIN NOW §k§6!!");
                }
                break;
            case "practice":
                if (self::getNpcPosition($name, "X") !== null) {
                    $text = $this->create(new Vector3((float)self::getNpcPosition($name, "X"), (float)self::getNpcPosition($name, "Y") + 2.15, (float)self::getNpcPosition($name, "Z")));
                    $this->send($text, $player, "§l§6G§fN: §r§cPractice PvP");

                    $text = $this->create(new Vector3((float)self::getNpcPosition($name, "X"), (float)self::getNpcPosition($name, "Y") + 2.50, (float)self::getNpcPosition($name, "Z")));
                    $this->send($text, $player, "§k§6!!§r §6COMING SOON §k§6!!");
                }
                break;
            case "uhc":
                if (self::getNpcPosition($name, "X") !== null) {
                    $text = $this->create(new Vector3((float)self::getNpcPosition($name, "X"), (float)self::getNpcPosition($name, "Y") + 2.15, (float)self::getNpcPosition($name, "Z")));
                    $this->send($text, $player, "§l§6G§fN: §r§6UHC GAMES");

                    $text = $this->create(new Vector3((float)self::getNpcPosition($name, "X"), (float)self::getNpcPosition($name, "Y") + 2.50, (float)self::getNpcPosition($name, "Z")));
                    $this->send($text, $player, "§k§6!!§r §bNEW RELEASE §k§6!!");
                }
                break;
            default:
                break;
        }
    }

    private function loadTextLobby(): void
    {
        $player = $this->getPlayer();

        $archive = $this->getNetwork()->getResourceManager()->getArchive("floatingtext.data.yml");
        $pos = $archive->get("lobby.text.position");
        $lines = $archive->get("lobby.text");

        try {
            $text = $this->create(new Vector3(($pos["pos.x"] ?? 0) + 0.5, ($pos["pos.y"] ?? 0) + 2.85, ($pos["pos.z"] ?? 0) + 0.5));
            $this->send($text, $player, $lines[0]);

            $text = $this->create(new Vector3(($pos["pos.x"] ?? 0) + 0.5, ($pos["pos.y"] ?? 0) + 2.50, ($pos["pos.z"] ?? 0) + 0.5));
            $this->send($text, $player, $lines[1]);

            $text = $this->create(new Vector3(($pos["pos.x"] ?? 0) + 0.5, ($pos["pos.y"] ?? 0) + 2.00, ($pos["pos.z"] ?? 0) + 0.5));
            $this->send($text, $player, TextUtils::replaceVars((string)$lines[2], ["{player.get.name}" => $player->getName()]));

            $text = $this->create(new Vector3(($pos["pos.x"] ?? 0) + 0.5, ($pos["pos.y"] ?? 0) + 1.70, ($pos["pos.z"] ?? 0) + 0.5));
            $this->send($text, $player, TextUtils::replaceVars($lines[3], ["{{current.server}" => "{$this->getNetwork()->getServerManager()->getCurrentServer()->getName()}"]));

            $text = $this->create(new Vector3(($pos["pos.x"] ?? 0) + 0.5, ($pos["pos.y"] ?? 0) + 1.40, ($pos["pos.z"] ?? 0) + 0.5));
            $this->send($text, $player, $lines[4]);

            $text = $this->create(new Vector3(($pos["pos.x"] ?? 0) + 0.5, ($pos["pos.y"] ?? 0) + 0.85, ($pos["pos.z"] ?? 0) + 0.5));
            $this->send($text, $player, $lines[5]);
        } catch (Exception $ex) {
            var_dump("Error in line: {$ex->getLine()}, File: {$ex->getFile()} \n Error: {$ex->getMessage()}");
        }
    }

    /**
     * @return Network
     */
    private function getNetwork(): Network
    {
        return new Network();
    }
}