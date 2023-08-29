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

    public static function from(string $str): static
    {
        return new static($str);
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
     * @param string $replace 要替换成的字符串
     * @return ExtString    返回一个新的字符串对象
     */
    public function replace(array|string $search, string $replace): ExtString
    {
        return new ExtString(str_replace($search, $replace, $this->str));
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
     * @return ExtArray 返回分隔后的数组
     */
    public function split(string $delimiter): ExtArray
    {
        return ExtArray::from(explode($delimiter, $this->str));
    }

    /**
     * 将``$this->str``中第一次出现``$search``字符串之前的部分替换成``$replace``
     * @param string $search 要替换的字符串
     * @param string $replace 要替换成的字符串
     * @return ExtString|static   如果替换成功则返回一个新的字符串对象,否则返回当前对象
     */
    function replaceBefore(string $search, string $replace): ExtString|static
    {
        //要替换的字符串在当前字符串中首次出现的位置
        $pos = strpos($this->str, $search);
        if ($pos !== false) {
            return new ExtString(substr_replace($this->str, $replace, 0, $pos));
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->str;
    }

}
