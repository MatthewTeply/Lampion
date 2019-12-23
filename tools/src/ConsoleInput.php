<?php

class ConsoleInput
{
    public $type;
    public $app;
    public $path;
    public $flags;

    public function read($argv) {
        $this->type = $argv[1];
        $this->app  = $argv[2];
        $this->path = $argv[3];

        for ($i = 4; $i < sizeof($argv); $i++) {
            $this->flags[] = $argv[$i];
        }
    }
}