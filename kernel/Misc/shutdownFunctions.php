<?php

function loadScripts() {
    foreach ($_SESSION['Lampion']['scripts'] as $script) {
        echo "<script src='$script'></script>";
    }
}

function sessionWriteClose() {
    session_write_close();
}

\Lampion\Core\Scheduler::registerShutdownFunction('loadScripts');
\Lampion\Core\Scheduler::registerShutdownFunction('sessionWriteClose');
