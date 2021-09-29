<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 28/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\task;

use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;
use zomarrd\core\network\Network;

abstract class TaskBase
{
    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }

    /**
     * @return TaskScheduler
     */
    public function getTaskScheduler(): TaskScheduler
    {
        return $this->getNetwork()->getTaskScheduler();
    }

    /**
     * @param Task $task
     * @param int  $period
     *
     * @return TaskHandler
     */
    public function registerTask(Task $task, int $period): TaskHandler
    {
        return $this->getTaskScheduler()->scheduleRepeatingTask($task, $period);
    }
}