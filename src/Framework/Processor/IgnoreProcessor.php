<?php

namespace Framework\Processor;


class IgnoreProcessor extends Processor
{
    public function then( $func ) {
        return $this;
    }


}