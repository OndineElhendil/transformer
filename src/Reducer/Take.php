<?php

namespace Pitchart\Transformer\Reducer;

use Pitchart\Transformer\Reduced;
use Pitchart\Transformer\Reducer;
use Pitchart\Transformer\Reducer\Traits\IsStateless;

class Take implements Reducer
{
    /**
     * @var Reducer
     */
    protected $next;

    /**
     * @var integer
     */
    protected $number;

    /**
     * @var integer
     */
    protected $remaining = 0;

    public function __construct(Reducer $next, int $number)
    {
        $this->next = $next;
        $this->number = $number;
    }

    public function init()
    {
        $this->remaining = $this->number;
        return $this->next->init();
    }

    public function step($result, $current)
    {
        $return = $this->next->step($result, $current);
        $this->remaining--;
        if ($this->remaining > 0) {
            return $return;
        }
        return $return instanceof Reduced ? $return : new Reduced($return);
    }

    public function complete($result)
    {
        return $this->next->complete($result);
    }


}