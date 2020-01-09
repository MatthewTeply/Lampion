<?php

namespace Lampion\Database;

use Lampion\Application\ApplicationManager;
use Lampion\Database\Query;

class Initializer
{
    public function __construct() {
        echo ApplicationManager::getApps()['monitor']->color;
    }
}
