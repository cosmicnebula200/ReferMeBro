<?php

namespace cosmicnebula200\ReferMeBro;

use cosmicnebula200\ReferMeBro\command\ReferCommand;
use cosmicnebula200\ReferMeBro\listener\EventListener;
use cosmicnebula200\ReferMeBro\messages\Messages;
use cosmicnebula200\ReferMeBro\player\PlayerManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class ReferMeBro extends PluginBase
{

    /** @var self */
    private static self $instance;
    /** @var Config */
    public static Config $forms;
    /** @var Messages */
    public static Messages $messages;
    /** @var DataConnector */
    private DataConnector $dataBase;
    /** @var PlayerManager */
    private PlayerManager $playerManager;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->saveResource('form.yml');
        $this->saveResource('messages.yml');
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        self::$forms = new Config($this->getDataFolder() . "form.yml", Config::YAML);
        self::$messages = new Messages();
        $this->getServer()->getCommandMap()->register("ReferMeBro", new ReferCommand($this, 'refer', "Refer players and gain rewards"));
        $this->initDataBase();
        $this->playerManager = new PlayerManager();
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    /**
     * @return void
     */
    public function initDataBase(): void
    {
        $db = libasynql::create($this, $this->getConfig()->get('database'), ['mysql' => 'mysql.sql', 'sqlite' => 'sqlite.sql']);
        $db->executeGeneric('refermebro.init');
        $db->waitAll();
        $this->dataBase = $db;
    }

    /**
     * @return DataConnector
     */
    public function getDataBase(): DataConnector
    {
        return $this->dataBase;
    }

    /**
     * @return PlayerManager
     */
    public function getPlayerManager(): PlayerManager
    {
        return $this->playerManager;
    }

}