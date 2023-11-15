<?php

namespace FiraAja\JumpRecord;

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    /** @var array<string, int> $record */
    private array $record = [];

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvent(PlayerJoinEvent::class, function (PlayerJoinEvent $event): void {
            $player = $event->getPlayer();
            if(isset($this->record[$player->getName()])) return;
            $this->record[$player->getName()] = 0;
        }, EventPriority::MONITOR, $this);
        $this->getServer()->getPluginManager()->registerEvent(PlayerJumpEvent::class, function (PlayerJumpEvent $event): void {
            $player = $event->getPlayer();
            if (!isset($this->record[$player->getName()])) return;
            $this->record[$player->getName()] += 1;
        }, EventPriority::MONITOR, $this);
        $this->initRecords();
    }

    protected function onDisable(): void
    {
        $records = json_encode($this->record);
        file_put_contents($this->getDataFolder() . "players.json", $records);
    }

    private function initRecords(): void {
        $records = file_get_contents($this->getDataFolder() . "players.json");
        $records = json_decode($records, true);
        $this->record = $records;
    }

    public function getRecord (Player $player): int {
        return $this->record[$player->getName()];
    }

    public function getRecords(): array {
        return $this->record;
    }
}