<?php

namespace ExtensionsPhp;

class ExtString
{
    private string $str;

    /**
     * ExtString constructor.
     * @param $str
     */
    public function __construct(string $str)
    {
        $this->str = $str;
    }


    /**
     * 替换字符串
     *
     * ```php
     *
     * $str = new ExtString('hello world');
     * $str.replace('world','php'); // hello php
     * $str.replace(['hello','world'],['hi','php']); // hi php
     * $str.replace(['hello','world'],'nihao'); // nihao nihao
     * ```
     *
     * @param array|string $search 要替换的字符串,如果是一个数组的话,则会替换所有的
     * @param string $replace   要替换成的字符串
     * @return static   返回当前对象
     */
    public function replace(array|string $search,string $replace): static
    {
        $this->str = str_replace($search, $replace, $this->str);
        return $this;
    }

    /**
     * 字符串分隔
     *
     * ```php
     *
     * $str = new ExtString('HELLO WORLD');
     * $str.split(' '); // ['HELLO','WORLD']
     * ```
     *
     * @param string $delimiter 分隔符
     * @return array  返回分隔后的数组
     */
    public function split(string $delimiter): array
    {
        return explode($delimiter, $this->str);
    }

    public function __toString(): string
    {
        return $this->str;
    }

}
