<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/2/1
 * Time: 10:30
 */

namespace Hifone\Services\Parsers;


class ParseLink
{
    //将链接转换为<a>标签
    public $links = [];
    public $post;

    public function parse($post)
    {
        $this->post= $post;
        $this->links = $this->getLinks();
        $this->replace();
        return $this->post;
    }

    protected function replace()
    {
        foreach ($this->links as $link) {
            $search = $link;
            $replace = '<a href="' . $link . '">' . $link . '</a>';
            $this->post = str_replace($search, $replace, $this->post);
        }
    }

    protected function getLinks()
    {
        preg_match_all("/(?<!>|\"|\')https?:\/\/[^\r\n\s\"\'<>]*/i", $this->post, $links_tmp);
        return array_unique($links_tmp[0]);
    }

}