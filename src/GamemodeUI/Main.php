<?php

namespace GamemodeUI;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;

use pocketmine\utils\Config;

use GamemodeUI\libs\jojoe77777\FormAPI\CustomForm;
use GamemodeUI\libs\jojoe77777\FormAPI\SimpleForm;

//  _____
// |  __ \
// | |  | | __ _ __   __ _   _
// | |  | |/ _` |\ \ / /| | | |
// | |__| | (_| | \ V / | |_| |
// |_____/ \__,_|  \_/   \__, |
//         Craft648       __/ |
//      Ini File Main    |___/

Class Main extends PluginBase implements Listener{

    private const CONFIG_VERSION = 1;

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§aMengaktifkan §bGamemodeUI...");

		$this->saveResource("config.yml");
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->saveDefaultConfig();
        $this->checkConfigs();
    }

    public function checkConfigs(): void{
        if ((!$this->getConfig()->exists("config-version")) || ($this->getConfig()->get("config-version") != self::CONFIG_VERSION)){
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
            $this->saveResource("config.yml");
            $this->getLogger()->critical("File config kadaluarsa!");
            $this->getLogger()->notice("File config lama telah disimpan sebagai config_old.yml dan file config baru telah dibuat.");
        }

    }

	public function onDisable(){
		$this->getLogger()->info("§aMenonaktifkan §bGamemodeUI...");
	}

	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool{
		if ($this->getConfig()->getNested("menu.new") === false){
			if($command->getName() === "gmui"){
				if ($sender instanceof Player){
					if($sender->hasPermission("gmui.cmd")){
						$form = new CustomForm(function(Player $sender, $data){
							$result = $data[0];
							if( !is_null($data)){
								switch($result){
									case 0:
										$sender->sendMessage($this->getConfig()->getNested("exit.msg"));
										break;

									case 1:
										if($sender->hasPermission("gmui.creative")){
											$sender->sendMessage($this->getConfig()->getNested("creative.msg"));
											$sender->addTitle(($this->getConfig()->getNested("creative.title")), ($this->getConfig()->getNested("creative.subtitle")));
											$sender->setGamemode(1);
										} else{
											$sender->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
										}
										break;

									case 2:
										if($sender->hasPermission("gmui.survival")){
											$sender->sendMessage($this->getConfig()->getNested("survival.msg"));
											$sender->addTitle(($this->getConfig()->getNested("survival.title")), ($this->getConfig()->getNested("survival.subtitle")));
											$sender->setGamemode(0);
										} else{
											$sender->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
										}
										break;

									case 3:
										if($sender->hasPermission("gmui.adventure")){
											$sender->sendMessage($this->getConfig()->getNested("adventure.msg"));
											$sender->addTitle(($this->getConfig()->getNested("adventure.title")), ($this->getConfig()->getNested("adventure.subtitle")));
											$sender->setGamemode(2);
										} else{
											$sender->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
										}
										break;

									case 4:
										if($sender->hasPermission("gmui.spectator")){
											$sender->sendMessage($this->getConfig()->getNested("spectator.msg"));
											$sender->addTitle(($this->getConfig()->getNested("spectator.title")), ($this->getConfig()->getNested("spectator.subtitle")));
											$sender->setGamemode(3);
										} else{
											$sender->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
										}
										break;
											return;
								}
							}
						});
						$form->setTitle($this->config->getNested("menu.title-form"));
						$form->addDropdown(($this->config->getNested("menu.description-form")), [($this->config->getNested("exit.button.name")), "Creative", "Survival", "Adventure", "Spectator"]);
						$form->sendToPlayer($sender);
					}	else {
						$sender->sendMessage($this->getConfig()->getNested("exit.noperm"));
						}
						return true;
				} else{
					$this->getLogger()->critical($this->getConfig()->getNested("exit.console"));
				}
			}
		}
		switch($command->getName()){
			case "gmui":
				if ($sender instanceof Player){
					if ($this->getConfig()->getNested("menu.new") === true){
						if ($sender->hasPermission("gmui.cmd")){
			 			$this->openMyForm($sender);
			 			} else{
			 				$sender->sendMessage($this->getConfig()->getNested("exit.noperm"));
			 			}
					}
				} else{
			 		$this->getLogger()->critical($this->getConfig()->getNested("exit.console"));
			 	}
		break;
		}
	 return true;
	}

	public function openMyForm($player){
		$form = new SimpleForm(function (Player $player, int $data = null){
			$result = $data;
			if($result === null){
				return true;
			}
			switch($result){
				case 0:
					if($player->hasPermission("gmui.creative")){
						$player->setGamemode(1);
						$player->sendMessage($this->getConfig()->getNested("creative.msg"));
						$player->addTitle(($this->getConfig()->getNested("creative.title")), ($this->getConfig()->getNested("creative.subtitle")));
					} else{
						$player->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
					}
				break;

				case 1:
					if($player->hasPermission("gmui.survival")){
						$player->setGamemode(0);
						$player->sendMessage($this->getConfig()->getNested("survival.msg"));
						$player->addTitle(($this->getConfig()->getNested("survival.title")), ($this->getConfig()->getNested("survival.subtitle")));
					} else{
						$player->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
					}
				break;

				case 2:
					if($player->hasPermission("gmui.adventure")){
						$player->setGamemode(2);
						$player->sendMessage($this->getConfig()->getNested("adventure.msg"));
						if($player->getGamemode() === 2){
							$player->addTitle(($this->getConfig()->getNested("adventure.title")), ($this->getConfig()->getNested("adventure.subtitle")));
						}
					} else{
						$player->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
					}
				break;

				case 3:
					if($player->hasPermission("gmui.spectator")){
						$player->setGamemode(2);
						$player->sendMessage($this->getConfig()->getNested("spectator.msg"));
						$player->addTitle(($this->getConfig()->getNested("spectator.title")), ($this->getConfig()->getNested("spectator.subtitle")));
					} else{
						$player->sendMessage($this->getConfig()->getNested("exit.nopermgm"));
					}
				break;

				case 4:
					$player->sendMessage($this->getConfig()->getNested("exit.msg"));
				break;
			}
		});
		$form->setTitle($this->config->getNested("menu.title-form"));
		$form->setContent($this->config->getNested("menu.description-form"));
		$form->addButton($this->config->getNested("creative.button.name"));
		$form->addButton($this->config->getNested("survival.button.name"));
		$form->addButton($this->config->getNested("adventure.button.name"));
		$form->addButton($this->config->getNested("spectator.button.name"));
		$form->addButton($this->config->getNested("exit.button.cancel"), 0, $this->getConfig->getNested("exit.button.image"));
		$form->sendToPlayer($player);
		return $form;
	}
}
