<?php

namespace Knplabs\Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Knplabs\Bundle\MenuBundle\Templating\Preparator\MenuPreparator;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class MenuHelper extends Helper implements \ArrayAccess
{
    /**
     * @var MenuPreparator
     */
    protected $preparator;

    /**
     * @param MenuPreparator
     */
    public function __construct(MenuPreparator $preparator, EngineInterface $engine)
    {
        $this->preparator = $preparator;
        $this->engine = $engine;
    }

    /**
     * @param string $name
     * @param string $path (optional)
     * @param integer $depth (optional)
     * @param string $template The PHP template to use for rendering
     * @return string
     */
    public function render($name, $path = null, $depth = null, $template = null)
    {
        $item = $this->preparator->get($name);
        
        $item->initialize(array('path' => $path));

        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || $depth === 0 || !$item->getShowChildren()) {
            return '';
        }
        
        if (null === $template) {
            $template = 'KnplabsMenuBundle:Menu:menu.html.php';
        }

        return trim($this->engine->render($template, array(
            'item'  => $item,
            'menu' => $this->preparator,
        )));
    }

    /**
     * @param string $name
     * @return \Knplabs\Bundle\MenuBundle\Menu
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        return $this->provider->getMenu($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($name)
    {
        return isset($this->menus[$name]);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet($name, $value)
    {
        return $this->menus[$name] = $value;
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset($name)
    {
        throw new \LogicException(sprintf('You can\'t unset a menu from a template (%s).', $name));
    }

}
