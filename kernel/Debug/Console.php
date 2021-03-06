<?php

namespace Lampion\Debug;

use Lampion\Http\Request;

/**
 * A bit of a hack, this class is used for logging data out into the JS console
 * @author Matyáš Teplý
 */
class Console {

    public static function log($var) {
        $request = new Request();

        if($request->isAjax()) {
            return;
        }

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