<?php

namespace Lampion\Database;

use Lampion\Database\Query;

class Initializer
{
    /**
     * This method checks if tables that are required by Kernel exist, if they don't, they will be created
     */
    public static function checkKernelTables() {
        require_once KERNEL_USR . 'kernelTables.php';

        $kernelTables = kernelTables();

        foreach ($kernelTables as $table) {
            if(!Query::tableExists($table)) {
                Query::raw(file_get_contents(KERNEL_SQL . "$table.sql"));
            }
        }
    }
}
