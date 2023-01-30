<?php

namespace supercrafter333\BetterBroadcast;

use InvalidArgumentException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use supercrafter333\BetterBroadcast\task\BroadcastTask;
use function strtolower;

class BetterBroadcast extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveDefaultConfig();

        $this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), $this->getConfig()->get("interval", 300) * 20);
    }

    public function sendBroadcast(string $type, string $title, ?string $subtitle = null): void
    {
        switch (strtolower($type)) {
            case "message":
            case "msg":
                $this->getServer()->broadcastMessage($title);
                break;
            case "tip":
            case "popup":
            case "actionbar":
                $this->getServer()->broadcastTip($title);
                break;
            case "title":
                $this->getServer()->broadcastTitle($title, ($subtitle !== null ? $subtitle : ""));
                break;
            case "toast":
            case "toastnotification":
                if ($subtitle === null)
                    throw new InvalidArgumentException('$subtitle (' . (string)$subtitle . ') must be a string to send a toast-notification!');

                foreach ($this->getServer()->getOnlinePlayers() as $player)
                    $player->sendToastNotification($title, $subtitle);

                break;
        }
    }
}