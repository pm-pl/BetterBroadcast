<?php

namespace supercrafter333\BetterBroadcast\task;

use pocketmine\scheduler\Task;
use supercrafter333\BetterBroadcast\BetterBroadcast;
use function array_keys;
use function count;
use function is_array;

class BroadcastTask extends Task
{

    protected string $lastType = "msg";

    protected int $msgCount = 0;
    protected int $tipCount = 0;
    protected int $titleCount = 0;
    protected int $toastCount = 0;

    public function onRun(): void
    {
        $cfg = BetterBroadcast::getInstance()->getConfig();
        $broadcasts = $cfg->get("broadcasts", []);

        if (count($broadcasts) <= 0) {
            $this->getHandler()->cancel();
            return;
        }

        switch ($this->lastType) {
            case "msg":
                goto tip;
            case "tip":
                goto title;
            case "title":
                goto toast;
            case "toast":
                goto msg;
        }

        tip: {
            if (!isset($broadcasts["tips"]) || !is_array($broadcasts["tips"]))
                goto title;
            else {
                $tips = $broadcasts["tips"];
                if (!isset($tips[$this->tipCount]))
                    $this->tipCount = 0;

                if (isset($tips[$this->tipCount])) {
                    BetterBroadcast::getInstance()->sendBroadcast("tip", $tips[$this->tipCount]);
                    $this->tipCount++;
                }
                $this->lastType = "tip";
            }
            return;
        }

        title: {
            if (!isset($broadcasts["titles"]) || !is_array($broadcasts["titles"]))
                goto toast;
            else {
                $titles = $broadcasts["titles"];
                $titleKeys = array_keys($titles);

                if (!isset($titleKeys[$this->titleCount]))
                    $this->titleCount = 0;

                if (isset($titleKeys[$this->titleCount])) {
                    BetterBroadcast::getInstance()->sendBroadcast("title", $titleKeys[$this->titleCount], $titles[$titleKeys[$this->titleCount]]);
                    $this->titleCount++;
                }
                $this->lastType = "title";
            }
            return;
        }

        toast: {
            if (!isset($broadcasts["toasts"]) || !is_array($broadcasts["toasts"]))
                goto msg;
            else {
                $toasts = $broadcasts["toasts"];
                if (!isset($toasts[$this->toastCount]))
                    $this->toastCount = 0;

                $toastKeys = array_keys($toasts);

                if (isset($toastKeys[$this->toastCount])) {
                    BetterBroadcast::getInstance()->sendBroadcast("toast", $toastKeys[$this->toastCount], $toasts[$toastKeys[$this->toastCount]]);
                    $this->toastCount++;
                }
                $this->lastType = "toast";
            }
            return;
        }

        msg: {
            if (!isset($broadcasts["messages"]) || !is_array($broadcasts["messages"]))
                goto tip;
            else {
                $msgs = $broadcasts["messages"];
                if (!isset($msgs[$this->msgCount]))
                    $this->msgCount = 0;

                if (isset($msgs[$this->msgCount])) {
                    BetterBroadcast::getInstance()->sendBroadcast("msg", $msgs[$this->msgCount]);
                    $this->msgCount++;
                }
                $this->lastType = "msg";
            }
            return;
        }
    }
}