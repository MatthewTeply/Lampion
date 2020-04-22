<?php

namespace Lampion\Debug;

class Console {

    public function log($var) {
        $type = \gettype($var);

        switch($type) {
            case 'array':
            case 'object':
                $var = \json_encode($var);
                break;
            case 'string':
                $var = '\'' . $var . '\'';
                break;
            case 'int':
                $var = (int)$var;
                break;
            case 'boolean':
                $var = $var ? 'true' : 'false';
                break;
        }

        echo '<script> console.log(' . $var . '); </script>';
    }

}