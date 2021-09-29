<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 29/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\network\utils;

final class EmoteUtils
{
    /**
     * @var array|string[]
     *
     * List of IDs with Minecraft emotes
     */
    private static array $emoteIds = [
        "Giddy" => "738497ce-539f-4e06-9a03-dc528506a468",
        "LikeDragon" => "c2a47805-c792-4882-a56d-17c80b6c57a8",
        "Meditating" => "85957448-e7bb-4bb4-9182-510b4428e52c",
        "Abduction" => "18891e6c-bb3d-47f6-bc15-265605d86525",
        "FacePlant" => "6d9f24c0-6246-4c92-8169-4648d1981cbb",
        "Cowpoke" => "f99ccd35-ebda-4122-b458-ff8c9f9a432f",
        "Surrendering" => "daeaaa6f-db91-4461-8617-400c5d1b8646"
    ];

    /**
     * @param string $emote
     *
     * @return string|null
     *
     * This returns the id of the array of the emotes.
     */
    static public function getEmoteId(string $emote): ?string
    {
        return self::$emoteIds[$emote] ?? null;
    }
}