<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 18/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\commands\lang;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use zomarrd\core\network\player\NetworkPlayer;

final class LangCmd extends Command
{
    public function __construct()
    {
        parent::__construct("lang", "Open Lang Menu", "/lang", []);
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     *
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof NetworkPlayer) $sender->getLangSession()->showForm("");
    }
}