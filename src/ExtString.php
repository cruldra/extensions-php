<?php

namespace ExtensionsPhp;

class ExtString
{
    private string $str;

    /**
     * ExtString constructor.
     * @param string $str
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
    public function replaceBefore(string $search, string $replace): ExtString|static
    {
        //要替换的字符串在当前字符串中首次出现的位置
        $pos = strpos($this->str, $search);
        if ($pos !== false) {
            return new ExtString(substr_replace($this->str, $replace, 0, $pos));
        }
        return $this;
    }

    /**
     * 截取``$this->str``中``$search``之后的部分
     *
     * ```php
     * $str = new ExtString('hello world');
     * $str.substringAfter('hello'); // world
     * ```
     *
     * @param string $search 要搜索的字符串
     * @return ExtString|static   如果搜索到了则返回一个新的字符串对象,否则返回当前对象
     */
    public function substringAfter(string $search): ExtString|static
    {
        $pos = strpos($this->str, $search);
        if ($pos !== false) {
            return new ExtString(substr($this->str, $pos + strlen($search)));
        }
        return $this;
    }

    /**
     * 截取``$this->str``中``$search``之前的部分
     *
     * ```php
     * $str = new ExtString('hello world');
     * $str.substringBefore('world'); // hello
     * ```
     *
     * @param string $search 要搜索的字符串
     * @return ExtString|static   如果搜索到了则返回一个新的字符串对象,否则返回当前对象
     */
    public function substringBefore(string $search): ExtString|static
    {
        $pos = strpos($this->str, $search);
        if ($pos !== false) {
            return new ExtString(substr($this->str, 0, $pos));
        }
        return $this;
    }


    /**
     * 去除字符串两边的空白字符
     *
     * ```php
     * $str = new ExtString(' hello world ');
     * $str.trim(); // hello world
     * ```
     * @return ExtString 返回一个新的字符串对象
     */
    function trim(): ExtString
    {
        return new ExtString(trim($this->str));
    }

    /**
     * 转换为小写
     *
     * ```php
     * $str = new ExtString('HELLO WORLD');
     * $str.toLowerCase(); // hello world
     * ```
     * @return ExtString 返回一个新的字符串对象
     */
    function toLowerCase(): ExtString
    {
        return new ExtString(strtolower($this->str));
    }

    /**
     * 转换为驼峰命名
     *
     * ```php
     * $str = new ExtString('hello_world');
     * $str.toCamelCase(); // helloWorld
     * ```
     * @return ExtString 返回一个新的字符串对象
     */
    function toCamelCase(): ExtString
    {
        return new ExtString(preg_replace_callback('/_([a-zA-Z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $this->str));
    }


    /**
     * 转换为大写
     *
     * ```php
     * $str = new ExtString('hello world');
     * $str.toUpperCase(); // HELLO WORLD
     * ```
     * @return ExtString 返回一个新的字符串对象
     */
    function toUpperCase(): ExtString
    {
        return new ExtString(strtoupper($this->str));
    }

    /**
     * 转换为蛇形命名
     *
     * ```php
     * $str = new ExtString('helloWorld');
     * $str.toSnakeCase(); // hello_world
     * ```
     * @return ExtString 返回一个新的字符串对象
     */
    function toSnakeCase(): ExtString
    {
        return new ExtString(preg_replace_callback('/([A-Z])/', function ($matches) {
            return '_' . strtolower($matches[1]);
        }, $this->str));
    }

    /**
     * 转换为中划线命名
     *
     * ```php
     * $str = new ExtString('helloWorld');
     * $str.toKebabCase(); // hello-world
     * ```
     * @return ExtString 返回一个新的字符串对象
     */
    function toKebabCase(): ExtString
    {
        return new ExtString(preg_replace_callback('/([A-Z])/', function ($matches) {
            return '-' . strtolower($matches[1]);
        }, $this->str));
    }

    /**
     * 判断此字符串是否以``$search``开头,如果``$search``是一个正则表达式,则使用正则表达式进行匹配
     * ```php
     * $str = new ExtString('hello world');
     * $str.startWith('hello'); // true
     * $str.startWith('/^hello/'); // true
     * ```
     *
     * @param string $search 要搜索的字符串
     * @return bool
     */
    public function startWith(string $search): bool
    {
        if (ExtString::from($search)->isRegex()) {
            return preg_match($search, $this->str) === 1;
        }
        return str_starts_with($this->str, $search);
    }

    /**
     * 判断此字符串是否是一个正则表达式
     *
     * ```php
     * $str = new ExtString('/^hello world$/');
     * $str.isRegex(); // true
     * ```
     *
     * @return bool
     */
    function isRegex(): bool
    {
        set_error_handler(function () {
        }, E_WARNING); // Suppress warnings
        $isRegex = @preg_match($this->str, '') !== FALSE;
        restore_error_handler(); // Restore the original error handler
        return $isRegex;
    }

    /**
     * 返回原始字符串
     * @return string
     */
    public function original(): string
    {
        return $this->str;
    }


    public function __toString(): string
    {
        return $this->str;
    }

}
