<?php

namespace DavyCraft648\GameModeUI;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

Class Main extends PluginBase
{
	public function onEnable() {
		$this->checkConfig();
	}

	private function checkConfig(): void
	{
		$this->saveDefaultConfig();
		if ($this->myConf()->get("configVersion") !== $this->getFullName()){
			$this->getLogger()->warning("Plugin not enabled due to invalid or outdated config");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
	}

	public function myConf(): Config
	{
		return new Config($this->getDataFolder() . "config.yml", Config::YAML);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		if ($command->getName() === "gamemodeui") {
			if (!($sender instanceof Player)) {
				$sender->sendMessage(TextFormat::colorize($this->myConf()->getNested("notPlayer")));
				return false;
			}
			/** @var Player $player */
			$player = $sender;
			if (!$player->hasPermission("gamemodeui.form")) return false;
			$this->gameModeForm($player);
		}
		return true;
	}

	/**
	 * @param Player $player
	 * @return SimpleForm
	 */
	private function gameModeForm(Player $player)
	{
		$form = new SimpleForm(function (Player $player, $data = null) {
			$config = $this->myConf();
			switch($data){
				case "creative":
					$player->setGamemode(1);
					if ($config->getNested("creative.message") !== "")
						$player->sendMessage(TextFormat::colorize($config->getNested("creative.message")));
					break;
				case "survival":
					$player->setGamemode(0);
					if ($config->getNested("survival.message") !== "")
						$player->sendMessage(TextFormat::colorize($config->getNested("survival.message")));
					break;
				case "spectator":
					$player->setGamemode(3);
					if ($config->getNested("spectator.message") !== "")
						$player->sendMessage(TextFormat::colorize($config->getNested("spectator.message")));
					break;
				case "adventure":
					$player->setGamemode(2);
					if ($config->getNested("adventure.message") !== "")
						$player->sendMessage(TextFormat::colorize($config->getNested("adventure.message")));
					break;
				default:
					if ($config->getNested("exit.message") !== "")
						$player->sendMessage(TextFormat::colorize($config->getNested("exit.message")));
			}
		});
		$config = $this->myConf();
		$form->setTitle(TextFormat::colorize($config->getNested("menu.titleForm")));
		$form->setContent(TextFormat::colorize($config->getNested("menu.descriptionForm")));
		if ($player->hasPermission("gamemodeui.creative"))
			$form->addButton(TextFormat::colorize($config->getNested("creative.button.name", "Creative")), $config->getNested("creative.image.type", -1), $config->getNested("creative.image.path", ""), "creative");
		if ($player->hasPermission("gamemodeui.survival"))
			$form->addButton(TextFormat::colorize($config->getNested("survival.button.name", "Survival")), $config->getNested("survival.image.type", -1), $config->getNested("survival.image.path", ""), "survival");
		if ($player->hasPermission("gamemodeui.spectator"))
			$form->addButton(TextFormat::colorize($config->getNested("spectator.button.name", "Spectator")), $config->getNested("spectator.image.type", -1), $config->getNested("spectator.image.path", ""), "spectator");
		if ($player->hasPermission("gamemodeui.adventure"))
			$form->addButton(TextFormat::colorize($config->getNested("adventure.button.name", "Adventure")), $config->getNested("adventure.image.type", -1), $config->getNested("adventure.image.path", ""), "adventure");
		$form->addButton(TextFormat::colorize($config->getNested("exit.button.name", "Exit")), $config->getNested("exit.image.type", -1), $config->getNested("exit.image.path", ""), "exit");
		$player->sendForm($form);
		return $form;
	}
}
