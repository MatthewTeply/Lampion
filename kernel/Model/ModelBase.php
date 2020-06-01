<?php

namespace Lampion\Model;

/**
 * (LEGACY CODE, PLANNED FOR DEPRECATION)
 * @author Matyáš Teplý
 */
class ModelBase
{
    public function load() {
        return new ModelLoader();
    }
}