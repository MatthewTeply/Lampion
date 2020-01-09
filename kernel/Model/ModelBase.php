<?php

namespace Lampion\Model;

class ModelBase
{
    public function load() {
        return new ModelLoader();
    }
}