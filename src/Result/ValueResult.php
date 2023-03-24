<?php

namespace Kuperwood\Eav\Result;

use Kuperwood\Eav\Enum\VALUE_RESULT;

class ValueResult extends Result
{
    public function created() : self
    {
        $this->code = VALUE_RESULT::CREATED->code();
        $this->message = VALUE_RESULT::CREATED->message();
        return $this;
    }

    public function updated() : self
    {
        $this->code = VALUE_RESULT::UPDATED->code();
        $this->message = VALUE_RESULT::UPDATED->message();
        return $this;
    }

    public function notAllowed() : self
    {
        $this->code = VALUE_RESULT::NOT_ALLOWED->code();
        $this->message = VALUE_RESULT::NOT_ALLOWED->message();
        return $this;
    }

    public function empty() : self
    {
        $this->code = VALUE_RESULT::EMPTY->code();
        $this->message = VALUE_RESULT::EMPTY->message();
        return $this;
    }
}