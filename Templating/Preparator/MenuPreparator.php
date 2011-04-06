<?php

namespace Knplabs\Bundle\MenuBundle\Templating\Preparator;

use Symfony\Component\Templating\Helper\Helper;
use Knplabs\Bundle\MenuBundle\ProviderInterface;
use Knplabs\Bundle\MenuBundle\MenuItem;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class MenuPreparator
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @param ProviderInterface
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function attributes($attributes)
    {
        if ($attributes instanceof \Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        return implode('', array_map(array($this, 'attributesCallback'), array_keys($attributes), array_values($attributes)));
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

    private function attribute($name, $value)
    {
        return sprintf('%s="%s"', $name, true === $value ? $name : $value);
    }

    /**
     * Prepares an attribute key and value for HTML representation.
     *
     * It removes empty attributes, except for the value one.
     *
     * @param  string $name   The attribute name
     * @param  string $value  The attribute value
     *
     * @return string The HTML representation of the HTML key attribute pair.
     */
    private function attributesCallback($name, $value)
    {
        if (false === $value || null === $value || ('' === $value && 'value' != $name)) {
            return '';
        } else {
            return ' '.$this->attribute($name, $value);
        }
    }

    /**
     *
     * @param MenuItem $menuItem
     * @param integer $depth The depth each child should render
     * @return array
     */
    public function getItemAttributes(MenuItem $item)
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->shouldBeRendered()) {
            return;
        }

        $depth = $item->getLevel();

        // explode the class string into an array of classes
        $class = ($item->getAttribute('class')) ? explode(' ', $item->getAttribute('class')) : array();

        if ($item->getIsCurrent()) {
            $class[] = 'current';
        }
        elseif ($item->getIsCurrentAncestor($depth)) {
            $class[] = 'current_ancestor';
        }

        if ($item->actsLikeFirst()) {
            $class[] = 'first';
        }
        if ($item->actsLikeLast()) {
            $class[] = 'last';
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        return $attributes;
    }
}
