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
use Pitchart\Transformer\Reducer\DropWhile;
use Pitchart\Transformer\Transducer as t;
use Pitchart\Transformer\Transformer;
use function Pitchart\Transformer\Tests\Fixtures\is_lower_than_four;

/**
 * @internal
 */
final class DropWhileTest extends TestCase
{
    public function test_is_a_reducer()
    {
        self::assertInstanceOf(Reducer::class, t\drop_while(is_lower_than_four())(t\to_array()));
    }

    public function test_drops_items_while_a_predicat_is_true()
    {
        $dropped = (new Transformer(range(1, 6)))
            ->dropWhile(is_lower_than_four())->toArray();
        self::assertEquals([4, 5, 6], $dropped);
    }

    public function test_is_immutable()
    {
        $transformer = (new Transformer([1, 2, 3, 4, 5, 6]));
        $copy = clone $transformer;

        self::assertEquals([4, 5, 6], $transformer->dropWhile(is_lower_than_four())->toArray());
        self::assertEquals([4, 5, 6], $transformer->dropWhile(is_lower_than_four())->toArray());
        self::assertEquals($transformer, $copy);
    }

    public function test_applies_to_arrays()
    {
        $dropped = t\drop_while(is_lower_than_four(), range(1, 6));
        self::assertEquals([4, 5, 6], $dropped);
    }

    public function test_applies_to_iterators()
    {
        $dropped = t\drop_while(is_lower_than_four(), new \ArrayIterator(range(1, 6)));
        self::assertEquals([4, 5, 6], $dropped);
    }
}
