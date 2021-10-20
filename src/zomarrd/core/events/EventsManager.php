<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 26/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\events;

use zomarrd\core\events\listener\InteractListener;
use zomarrd\core\events\listener\LPlayer;
use zomarrd\core\LobbyCore;
use const zOmArRD\PREFIX;

final class EventsManager extends Events
{

    public function __construct()
    {
        $this->loadEvents();
    }

    /**
     * In this function you add the events to the foreach array to register them.
     */
    public function loadEvents(): void
    {
        foreach ([new LPlayer(), new InteractListener()] as $listener) $this->register($listener);
        LobbyCore::$logger->info(PREFIX . "the events have been registered!");
    }
}