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

namespace zomarrd\core;

use pocketmine\network\mcpe\RakLibInterface;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;

final class LobbyCore extends PluginBase
{
    /** @var LobbyCore */
    public static LobbyCore $instance;

    /** @var PluginLogger */
    public static PluginLogger $logger;

    public function onLoad(): void
    {
        self::setInstance($this);
        self::setLogger($this->getLogger());
    }

    public function onEnable(): void
    {
        foreach ($this->getServer()->getNetwork()->getInterfaces() as $interface) {
            if ($interface instanceof RakLibInterface) {
                $interface->setPacketLimit(PHP_INT_MAX);
            }
        }
    }

    /**
     * @param LobbyCore $instance
     */
    public static function setInstance(LobbyCore $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * @return LobbyCore
     */
    public static function getInstance(): LobbyCore
    {
        return self::$instance;
    }

    /**
     * @param PluginLogger $logger
     */
    public static function setLogger(PluginLogger $logger): void
    {
        self::$logger = $logger;
    }
}