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

use pocketmine\utils\TextFormat;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\session\Session;

final class Scoreboard extends ScoreboardAPI
{

    public function __construct(NetworkPlayer $player)
    {
        $this->setPlayer($player);
    }

    /** @var string[] This is to replace blanks */
    private const EMPTY_CACHE = ["§0\e", "§1\e", "§2\e", "§3\e", "§4\e", "§5\e", "§6\e", "§7\e", "§8\e", "§9\e", "§a\e", "§b\e", "§c\e", "§d\e", "§e\e"];

    public function set(): void
    {
        $pn = $this->getPlayer()->getName();
        if (isset(Session::$playerSettings[$pn]["scoreboard"])) {
            $scData = Session::$playerSettings[$pn];
            if (!(bool)$scData["scoreboard"]) {
                return;
            }
        }

        $config = $this->getNetwork()->getResourceManager()->getArchive("scoreboard.yml");
        $this->new("greek.lobby", $config->get("display.name", "§6§lGreek §8Network"));
        $this->update();
    }

    public function update(): void
    {
        $player = $this->getPlayer();
        $config = $this->getNetwork()->getResourceManager()->getArchive("scoreboard.yml")->get($player->getLangSession()->get());

        if (!is_array($config)) return;

        foreach ($config as $scLine => $str) {
            $line = $scLine + 1;
            $msg = $this->replaceData($line, (string)$str);
            $this->setLine($line, $msg);
        }
    }

    /**
     * @param int    $line
     * @param string $message
     *
     * @return string
     */
    public function replaceData(int $line, string $message): string
    {
        if (empty($message)) return self::EMPTY_CACHE[$line] ?? "";
        $msg = $message;

        $data = [
            "{black}" => TextFormat::BLACK,
            "{dark.blue}" => TextFormat::DARK_BLUE,
            "{dark.green}" => TextFormat::DARK_GREEN,
            "{dark.aqua}" => TextFormat::DARK_AQUA,
            "{dark.red}" => TextFormat::DARK_RED,
            "{dark.purple}" => TextFormat::DARK_PURPLE,
            "{gold}" => TextFormat::GOLD,
            "{gray}" => TextFormat::GRAY,
            "{dark.gray}" => TextFormat::DARK_GRAY,
            "{blue}" => TextFormat::BLUE,
            "{green}" => TextFormat::GREEN,
            "{aqua}" => TextFormat::AQUA,
            "{red}" => TextFormat::RED,
            "{light.purple}" => TextFormat::LIGHT_PURPLE,
            "{yellow}" => TextFormat::YELLOW,
            "{white}" => TextFormat::WHITE,
            "{obfuscated}" => TextFormat::OBFUSCATED,
            "{bold}" => TextFormat::BOLD,
            "{strikethrough}" => TextFormat::STRIKETHROUGH,
            "{underline}" => TextFormat::UNDERLINE,
            "{italic}" => TextFormat::ITALIC,
            "{reset}" => TextFormat::RESET,
            "{eol}" => TextFormat::EOL,
            "{player.get.name}" => $this->getPlayer()->getName(),
            "{date}" => date("d/m/Y"),
            "{network.get.players}" => $this->getNetwork()->getServerManager()->getNetworkPlayers(),
            "{current.server}" => $this->getNetwork()->getServerManager()->getCurrentServer()->getName()
        ];

        $keys = array_keys($data);
        $values = array_values($data);

        for ($i = 0; $i < count($keys); $i++) {
            $msg = str_replace($keys[$i], (string)$values[$i], $msg);
        }

        return $msg;
    }
}