<?php
namespace pl\simplebert\rtp\commands;

use pl\simplebert\rtp\RTP;
use pl\simplebert\rtp\events\AddBlockEvent;
use pl\simplebert\rtp\events\DeleteBlockEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;


class RandomTeleportCommand extends PluginBase implements CommandExecutor {
	private $rtp;
	private $config;
	
	public function __construct(RTP $rtp) {
		$this->rtp = $rtp;
		$this->config = $rtp->config;
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
		if(strtolower($cmd->getName()) == "randomteleport") {
			if(count($args) == 0) {
				if($sender instanceof Player) {
					if($this->config->get("cmd")) {
						if($sender->hasPermission("randomteleport.teleport")) {
							$this->rtp->randomTeleport($sender);
							return true;
						}
						else {
								$sender->sendMessage(TextFormat::RED . "You haven't got permission to use this command!");
								return true;
						}
					}
					else {
						$sender->sendMessage(TextFormat::RED . "This command is disabled.");
						return true;
					}
				}
				else {
					$this->rtp->getLogger()->info(TextFormat::RED . "This command can't be executed by the console!");
					return true;
				}
			}
			
			elseif(count($args) == 1) {
				$args[0] = strtolower($args[0]);
				
				if($args[0] == "addblock") {
					if($sender instanceof Player) {
						if($sender->hasPermission("randomteleport.addblock")) {
							array_push($this->rtp->events, new AddBlockEvent($sender));
							$sender->sendMessage(TextFormat::GREEN . RTP::PREFIX . " Please select the block to be added to the list");
							return true;
						}
						else {
							$sender->sendMessage(TextFormat::RED . "You haven't got permission to use this command!");
							return true;
						}
					}
					else {
						$this->rtp->getLogger()->info(TextFormat::RED . "That command can't be executed by the console!");
						return true;
					}
				}
				
				if($args[0] == "delblock") {
					if($sender instanceof Player) {
						if($sender->hasPermission("randomteleport.delblock")) {
							array_push($this->rtp->events, new DeleteBlockEvent($sender));
							$sender->sendMessage(TextFormat::GREEN . RTP::PREFIX . " Please select the block to be removed from the list.");
							return true;
						}
						else {
							$sender->sendMessage(TextFormat::RED . "You haven't got permission to use this command!");
							return true;
						}
					}
					else {
						$this->rtp->getLogger()->info(TextFormat::RED . "This command can't be executed by the console!");
						return true;
					}
				}
				
				elseif($args[0] == "reload") {
					if($sender->hasPermission("randomteleport.reload")) {
						$this->rtp->saveDefaultConfig();
						$this->rtp->reloadConfig();
						
						if($sender instanceof Player) $sender->sendMessage(TextFormat::GREEN . RTP::PREFIX . " Configuration reloaded.");
						$this->rtp->getLogger()->info(TextFormat::GREEN . "Configuration reloaded.");
						return true;
					}
					else {
						$sender->sendMessage(TextFormat::RED . "You haven't got permission to use this command!");
						return true;
					}
				}
				
				elseif($args[0] == "info") {
					$plugin = $this->rtp->getDescription();
					$info = array(
						"description" => $plugin->getDescription(),
						"author" => implode(", ", $plugin->getAuthors()),
						"version" => $plugin->getVersion(),
						"api" => implode(", ", $plugin->getCompatibleApis())
					);
					
					$sender->sendMessage("");
					$sender->sendMessage(TextFormat::GREEN . RTP::PREFIX . " Plugin informations:");
					foreach($info as $name => $content) {
						$sender->sendMessage(TextFormat::GREEN . "$name: $content");
					}
					$sender->sendMessage("");
					return true;
				}
				
				else {
					$sender->sendMessage(TextFormat::RED . " Use: " . $cmd->getUsage());
					return true;
				}
			}
			else {
				$sender->sendMessage(TextFormat::RED . " Use: " . $cmd->getUsage());
				return true;
			}
		}
		return false;
	}
}
