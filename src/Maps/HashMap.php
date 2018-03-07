<?php
/**
 * MIT License
 *
 * Copyright (c) 2018 Dogan Ucar, <dogan@dogan-ucar.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace doganoo\PHPAlgorithms\Maps;

use doganoo\PHPAlgorithms\LinkedLists\SinglyLinkedList;
use doganoo\PHPAlgorithms\Util\MapUtil;

/**
 * HashMap class - implementation of a map using hashes in order to avoid collisions
 *
 * If you want to read more about the theory behind visit: https://dogan-ucar.de/php-hashmap-implementation/
 *
 * Class HashMap
 *
 * TODO implement entrySet
 * TODO implement values
 * TODO implement Java-like generics for key and value
 * TODO (optional) implement universal hashing
 *
 * @package doganoo\PHPAlgorithms\Maps
 */
class HashMap {
    /**
     * @var array $bucket the buckets containing the nodes
     */
    private $bucket = null;

    /**
     * @var int $maxSize the maximum number of buckets
     */
    private $maxSize = 128;

    /**
     * HashMap constructor creates an empty array.
     */
    public function __construct() {
        $this->initializeBucket();
    }

    /**
     * initializes the bucket in setting the
     * bucket attribute to an empty array.
     */
    private function initializeBucket() {
        $this->bucket = [];
    }

    /**
     * adds a node to the hash map
     *
     * @param Node $node
     * @return bool
     * @throws \doganoo\PHPAlgorithms\Exception\InvalidKeyTypeException
     * @throws \doganoo\PHPAlgorithms\Exception\UnsupportedKeyTypeException
     */
    public function addNode(Node $node): bool {
        $added = $this->add($node->getKey(), $node->getValue());
        return $added;
    }

    /**
     * adds a new value assigned to a key. The key has to be a scalar
     * value otherwise the method throws an InvalidKeyTypeException.
     *
     * @param $key
     * @param $value
     * @return bool
     * @throws \doganoo\PHPAlgorithms\Exception\InvalidKeyTypeException
     * @throws \doganoo\PHPAlgorithms\Exception\UnsupportedKeyTypeException
     */
    public function add($key, $value): bool {
        $arrayIndex = $this->getBucketIndex($key);
        $key = MapUtil::normalizeKey($key);
        if (isset($this->bucket[$arrayIndex])) {
            $list = $this->bucket[$arrayIndex];
        } else {
            $list = new SinglyLinkedList();
        }
        /*
         * the method checks the value if it is already
         * in the map or not.
         *
         * Notice that contains() looks for the value, not
         * key as below.
         */
        if ($list->containsValue($value)) {
            return true;
        }
        $list->add($key, $value);
        $this->bucket[$arrayIndex] = $list;
        return true;
    }

    /**
     * returns the bucket array index by using the "division method".
     *
     * note that the division method has limitations: if the hash function
     * calculates the hashes in a constant way, the way how keys are created
     * can be chosen so that they hash to the same bucket. Thus, the worst-case
     * scenario of having n nodes in one chain would be true.
     * Solution: use universal hashing
     *
     * @param $key
     * @return int
     * @throws \doganoo\PHPAlgorithms\Exception\InvalidKeyTypeException
     * @throws \doganoo\PHPAlgorithms\Exception\UnsupportedKeyTypeException
     */
    private function getBucketIndex($key) {
        /*
         * first, it must be ensured that the
         * key is an integer.
         */
        $key = MapUtil::normalizeKey($key);
        /*
         * next, the keys hash is calculated by a
         * private method. Next, the array index
         * (bucket index) is calculated from this hash.
         *
         * Doing this avoids hash collisions.
         */
        $hash = $this->getHash($key);
        $arrayIndex = $this->getArrayIndex($hash);
        return $arrayIndex;
    }

    /**
     * returns the hash that is used to calculate the
     * bucket index.
     *
     * @param $key
     * @return int
     */
    private function getHash($key): int {
        return crc32($key);
    }

    /**
     * calculates the bucket index for a given hash
     *
     * @param int $hash
     * @return int
     */
    private function getArrayIndex(int $hash): int {
        return $hash % $this->maxSize;
    }

    /**
     * determines whether the HashMap contains a value.
     *
     * @param $value
     * @return bool
     */
    public function containsValue($value): bool {

        /**
         * @var string           $arrayIndex
         * @var SinglyLinkedList $list
         */
        foreach ($this->bucket as $arrayIndex => $list) {
            /* $list is the first element in the bucket. The bucket
             * can contain max $maxSize entries and each entry has zero
             * or one nodes which can have zero, one or multiple
             * successors.
             */
            if ($list->containsValue($value)) {
                return true;
            }
        }
        /*
         * If no bucket contains the value then return false because
         * the searched value is not in the list.
         */
        return false;
    }

    /**
     * this method returns the node if it is presentable in the list or null, if not.
     * Please note: this method returns the first node that has the occurrence of the value
     *
     *
     * @param $value
     * @return Node|null
     */
    public function getNodeByValue($value): ?Node {
        /**
         * @var string           $arrayIndex
         * @var SinglyLinkedList $list
         */
        foreach ($this->bucket as $arrayIndex => $list) {
            /*
             * $list is the first element in the bucket. The bucket
             * can contain max $maxSize entries and each entry has zero
             * or one nodes which can have zero, one or multiple
             * successors.
             *
             */
            if (!$list->containsValue($value)) {
                continue;
            }
            $node = $list->getNodeByValue($value);
            return $node;
        }
        //return null if there is no value
        return null;
    }

    /**
     * searches the hash map for a node by a given key.
     *
     * @param $key
     * @return Node|null
     * @throws \doganoo\PHPAlgorithms\Exception\InvalidKeyTypeException
     * @throws \doganoo\PHPAlgorithms\Exception\UnsupportedKeyTypeException
     */
    public function getNodeByKey($key): ?Node {
        $arrayIndex = $this->getBucketIndex($key);
        $key = MapUtil::normalizeKey($key);
        /*
         * the list is requested from the array based on
         * the array index hash.
         */
        /** @var SinglyLinkedList $list */
        if (!isset($this->bucket[$arrayIndex])) {
            return null;
        }
        $list = $this->bucket[$arrayIndex];

        if (!$list->containsKey($key)) {
            return null;
        }
        $node = $list->getNodeByKey($key);
        return $node;
    }

    /**
     * removes a node by a given key
     *
     * @param $key
     * @return bool
     * @throws \doganoo\PHPAlgorithms\Exception\InvalidKeyTypeException
     * @throws \doganoo\PHPAlgorithms\Exception\UnsupportedKeyTypeException
     */
    public function remove($key): bool {
        //get the corresponding index to key
        $arrayIndex = $this->getBucketIndex($key);

        /*
         *if the array index is not available in the
         * bucket list, end the method and return true.
         * True due to the operation was successful, meaning
         * that $key is not in the list.
         * False would indicate that there was an error
         * and the node is still in the list
         */
        if (!isset($this->bucket[$arrayIndex])) {
            return true;
        }
        /** @var SinglyLinkedList $list */
        $list = $this->bucket[$arrayIndex];
        /** @var Node $head */
        $head = $list->getHead();
        /*
         * there is one special case for the HashMap:
         * if there is only one node in the bucket, then
         * check if the nodes key equals to the key that
         * should be deleted.
         * If this is the case, set the bucket to null
         * because the only one node is removed.
         * If this is not the key, return false as there
         * is no node to remove.
         */
        if ($list->size() == 1 && $head->getKey() === $key) {
            $this->bucket[$arrayIndex] = null;
            return true;
        }
        return $list->remove($key);
    }

    /**
     * removes all buckets and their nodes.
     */
    public function clear() {
        $this->initializeBucket();
    }

    /**
     * basic implementation of Java-like keySet().
     * The method returns an array containing the node keys.
     *
     * TODO return (java like generic) set object
     *
     * @return array
     */
    public function keySet(): array {
        $keySet = [];
        /** @var SinglyLinkedList $list */
        foreach ($this->bucket as $list) {
            /** @var Node $head */
            $head = $list->getHead();
            while ($head !== null) {
                $keySet[] = $head->getKey();
                $head = $head->getNext();
            }
        }
        return $keySet;
    }
}