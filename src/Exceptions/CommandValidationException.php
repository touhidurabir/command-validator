<?php

namespace Touhidurabir\CommandValidator\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Support\MessageProvider;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class CommandValidationException extends InvalidArgumentException implements MessageProvider
{
    /**
     * The failed validator instance.
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;


    /**
     * Create a new exception instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    public function __construct(Validator $validator)
    {

        $this->validator = $validator;

        parent::__construct(implode(PHP_EOL, $validator->errors()->all()));
    }


    /**
     * Get the failed validator instance.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function getValidator()
    {

        return $this->validator;
    }


    /**
     * Get the validation error messages.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessageBag()
    {

        return $this->validator->errors();
    }
}
