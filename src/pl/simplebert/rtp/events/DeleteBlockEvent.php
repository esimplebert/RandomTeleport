<?php
namespace pl\simplebert\rtp\events;

use pl\simplebert\rtp\RTP;
use pocketmine\Player;
use pocketmine\event\plugin\PluginEvent;

class DeleteBlockEvent extends PluginEvent {
	private $player;
	
	public function __construct(Player $p) {
		$this->player = $p;
	}
	
	public function getPlayer() {
		return $this->player;
	}
}	
