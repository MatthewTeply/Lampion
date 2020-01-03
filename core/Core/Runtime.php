<?php

namespace Lampion\Core;

class Runtime
{
    /**
     * @param $ru
     * @param $rus
     * @param $index
     */
    public static function rutime($ru, $rus, $index) {
        $_SESSION['Lampion']['execTime'][$index] = ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
    }

    /**
     * Returns execution time in ms
     *  utime = computations
     *  stime = system calls
     * @return array
     */
    public static function execTime() {
        return $_SESSION['Lampion']['execTime'];
    }

    /**
     * @return array
     */
    public static function dbInfo() {
        return $_SESSION['Lampion']['DB'];
    }

    public static function errors() {
        // TODO: Add error handling

        var_dump($GLOBALS['_ERRORS']);
    }

    public static function setDbInfo(array $queryInfo, array $params, $results, $message, $status, $code) {
        $paramsInfo = [];

        $queryTranslated = $queryInfo['query'];

        foreach ($params as $key => $param) {
            $paramsInfo[$key]['value'] = $param;
            $paramsInfo[$key]['type']  = gettype($param);

            $queryTranslated = str_replace($key, is_string($param) ? "'$param'" : $param, $queryTranslated);
        }

        $_SESSION['Lampion']['DB']['queryCount']++; // Add one to query count

        # Add query to the DB array
        $_SESSION['Lampion']['DB']['queries'][] = [
            'query' => $queryInfo['query'],
            'queryTranslated' => $queryTranslated,
            'info' => $queryInfo,
            'params' => $paramsInfo,
            'results' => $results,
            'message' => $message,
            'status' => $status,
            'code' => $code
        ];
    }
}