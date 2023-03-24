<?php

namespace Kuperwood\Eav\Result;

use Kuperwood\Eav\Enum\VALUE_RESULT;

class ValueResult extends Result
{
    public function created() : self
    {
        return $this->setCode(VALUE_RESULT::CREATED->code())
            ->setMessage(VALUE_RESULT::CREATED->message());
    }

    public function updated() : self
    {
        return $this->setCode(VALUE_RESULT::UPDATED->code())
            ->setMessage(VALUE_RESULT::UPDATED->message());
    }

    public function deleted() : self
    {
        return $this->setCode(VALUE_RESULT::DELETED->code())
            ->setMessage(VALUE_RESULT::DELETED->message());
    }

    public function notDeleted() : self
    {
        return $this->setCode(VALUE_RESULT::NOT_DELETED->code())
            ->setMessage(VALUE_RESULT::NOT_DELETED->message());
    }

    public function found() : self
    {
        return $this->setCode(VALUE_RESULT::FOUND->code())
            ->setMessage(VALUE_RESULT::FOUND->message());
    }

    public function notFound() : self
    {
        return $this->setCode(VALUE_RESULT::NOT_FOUND->code())
            ->setMessage(VALUE_RESULT::NOT_FOUND->message());
    }

    public function notEnoughArgs() : self
    {
        return $this->setCode(VALUE_RESULT::NOT_ENOUGH_ARGS->code())
            ->setMessage(VALUE_RESULT::NOT_ENOUGH_ARGS->message());
    }

    public function notAllowed() : self
    {
        return $this->setCode(VALUE_RESULT::NOT_ALLOWED->code())
            ->setMessage(VALUE_RESULT::NOT_ALLOWED->message());
    }

    public function empty() : self
    {
        return $this->setCode(VALUE_RESULT::EMPTY->code())
            ->setMessage(VALUE_RESULT::EMPTY->message());
    }


}