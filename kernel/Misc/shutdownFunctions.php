<?php

function loadScripts() {
    foreach ($_SESSION['Lampion']['scripts'] as $script) {
        echo "<script src='$script'></script>";
    }
}

\Lampion\Core\Scheduler::registerShutdownFunction('loadScripts');
