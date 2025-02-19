<?php
declare(strict_types=1);
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

namespace doganoo\PHPAlgorithmsTest\Maps;

use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;
use doganoo\PHPAlgorithmsTest\Util\HashMapUtil;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class HashMapTest PHPUnit test class
 */
class HashMapTest extends TestCase {

    /**
     * tests adding new elements to the map
     */
    public function testAddition() {
        $class   = stdClass::class;
        $hashMap = new HashMap();
        $boolean = $hashMap->add(1, $class);
        $has     = $hashMap->getNodeByValue($class);
        $this->assertTrue($boolean);
        $this->assertTrue(null !== $has);
    }

    public function testSize() {
        $class   = stdClass::class;
        $hashMap = new HashMap();
        $hashMap->add(1, $class);
        $hashMap->add(2, $class);
        $hashMap->add(3, $class);
        $hashMap->add(4, $class);
        $hashMap->add(5, $class);

        $this->assertTrue($hashMap->size() === 5);
    }

    /**
     * tests querying the map for a value
     */
    public function testContains() {
        $class   = stdClass::class;
        $hashMap = new HashMap();
        $hashMap->add(1, $class);
        $boolean = $hashMap->containsValue($class);
        $this->assertTrue($boolean);
    }

    /**
     * tests retrieving a node from the map
     */
    public function testGetNodeByValue() {
        $class   = stdClass::class;
        $hashMap = new HashMap();
        $hashMap->add(1, $class);
        $node = $hashMap->getNodeByValue($class);
        $this->assertTrue($node !== null);
    }

    /**
     * tests removing a value from the map
     */
    public function testRemove() {
        $hashMap = HashMapUtil::getHashMap(500);
        $boolean = $hashMap->remove(320);
        $this->assertTrue($boolean);
    }

    /**
     * tests adding different key types to the map
     */
    public function testKeyTypes() {
        $hashMap = new HashMap();
        $added   = $hashMap->add(new stdClass(), "stdClass");
        $this->assertTrue($added);
    }

    /**
     * tests retrieving all keys from the map
     */
    public function testKeySet() {
        $hashMap = HashMapUtil::getHashMap(10);
        $keySet  = $hashMap->keySet();
        $this->assertTrue(count($keySet) == 10);
    }

    public function testClosure() {
        $hashMap = new HashMap();
        $added   = $hashMap->add("test", function () {
            return new stdClass();
        });
        $this->assertTrue($added);
        $added = $hashMap->add("test2", new class {

            public function x() {
            }

        });
        $this->assertTrue($added);
    }

}