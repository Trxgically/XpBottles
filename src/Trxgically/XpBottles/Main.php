<?php

declare(strict_types=1);

namespace Trxgically\XPBottles;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat as TF;
use pocketmine\{entity\object\ExperienceOrb,
    entity\projectile\ExperienceBottle,
    entity\projectile\SplashPotion,
    event\player\PlayerItemConsumeEvent,
    item\ProjectileItem,
    Server,
    Player};
use pocketmine\plugin\PluginBase;

use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};

class Main extends PluginBase implements Listener{

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("XPBottles enabled!");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {


        return true;
    }

    public function onPlayerDeath(\pocketmine\event\player\PlayerDeathEvent $e){
        $drops = $e->getDrops();
        $drops[] = \pocketmine\item\Item::get(\pocketmine\item\Item::AIR, 0, 1);

        $name = $e->getPlayer();
        $username = $name->getName();

        $xp = $e->getPlayer()->getCurrentTotalXp();


        $item = Item::get(Item::EXPERIENCE_BOTTLE, 0, 1);
        $item->setCustomName(TF::BOLD . TF::GREEN . "Experience Bottle");
        $item->setLore(["\n" . TF::BOLD . TF::WHITE . $username . TF::RESET . TF::GRAY . " was killed!\n" . TF::DARK_GRAY . "Click or tap to claim " . $xp . " experience!"]);
        $item->setNamedTagEntry(new tag\StringTag("experience", (string)$xp));
        $item->getMaxStackSize(1);
        $e->setXpDropAmount(0);

        if($xp > 0){
            $e->setDrops([$item]); // Set the new drops array
        }else if($xp <= 0){
            return;
        }

    }

    public function onInteract(PlayerInteractEvent $e){

        $player = $e->getPlayer();
        $username = $player->getName();
        $item = $e->getItem();


        if($item->hasCustomName(TF::BOLD . TF::GREEN . "Experience Bottle")){
            if($item->getId() == 384){
                $tag = $item->getNamedTagEntry("experience")->getValue();
                $e->getPlayer()->addXp((int)$tag);
                $e->getPlayer()->sendMessage(TF::BOLD . TF::GREEN . "+" . (int)$tag . "XP");
            }
        }
    }

    public function onProjectileLaunch(ProjectileLaunchEvent $e) {
        $entity = $e->getEntity();
        $player = $entity->getOwningEntity();

        if ($player === null) return;
        if (!$player instanceof Player) return;

        $inv = $player->getInventory();
        $item = $inv->getItemInHand();
        $name = $item->getName();

        if ($name === TF::BOLD . TF::GREEN . "Experience Bottle") {
            $e->setCancelled();
        }

    }

}
