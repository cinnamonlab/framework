<?php

namespace Framework;

use SplFileObject;

class InputFile extends SplFileObject {

    private $content_type;
    private $original_name;

    /**
     * @param mixed $content_type
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }

    /**
     * @return mixed
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @param mixed $original_name
     */
    public function setOriginalName($original_name)
    {
        $this->original_name = $original_name;
    }

    /**
     * @return mixed
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }

}