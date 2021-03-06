<?php

/*
 * This file is part of the pitchart/transformer library.
 * (c) Julien VITTE <vitte.julien@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Pitchart\Transformer\Tests\Reducer;

use PHPUnit\Framework\TestCase;
use Pitchart\Transformer\Reducer;
use Pitchart\Transformer\Transducer as t;
use Pitchart\Transformer\Transformer;

/**
 * @internal
 */
final class CatTest extends TestCase
{
    public function test_is_a_reducer()
    {
        self::assertInstanceOf(Reducer::class, t\cat()(t\to_array()));
    }

    public function test_concatenates_items_from_nested_lists()
    {
        $concatenated = (new Transformer([0 ,1, [2, 3], [[4, 5], 6]]))
            ->cat()->toArray();

        self::assertEquals([0, 1, 2, 3, [4, 5], 6], $concatenated);
    }

    public function test_is_immutable()
    {
        $transformer = (new Transformer([0 ,1, [2, 3], [[4, 5], 6]]));
        $copy = clone $transformer;

        self::assertEquals([0, 1, 2, 3, [4, 5], 6], $transformer->cat()->toArray());
        self::assertEquals([0, 1, 2, 3, [4, 5], 6], $transformer->cat()->toArray());
        self::assertEquals($transformer, $copy);
    }

    public function test_removes_empty_nested_lists()
    {
        $concatenated = (new Transformer([0 ,1, [], [2, 3], [[4, 5], 6]]))
            ->cat()->toArray();

        self::assertEquals([0, 1, 2, 3, [4, 5], 6], $concatenated);
    }

    public function test_applies_to_arrays()
    {
        $concatenated = t\cat([0 ,1, [], [2, 3], [[4, 5], 6]]);
        self::assertEquals([0, 1, 2, 3, [4, 5], 6], $concatenated);
    }

    public function test_applies_to_iterators()
    {
        $concatenated = t\cat(new \ArrayIterator([0 ,1, [], [2, 3], [[4, 5], 6]]));
        self::assertEquals([0, 1, 2, 3, [4, 5], 6], $concatenated);
    }
}
