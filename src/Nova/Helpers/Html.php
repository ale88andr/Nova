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

    public static function importCss($src)
    {
        if (is_array($src)) {
            foreach ($src as $file) {
                static::inc($file, 'css');
            }
        } else {
            static::inc($src, 'css');
        }
    }

    public static function importJS($src)
    {
        if (is_array($src)) {
            foreach ($src as $file) {
                static::inc($file, 'js');
            }
        } else {
            static::inc($src, 'js');
        }
    }

    public static function inc($fileSource, $fileType){
        try {
            static::importAssets($fileSource, $fileType);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function importAssets($fileSource, $fileType)
    {
        switch ($fileType) {
            case('css'):
                $assetsPath = '/app' . '/' . 'assets/' . $fileType . '/' . $fileSource . '.' . $fileType;
                $publicPath = ROOT_URL . 'public' . DIRECTORY_SEPARATOR . $fileType . DIRECTORY_SEPARATOR . $fileSource . '.' . $fileType;
                if (file_exists(ROOT_APP . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $fileSource . '.' . $fileType)) {
                    echo '<link rel="stylesheet" type="text/css" href="'. $assetsPath . '">';
                } elseif (file_exists($publicPath)) {
                    echo '<link rel="stylesheet" type="text/css" href="'. $publicPath . '">';
                } else {
                    throw new Exception('File ' . $fileSource . '.' . $fileType . ' not found. Searched in: <br>' . $assetsPath . '<br>' . $publicPath);
                }
                break;
            case('js'):
                $assetsPath = ROOT_APP . 'assets' . DIRECTORY_SEPARATOR . 'javascript' . DIRECTORY_SEPARATOR . $fileSource . '.' . $fileType;
                $publicPath = ROOT . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $fileType . DIRECTORY_SEPARATOR . $fileSource . '.' . $fileType;
                if (file_exists($assetsPath)) {
                    echo '<script src="' . $assetsPath . '" type="text/javascript" encoding="UTF-8"></script>';
                } elseif (file_exists($publicPath)) {
                    echo '<script src="' . $publicPath . '" type="text/javascript" encoding="UTF-8"></script>';
                } else {
                    throw new Exception('File ' . $fileSource . '.' . $fileType . ' not found. Searched in: <br>' . $assetsPath . '<br>' . $publicPath);
                }
                break;
            default:
                throw new Exception('Unknown type of including assets file: ' . $fileType);
        }
    }

    public static function useCdn($cdn)
    {
        $fileNameArray = explode('.', $cdn);
        $fileType = end($fileNameArray);
        switch ($fileType){
            case('css'):
                echo '<link rel="stylesheet" href="' . $cdn . '">';
                break;
            case('js'):
                echo '<script src="' . $cdn . '"></script>';
                break;
            default:
                echo '';
        }
    }
}