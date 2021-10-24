<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 23/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\modules\form;

use pocketmine\utils\TextFormat;
use zomarrd\core\modules\form\lib\SimpleForm;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\utils\TextUtils;
use const zOmArRD\PREFIX;

final class CosmeticsForm
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
        $this->show();
    }

    public function show(): void
    {
        $player = $this->getPlayer();

        $form = new SimpleForm(function (NetworkPlayer $player, $data) {
            if (isset($data)) {

                switch ($data) {
                    case "close":
                        return;
                    case "particles":
                        $this->showParticlesSelector();
                        break;
                }
            }
        });


        $images = [
            "particles" => "textures/ui/icon_staffpicks",
            "close" => "textures/gui/newgui/anvil-crossout",
        ];

        $form->setTitle(TextUtils::replaceColor("{bold}{gray}» {light.purple}Cosmetics Menu §7«"));
        $form->setContent(TextUtils::replaceColor("{yellow}Select which cosmetic you want"));

        $form->addButton("§cParticles", 0, $images['particles'], 'particles');

        $form->addButton($player->getLangTranslated("form.button.close"), 0, $images['close'], 'close');
        $player->sendForm($form);
    }

    public function showParticlesSelector(): void
    {
        $player = $this->getPlayer();

        $form = new SimpleForm(function (NetworkPlayer $player, $data) {
            if (isset($data)) {
                switch ($data) {
                    case "back":
                        $this->show();
                        break;
                    case "disable":
                        $player->getCosmeticsSession()->removeParticle();
                        break;
                    default:
                        if ($player->hasPermission("cosmetics.particles.$data")) {
                            $player->getCosmeticsSession()->setParticle($data);
                            $player->sendMessage(PREFIX . $player->getCosmeticsSession()->getMessageUpdated("particles", "activate", $data));
                        } else {
                            $player->sendMessage(PREFIX . $player->getCosmeticsSession()->getMessageUpdated("particles", "noperms"));
                        }
                        break;
                }
            }
        });

        $images = [
            "remove" => "textures/ui/book_trash_default",
        ];

        $form->setTitle(TextUtils::replaceColor("{bold}{gray}» {gold}Particles Menu {gray}«"));
        $form->setContent(TextUtils::replaceColor("{yellow}Select which particles you want"));

        $form->addButton(TextUtils::replaceColor("{red}disable particles"), 0, $images['remove'], 'disable');

        $this->customButton($form, "Lava (Splash)", "lava.splash", "particles");
        $this->customButton($form, "Water (Splash)", "water.splash", "particles");

        $form->addButton($player->getLangTranslated("form.button.back"), 0, "", 'back');
        $player->sendForm($form);
    }

    /**
     * @param SimpleForm  $form
     * @param string      $buttonName
     * @param array       $imageData
     * @param string|null $label
     * @param string      $type
     */
    public function customButton(SimpleForm $form, string $buttonName, ?string $label, string $type, array $imageData = [0, ""],): void
    {
        $name = $buttonName;
        $player = $this->getPlayer();

        if (!$player->hasPermission("cosmetics.$type.$label") or !$player->isOp()) {
            $name .= TextFormat::EOL . TextUtils::replaceColor($player->getLangTranslated("form.button.locked"));
        } else {
            $name .= TextFormat::EOL . TextUtils::replaceColor($player->getLangTranslated("form.button.unlocked"));
        }

        $form->addButton($name, $imageData[0], $imageData[1], $label);
    }
}