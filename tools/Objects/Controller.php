<?php

class Controller
{
    public $app;
    public $path;

    public function __construct(string $app, string $path) {
        $this->app  = $app;
        $this->path = $path;
    }

    public function generate() {
        $dummy = readfile("Dummies/Controller.dummy");

        $this->path = explode("/", $this->path);

        foreach ($this->path as $key => $value) {
            $this->path[$key] = ucfirst($value);
        }

        $dummy = str_replace("{{ APP }}", $this->app, $dummy);
        $dummy = str_replace("{{ NAMESPACE }}", implode("\\", $this->path), $dummy);
        $dummy = str_replace("{{ NAME }}", end($this->path), $dummy);

        echo $dummy;
    }
}