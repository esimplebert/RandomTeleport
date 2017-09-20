<?php
namespace pl\simplebert\rtp;

use pl\simplebert\rtp\commands\RandomTeleportCommand;
use pl\simplebert\rtp\EventListener;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\generator\biome\Biome;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class RTP extends PluginBase {
	const PREFIX = "[RandomTP]";
	
	public $config;
	public $events = array();
	
	
	public function onLoad() {
		$this->getLogger()->info(TextFormat::GREEN . "Plugin has been loaded.");
	}
	
	
	public function onEnable() {
		if(!is_dir($this->getDataFolder())) {
			@mkdir($this->getDataFolder());
		}
		$this->config = $this->getConfig();
		$this->saveDefaultConfig();
		
		//register events
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		//register commands
		$this->getCommand("randomteleport")->setExecutor(new RandomTeleportCommand($this));
		
		
		$this->getLogger()->info(TextFormat::GREEN . "Plugin has been enabled.");
	}
	
	
	public function onDisable() {
		$this->getLogger()->info(TextFormat::RED . "Plugin has been disabled");
	}
	
	
	
	
	
	
	public function randomTeleport(Player $p) {
		$level = $p->getLevel();
		$name = $level->getName();
		
		if(in_array($name, $this->config->get("levels"))) {
			$radius = $this->config->getAll()["radius"];
			$x = mt_rand($radius["x"][0], $radius["x"][1]);
			$z = mt_rand($radius["z"][0], $radius["z"][1]);
			$y = $level->getHighestBlockAt($x, $z);
		
			if($level->getBiomeId($x, $z) != Biome::OCEAN) {
				$p->teleport(new Position($x, $y+1, $z, $level));
				$p->sendMessage(TextFormat::GREEN . "You have been teleported to random coordinates (x=$x, y=$y, z=$z).");
			}
			else {
				$this->randomTeleport($p);
			}
		}
		else {
			$p->sendMessage(TextFormat::RED . "You can't be teleported to random coordinates in this world!");
		}
	}
}
