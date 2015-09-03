<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace MyOwnWorld\Entities;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\Monster;
use pocketmine\entity\ProjectileSource;

class Skeleton extends Monster implements ProjectileSource{
	const NETWORK_ID = 34;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;

	public function getName(){
		return "Skeleton";
	}

	public function spawnTo(Player $player){

		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Skeleton::NETWORK_ID;
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
		$drops = [
			ItemItem::get(ItemItem::ARROW, 0, mt_rand(0,2)),
			ItemItem::get(ItemItem::BONE, 0, mt_rand(0,2)),
		];
		if($this->lastDamageCause instanceof EntityDamageByEntityEvent and $this->lastDamageCause->getEntity() instanceof Player){
			if(\mt_rand(0, 199) < 5){
				switch(\mt_rand(0, 10)){
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
						$drops[] = ItemItem::get(ItemItem::BOW, 0, 1);
						break;
					case 5:
					case 6:
					case 7:
						$drops[] = ItemItem::get(ItemItem::CHAIN_CHESTPLATE, 0, 1);
						break;
					case 8:
					case 9:
						$drops[] = ItemItem::get(ItemItem::GOLD_CHESTPLATE, 0, 1);
						break;
					case 10:
						$drops[] = ItemItem::get(ItemItem::IRON_CHESTPLATE, 0, 1);
						break;
				}
			}
		}

		return $drops;
	}
}
