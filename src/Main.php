<?php

declare(strict_types=1);

namespace Poggit\SecurityExam;

use pocketmine\command\{Command, CommandSender};
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use SQLite3;

class Main extends PluginBase implements Listener {
	private SQLite3 $db;
	private array $checkpoints = [];

	public function onEnable() {
		$this->db = new SQLite3($this->getDataFolder() . "invites.db");
		$this->db->query("CREATE TABLE invites (inviter TEXT, invited TEXT, PRIMARY KEY(inviter, invited))");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if($command->getName() === "invite") {
			$target = $args[0];
			$this->db->query("INSERT INTO invites VALUES ('{$sender->getName()}', '$target')");
		} elseif($command->getName() === "checkpoint") {
			$config = new Config($this->getDataFolder() . $args[0] . ".yml");
			$config->set("x", $sender->getLocation()->x);
			$config->set("y", $sender->getLocation()->y);
			$config->set("z", $sender->getLocation()->z);
			$config->save();

			$this->checkpoints[$args[0]] = $config->getAll();
		}

		return true;
	}
}
