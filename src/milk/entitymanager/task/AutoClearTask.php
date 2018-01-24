<?php

namespace milk\entitymanager\task;

use milk\entitymanager\EntityManager;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;

class AutoClearTask extends PluginTask{

    public function onRun(int $currentTick){
        $levelList = [];
        $levels = EntityManager::getData('autoclear.levels', []);
        if(count($levels) > 0){
            foreach($levels as $levelname){
                $level = Server::getInstance()->getLevelByName($levelname);
                if($level !== null) $levelList[] = $level;
            }
        }

        $list = EntityManager::getData('autoclear.entities', ['Projectile', 'Item']);
        foreach((count($levelList) > 0 ? $levelList : Server::getInstance()->getLevels()) as $level){
            foreach($level->getEntities() as $entity){
                $reflect = new \ReflectionClass(\get_class($entity));
                while(\true){
                    if(in_array($reflect->getShortName(), $list)){
                        $entity->close();
                        break;
                    }

                    if(($reflect = $reflect->getParentClass()) === \false || $reflect->getShortName() === 'Entity'){
                        break;
                    }
                }
            }
        }
    }

}
