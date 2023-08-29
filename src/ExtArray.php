<?php

namespace ExtensionsPhp;
use ArrayAccess;

/**
 * @template T
 */
class ExtArray implements ArrayAccess
{
    /**
     * @var T[]
     */
    private array $arr;

    /**
     * ExtArray constructor.
     * @param array $arr
     */
    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }


    /**
     * 遍历该数组,将数组中的每一个元素作为参数传入``$callback``中并执行
     * @param callable(T, mixed):void $callback 回调函数,该函数接受两个参数,第一个参数为数组中的元素,第二个参数为该元素的键名
     * @return static 返回当前对象
     */
    public function forEach(callable $callback): static
    {
        foreach ($this->arr as $key => $value) {
            $callback($value, $key);
        }
        return $this;
    }

    /**
     * 返回数组中所有的键
     * @return ExtArray  返回一个包含原数组所有键的新数组
     */
    public function keys(): ExtArray
    {
        return new ExtArray(array_keys($this->arr));
    }

    /**
     * 返回数组中所有的值
     * @return  ExtArray 返回一个包含原数组所有值的新数组
     */
    public function values(): ExtArray
    {
        return new ExtArray(array_values($this->arr));
    }

    /**
     * 判断给定的值``$value``是否在数组中
     * @param mixed $value 要判断的值
     * @return bool  如果``$value``在数组中则返回``true``,否则返回``false``
     */
    public function contains(mixed $value): bool
    {
        return in_array($value, $this->arr);
    }

    /**
     * 判断给定的键``$key``是否在数组中
     * @param mixed $key 要判断的键
     * @return bool  如果``$key``在数组中则返回``true``,否则返回``false``
     */
    public function containsKey(mixed $key): bool
    {
        return array_key_exists($key, $this->arr);
    }

    public function __toString(): string
    {
        return json_encode($this->arr);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->arr[$offset]);
    }

    public function offsetGet(mixed $offset)
    {
        return $this->arr[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->arr[] = $value;
        } else {
            $this->arr[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->arr[$offset]);
    }
}
