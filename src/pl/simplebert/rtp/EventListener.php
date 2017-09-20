<?php
namespace pl\simplebert\rtp;

use pl\simplebert\rtp\RTP;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

class EventListener implements Listener {
	private $rtp;
	
	public function __construct(RTP $rtp) {
		$this->rtp = $rtp;
	}
	
	
	
	
	public function onInteract(PlayerInteractEvent $e) {
		$teleport = true;
		
		$player = $e->getPlayer();
		$name = $player->getName();
		
		$block = $e->getBlock();
		$pos = array($block->getX(), $block->getY(), $block->getZ());
		$blocks = (array) $this->rtp->config->get("blocks");
		
		foreach($this->rtp->events as $offset => $event) {
			if($event->getPlayer()->getName() == $name) {
				if(strpos(get_class($event), "AddBlockEvent")) {
					if(!in_array($pos, $blocks)) {
						unset($this->rtp->events[$offset]); //unset event
						
						//add block pos to config
						$new = $blocks;
						array_push($new, $pos);
						$this->rtp->config->set("blocks", $new);
						$this->rtp->config->save();
					
						$player->sendMessage(TextFormat::GREEN . RTP::PREFIX . " The block has been added to the list.");
					}
					else {
						unset($this->rtp->events[$offset]); //unset event
						$player->sendMessage(TextFormat::RED . RTP::PREFIX . " This block is already on the list!");
					}
				}
				
				
				
				elseif(strpos(get_class($event), "DeleteBlockEvent")) {
					if(in_array($pos, $blocks)) {
						unset($this->rtp->events[$offset]); //unset event
						
						//delete block pos from config
						$key = array_search($pos, $blocks);
						unset($blocks[$key]);
						$this->rtp->config->set("blocks", $blocks);
						$this->rtp->config->save();
						
						$player->sendMessage(TextFormat::GREEN . RTP::PREFIX . " The block has been removed from the list.");
					}
					else {
						unset($this->rtp->events[$offset]); //unset event
						$player->sendMessage(TextFormat::RED . RTP::PREFIX . " This block isn't on the list!");
					}
				}
				
				$teleport = false;
				break;
			}
		}
		
		
		
		if($teleport) {
			if($player->hasPermission("randomteleport.teleport")) {
				if(in_array($pos, $blocks)) {
					$this->rtp->randomTeleport($player);
				}
			}
		}
	}
}
