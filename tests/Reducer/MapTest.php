<?php

/*
 * This file is part of the pitchart/transformer library.
 * (c) Julien VITTE <vitte.julien@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Pitchart\Transformer\Tests\Reducer;

use PHPUnit\Framework\TestCase;
use Pitchart\Transformer\Transducer as t;
use Pitchart\Transformer\Transformer;
use function Pitchart\Transformer\Tests\Fixtures\square;

/**
 * @internal
 */
final class MapTest extends TestCase
{
    public function test_applies_a_callable_on_each_item()
    {
        $squared = (new Transformer(range(1, 2)))
            ->map(square())
            ->toArray();

        self::assertEquals([1, 4], $squared);
    }

    public function test_is_immutable()
    {
        $transformer = (new Transformer([1, 2, 3, 4, 5]));
        $copy = clone $transformer;

        self::assertEquals([1, 4, 9, 16, 25], $transformer->map(square())->toArray());
        self::assertEquals([1, 4, 9, 16, 25], $transformer->map(square())->toArray());
        self::assertEquals($transformer, $copy);
    }

    public function test_applies_on_arrays()
    {
        $squared = t\map(square(), range(1, 2));
        self::assertEquals([1, 4], $squared);
    }

    public function test_applies_on_iterator()
    {
        $squared = t\map(square(), new \ArrayIterator([1, 2]));
        self::assertEquals([1, 4], $squared);
    }
}
