<?php

# Sorting out URL and app
if(isset($_GET['url'])) {
    $firstUrlParam = explode("/", $_GET['url'])[0];

    if(is_dir(APP . $firstUrlParam)) {
        $app = $firstUrlParam;

        if(is_array(explode($firstUrlParam . "/", $_GET['url'])) > 1)
            $_GET['url'] = explode($firstUrlParam . "/", $_GET['url'])[1];
    }

    else {
        $app = DEFAULT_APP;
    }
}

else {
    $app = DEFAULT_APP;
}