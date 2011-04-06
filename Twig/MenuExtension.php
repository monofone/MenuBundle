<?php

namespace Knplabs\Bundle\MenuBundle\Twig;

use Knplabs\Bundle\MenuBundle\Templating\Preparator\MenuPreparator;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var MenuPreparator
     */
    protected $preparator;

    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * @param MenuPreparator
     */
    public function __construct(MenuPreparator $preparator)
    {
        $this->preparator = $preparator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'menu' => new \Twig_Function_Method($this, 'render', array(
                'is_safe' => array('html'),
            )),
            'menu_get' => new \Twig_Function_Method($this, 'get', array(
                'is_safe' => array('html'),
            )),
        );
    }

    /**
     * @param string $name
     * @param integer $depth (optional)
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
            $template = 'KnplabsMenuBundle:Menu:menu.html.twig';
        }

        return trim($this->environment->render($template, array(
            'item'  => $item,
            'menu' => $this->preparator,
        )));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }
}
