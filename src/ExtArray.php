<?php

namespace ExtensionsPhp;

use ArrayAccess;
use Iterator;

/**
 * @template T
 */
class ExtArray implements ArrayAccess, Iterator
{
    /**
     * @var T[]
     */
    private array $arr;
    private int $position = 0;

    /**
     * ExtArray constructor.
     * @param array $arr
     */
    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    public static function from(array $arr): static
    {
        return new static($arr);
    }

    /**
     * 遍历该数组,将数组中的每一个元素作为参数传入``$callback``中并执行
     *
     * ```php
     * ExtArray::from([1,2,3])->forEach(function($value,$key){
     *    echo $value;
     * });
     * // 输出: 1 2 3
     * ```
     *
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
     * 将数组中的元素连接成字符串并返回
     * ```php
     * ExtArray::from([1,2,3])->joinToString();
     * // 输出: "1,2,3"
     * ```
     *
     * @param string $separator
     * @return ExtString
     */
    public function joinToString(string $separator = ","): ExtString
    {
        return ExtString::from(implode($separator, $this->arr));
    }

    /**
     * 遍历该数组,将数组中的每一个元素作为参数传入``$callback``中并执行,并将``$callback``的返回值作为新数组的元素
     *
     * ```php
     * ExtArray::from([1,2,3])->map(function($value,$key){
     *    return $value * 2;
     * });
     * // 输出: [2,4,6]
     * ```
     *
     * @param callable(T, mixed):mixed $callback 回调函数,该函数接受两个参数,第一个参数为数组中的元素,第二个参数为该元素的键名
     * @return static 返回一个新的数组对象
     */
    public function map(callable $callback): static
    {
        $arr = [];
        foreach ($this->arr as $key => $value) {
            $arr[$key] = $callback($value, $key);
        }
        return new static($arr);
    }

    /**
     * 对值进行分组
     * ```php
     *  ExtArray::from([
     * {group:'1',key:'g1k1',value:'v'},
     * {group:'1',key:'g1k2',value:'v'},
     * {group:'2',key:'g2k1',value:'v'},
     * {group:'3',key:'g3k1',value:'v'},
     * ] )->groupBy('group',function($item){
     * return [
     * 'key'=>$item['key'],
     * 'value'=>$item['value']
     * ];
     * });
     * // 输出: [
     * [
     * 'name'=>'1',
     * 'values'=>[
     * ['key'=>'g1k1','value'=>'v'],
     * ['key'=>'g1k2','value'=>'v'],
     * ]
     * ],
     * [
     * 'name'=>'2',
     * 'values'=>[
     * ['key'=>'g2k1','value'=>'v'],
     * ]
     * ],
     * [
     * 'name'=>'3',
     * 'values'=>[
     * ['key'=>'g3k1','value'=>'v'],
     * ]
     * ],
     * ]
     * ```
     *
     * @param $key
     * @param $mapper
     * @param string $groupNameField
     * @param string $groupValuesField
     * @return $this
     */
    public function groupBy($key, $mapper, string $groupNameField = 'name', string $groupValuesField = 'values'): static
    {
        $result = [];

        foreach ($this->arr as $item) {
            $groupKey = $item[$key];

            if (!isset($result[$groupKey])) {
                $result[$groupKey] = [
                    $groupNameField => $groupKey,
                    $groupValuesField => [],
                ];
            }

            $result[$groupKey][$groupValuesField][] = $mapper($item);
        }

        return  new static(array_values($result));
    }

    /**
     * 删除数组中符合条件的元素
     *
     * ```php
     *  ExtArray::from([1,2,3])->dropWhere(function($value,$key){
     *   return $value > 1;
     * }); // [1]
     * ```
     *
     * @param callable(T, mixed):bool $callback 回调函数,该函数接受两个参数,第一个参数为数组中的元素,第二个参数为该元素的键名
     * @return static 返回一个新的数组对象
     */
    public function dropWhere(callable $callback): static
    {
        $arr = [];
        foreach ($this->arr as $key => $value) {
            if (!$callback($value, $key)) {
                $arr[$key] = $value;
            }
        }
        return new static($arr);
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

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->arr[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->arr[$this->position]);
    }

    /**
     * 返回原数组
     * @return array
     */
    public function original(): array
    {
        return $this->arr;
    }


    /**
     * 返回数组中最后一个元素
     * @return mixed
     */
    public function last(): mixed
    {
        return end($this->arr);
    }

    /**
     * 将数组中除最后一个元素外的所有其它元素作为新数组返回
     * @return static
     */
    public function subArrayBeforeLast(): static
    {
        $arr = $this->arr;
        array_pop($arr);
        return new static($arr);
    }

    /**
     * 返回数组中第一个元素
     * @return mixed
     */
    public function first(): mixed
    {
        return reset($this->arr);
    }
}
