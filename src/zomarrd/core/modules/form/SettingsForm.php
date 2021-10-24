<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 22/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\modules\form;

use zomarrd\core\network\player\NetworkPlayer;

final class SettingsForm
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
    }

    private function show(): void
    {

    }
}