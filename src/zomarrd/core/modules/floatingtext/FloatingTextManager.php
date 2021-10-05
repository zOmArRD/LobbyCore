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

use pocketmine\math\Vector3;
use zomarrd\core\network\player\NetworkPlayer;

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

        foreach (["hcf"] as $npc) $this->loadNpcText($this->getPlayer(), $npc);
    }

    private function loadNpcText(NetworkPlayer $player, string $name): void
    {
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
            default:
                break;
        }
    }
}