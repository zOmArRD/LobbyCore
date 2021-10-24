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

use zomarrd\core\modules\form\lib\SimpleForm;
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
        $this->show();
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

    public function show(): void
    {
        $player = $this->getPlayer();

        $form = new SimpleForm(function (NetworkPlayer $player, $data) {
            if (isset($data)) {

                switch ($data) {
                    case "close":
                        return;
                    case "change.lang":
                        $player->getLangSession()->showForm();
                        break;
                }
            }
        });


        $images = [
            "language" => "textures/ui/language_glyph_color",
            "close" => "textures/gui/newgui/anvil-crossout",
        ];

        $form->setTitle($player->getLangTranslated("form.title.settings"));

        $form->addButton($player->getLangTranslated("form.button.change.lang"), $form::IMAGE_TYPE_PATH, $images['language'], 'change.lang');

        $form->addButton($player->getLangTranslated("form.button.close"), $form::IMAGE_TYPE_PATH, $images['close'], 'close');
        $player->sendForm($form);
    }
}