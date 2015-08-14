<?php

namespace Nova\Helpers;

/**
* Link helper
*/
class Link extends Html
{
    /**
     * Method return html a tag.
     * Example: Link::to('home', 'Home page', ['class' => 'link'])
     * Return: <a href='www.site.domain/home' class='link'>Home page</a>
     *
     * @param string $href      URI string
     * @param string $title     Link title
     * @param array $options    Html elements attributes
     * @return string (html)
     */
    public static function to($href, $title, $options = [])
    {
        return static::tag('a', $title, array_merge(['href' => ROOT_URL . $href], $options));
    }
}