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

namespace zomarrd\core\network\player;

interface IPlayer
{
    /**
     * @param NetworkPlayer $player
     */
    public function setPlayer(NetworkPlayer $player): void;

    /**
     * @return NetworkPlayer
     */
    public function getPlayer(): NetworkPlayer;

    /**
     * @return string
     */
    public function getPlayerName(): string;

    /**
     * @param NetworkPlayer $player
     */
    public function __construct(NetworkPlayer $player);
}