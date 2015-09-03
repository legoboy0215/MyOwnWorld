<?php

namespace MyOwnWorld\Entities;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\Monster;
use pocketmine\entity\Explosive;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\level\Explosion;

class Creeper extends Monster implements Explosive {
    const NETWORK_ID = 33;

    public $width = 0.6;
    public $length = 0.6;
    public $height = 1.8;

    public function getName(){
        return "Creeper";
    }

    public function spawnTo(Player $player){
        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = Creeper::NETWORK_ID;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        $player->addEntityMotion($this->getId(), $this->motionX, $this->motionY, $this->motionZ);

        parent::spawnTo($player);
    }

    public function getDrops(){
        $drops = [];
        if($this->lastDamageCause instanceof EntityDamageByEntityEvent and $this->lastDamageCause->getEntity() instanceof Player){
            if(\mt_rand(0, 10) < 8){
                $count = 1;
                if(\mt_rand(0, 10) < 5){
                    $count = 2;

                }
                $drops[] = ItemItem::get(ItemItem::GUNPOWDER, 0, $count);
            }
        }

        return $drops;
    }

    public function explode(){
        $this->server->getPluginManager()->callEvent($ev = new ExplosionPrimeEvent($this, 3));

        if(!$ev->isCancelled()){
            $explosion = new Explosion($this, $ev->getForce(), $this);
            if($ev->isBlockBreaking()){
                $explosion->explodeA();
            }
            $explosion->explodeB();
        }
        $this->kill();
    }
}
