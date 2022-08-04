
<?php

namespace FiraAja\JumpRecord;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\player\Player;

class Main extends PluginBase implements Listener {
	
	/** @var Config $dataJump */
	public Config $dataJump;
	/** @var Main */
	public static $instance;
	
	public function onEnable(): void {
		self::$instance = $this;
		$this->saveResource("dataJump.yml");
		$this->dataJump = new Config($this->getDataFolder() . "dataJump.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public static function getInstance(): self {
		return self::$instance;
	}
	
	public function onJoin(PlayerLoginEvent $event){
		$player = $event->getPlayer();
		if(!$this->dataJump->exists($player->getName())){
			$this->dataJump->setNested($player->getName() . ".jump", (int) 0);
			$this->dataJump->save();
		}
	}
	
	public function onJump(PlayerJumpEvent $event){
		$player = $event->getPlayer();
		if($player instanceof Player){
			$this->addJump($player);
		}
	}
	
	public function addJump(Player $player){
		$this->dataJump->setNested($player->getName() . ".jump", $this->dataJump->getAll()[$player->getName()]["jump"] + 1);
		$this->dataJump->save();
	}
	
	public function getJump(Player $player){
		return $this->dataJump->getAll()[$player->getName()]["jump"];
	}
	
	public function getAllJump(){
		return $this->dataJump->getAll();
	}
}
