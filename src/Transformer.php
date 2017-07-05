<?php

namespace Pitchart\Transformer;

use Pitchart\Transformer\Exception\InvalidArgument;
use Pitchart\Transformer\Transducer as t;

class Transformer
{
    /**
     * @var Composition
     */
    private $composition;

    /**
     * @var Termination
     */
    private $termination;

    /**
     * @var iterable
     */
    private $iterable;

    /**
     * @var mixed
     */
    private $initial;

    /**
     * Transformer constructor.
     *
     * @param iterable    $iterable
     * @param Composition $composition
     * @param Termination $termination
     * @param mixed       $initial
     */
    public function __construct($iterable, Composition $composition = null, Termination $termination = null, $initial = null)
    {
        if ($composition === null) {
            $composition = new Composition();
        }

        $this->composition = $composition;
        $this->termination = $termination;
        $this->iterable = $iterable;
        $this->initial = $initial;
    }

    /**
     * @param callable $callback
     *
     * return static
     */
    public function map(callable $callback)
    {
        return $this->appendComposition(t\map($callback));
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function filter(callable $callback)
    {
        return $this->appendComposition(t\filter($callback));
    }

    public function select(callable $calable)
    {
        return $this->filter($calable);
    }

    public function keep(callable $callback)
    {
        return $this->appendComposition(t\keep($callback));
    }

    public function remove(callable $callback)
    {
        return $this->appendComposition(t\remove($callback));
    }

    public function reject(callable $callback)
    {
        return $this->remove($callback);
    }

    public function first(callable $callback)
    {
        return $this->appendComposition(t\first($callback));
    }

    public function cat()
    {
        return $this->appendComposition(t\cat());
    }

    public function mapcat(callable $callback)
    {
        return $this->appendComposition(t\mapcat($callback));
    }

    public function flatten()
    {
        return $this->appendComposition(t\flatten());
    }

    public function take(int $number)
    {
        return $this->appendComposition(t\take($number));
    }

    public function takeWhile(callable $callback)
    {
        return $this->appendComposition(t\take_while($callback));
    }

    public function takeNth(int $frequency)
    {
        return $this->appendComposition(t\take_nth($frequency));
    }

    public function drop(int $number)
    {
        return $this->appendComposition(t\drop($number));
    }

    public function dropWhile(callable $callback)
    {
        return $this->appendComposition(t\drop_while($callback));
    }

    public function replace(array $map)
    {
        return $this->appendComposition(t\replace($map));
    }

    public function distinct()
    {
        return $this->appendComposition(t\distinct());
    }

    public function dedupe()
    {
        return $this->appendComposition(t\dedupe());
    }

    public function partition($size)
    {
        return $this->appendComposition(t\partition($size));
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        return $this->terminate(t\to_array());
    }

    /**
     * @return mixed
     */
    public function single()
    {
        return $this->terminate(t\to_single());
    }

    /**
     * @param callable    $transducer
     * @param Termination $reducer
     * @param array       $iterable
     * @param mixed       $initial
     *
     * @return mixed
     */
    private function transduce(callable $transducer, Termination $reducer, $iterable, $initial = null)
    {
        InvalidArgument::assertIterable($iterable, static::class, __FUNCTION__, 3);
        /** @var Reducer $transformation */
        $transformation = $transducer($reducer);

        $accumulator = ($initial === null) ? $transformation->init() : $initial;

        foreach ($this->generator($iterable) as $current) {
            $accumulator = $transformation->step($accumulator, $current);

            //early termination
            if ($accumulator instanceof Reduced) {
                $accumulator = $accumulator->value();
                break;
            }
        }

        return $transformation->complete($accumulator);
    }

    /**
     * @param $iterable
     *
     * @return \Generator
     */
    private function generator($iterable)
    {
        yield from $iterable;
    }

    /**
     * Returns a new Transformer with adding a callback to its composition
     *
     * @param callable $callback
     *
     * @return static
     */
    private function appendComposition(callable $callback)
    {
        return new static($this->iterable, $this->composition->append($callback), $this->termination, $this->initial);
    }

    /**
     * Processes transducing with a Termination
     *
     * @param Termination $termination
     *
     * @return mixed
     */
    private function terminate(Termination $termination)
    {
        return $this->transduce($this->composition, $termination, $this->iterable, $this->initial);
    }
}
