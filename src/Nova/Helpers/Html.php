<?php

namespace Nova\Helpers;

use \Exception;

class Html
{
    /**
     * Html void elements.
     *
     * @var array
     */
    public static $voidTags = ['area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr'
    ];

    /**
     * Method return html element.
     * Example: Html::tag('div', 'Div content', ['class' => 'div_tag'])
     * Return: <div class='div_tag'>Div content</div>
     *
     * @param string $tag Tag name string
     * @param string $content Tag content
     * @param array $options Html elements attributes
     * @return string (html)
     */
    public static function tag($tag, $content = false, $options = [])
    {
        $html = "<{$tag} " . static::optionsHandler($options) . '>';
        if (array_search($tag, static::$voidTags)) {
            return $html;
        }

        return $html . $content . '</' . $tag . '>';
    }

    /**
     * Parse html attributes for elements.
     *
     * @param array $options Html elements attributes
     * @return string
     */
    public static function optionsHandler($options = [])
    {
        if (is_array($options)) {
            $html_params = '';
            foreach ($options as $tag_attribute => $attribute_value) {
                if (!empty($attribute_value)) {
                    $html_params .= (is_bool($attribute_value)) ? "{$tag_attribute} "
                        : "{$tag_attribute}='{$attribute_value}' ";
                }
            }
            return $html_params;
        }
    }
}