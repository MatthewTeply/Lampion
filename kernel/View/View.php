<?php

namespace Lampion\View;

use Lampion\Application\Application;
use Lampion\Controller\ControllerLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Markup;
use Lampion\Http\Url;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class for managing views
 * @author Matyáš Teplý
 */
class View {

    private $twig;
    private $app;
    private $isPlugin;

    /**
     * View constructor.
     * @param string $templateFolder
     * @param string $app
     * @param bool   $isPlugin
     */
    public function __construct(string $templateFolder, string $app, bool $isPlugin = false)
    {
        $loader = new FilesystemLoader(strtolower($templateFolder));
        $this->twig = new Environment($loader, [
            'debug' => true
        ]);

        $this->twig->addExtension(new \Twig\Extension\DebugExtension());

        # Register custom functions
        $this->customFunctions();

        # Register custom filters
        $this->customFilters();

        $this->app = empty($app) ? Application::name() : strtolower($app);
        $this->isPlugin = $isPlugin;
    }

    /**
     * Renders a templates
     * @param string $path
     * @param array  $args
     * @return $this
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(string $path, array $args = []) {
        if($this->isPlugin) {
            $initialPath = PLUGINS;
        }

        else {
            $initialPath = APP;
        }

        $args['__css__']     = WEB_ROOT . $initialPath . $this->app . CSS;
        $args['__scripts__'] = WEB_ROOT . $initialPath . $this->app . SCRIPTS;
        $args['__img__']     = WEB_ROOT . $initialPath . $this->app . IMG;
        $args['__storage__'] = WEB_ROOT . $initialPath . $this->app . STORAGE;
        $args['__webStorage__'] = WEB_ROOT . $initialPath . $this->app . STORAGE;
        $args['__webRoot__'] = WEB_ROOT;

        echo $this->twig->render("$path.twig", !empty($args) ? $args : get_object_vars($this));

        return $this;
    }

    public function setFilter(string $filterName, $func) {
        $filter = new TwigFilter($filterName, $func);

        $this->twig->addFilter($filter);
    }

    /**
     * Loads a templates, if a Controller with the same directory path is found, it is used to render the templates with variables inserted
     * @param string $path
     * @param array  $args
     * @param bool   $rawTemplate
     * @param string $app
     * @return Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function load(string $path, array $args = [], bool $rawTemplate = false) {
        $loader = new ControllerLoader($this->app);

        if($loader->controller($path) && !$rawTemplate) {
            ob_start();
                echo $loader->controller($path)->index();
            return new Markup(ob_get_clean(), 'UTF-8');
        }

        else {
            if($this->isPlugin) {
                $initialPath = PLUGINS;
            }
    
            else {
                $initialPath = APP;
            }

            # Pass system variables
            $args['__css__']        = WEB_ROOT . $initialPath . $this->app . CSS;
            $args['__scripts__']    = WEB_ROOT . $initialPath . $this->app . SCRIPTS;
            $args['__img__']        = WEB_ROOT . $initialPath . $this->app . IMG;
            $args['__webStorage__'] = WEB_ROOT . $initialPath . $this->app . STORAGE;
            $args['__webRoot__']    = WEB_ROOT;

            return new Markup($this->twig->render("$path.twig", $args), 'UTF-8');
        }
    }

    private function customFunctions() {
        $this->twig->addFunction(new TwigFunction('icon', function($icon) {
            return file_get_contents(ROOT . APP . Application::name() . ASSETS . 'images/icons/' . $icon . '.svg');
        }));

        $this->twig->addFunction(new TwigFunction('path', function($route) {
            if(!$route) {
                return '';
            }
            
            return Url::link($route);
        }));
    }

    private function customFilters() {
        $this->setFilter('json_decode', function($json) {
            return json_decode($json);
        });

        $this->setFilter('isImg', function($src) {
            $imgExts = [
                'png',
                'jpg',
                'svg',
                'gif',
                'jpeg'
            ];

            $srcExplode = explode('.', $src);

            return in_array(strtolower(end($srcExplode)), $imgExts);
        });

        $this->setFilter('toArray', function ($stdClassObject) {
            $response = array();
            foreach ($stdClassObject as $key => $value) {
                $response[$key] = $value;
            }
            return $response;
        });

        $this->setFilter('getClassName', function ($object) {
            $className = explode('\\', get_class($object));

            return end($className);
        });

        $this->setFilter('getClassNameFull', function ($object) {
            return get_class($object);
        });
    }
}
