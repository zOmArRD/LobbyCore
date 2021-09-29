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

final class TaskManager extends TaskBase
{
    public function __construct()
    {
        $this->load();
    }

    private function load(): void
    {
        $this->registerTask(new GlobalTask(), 1);
    }
}