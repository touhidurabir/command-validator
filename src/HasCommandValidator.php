<?php

namespace Touhidurabir\CommandValidator;

use Throwable;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;

trait HasCommandValidator {

    /**
     * The command validator instance.
     *
     * @var object<\Illuminate\Contracts\Validation\Validator>
     */
    protected $validator;


    /**
     * The command arguments and options validation rules
     *
     * @return array
     */
    protected function rules(): array {

        return [];
    }


    /**
     * Should the validation error print on the console
     *
     * @return bool
     */
    protected function allowValidationFailureOnConsole() {

        return true;
    }


    /**
     * Execute the console command.
     *
     * @param  object<\Symfony\Component\Console\Input\InputInterface>      $input
     * @param  object<\Symfony\Component\Console\Output\OutputInterface>    $output
     * 
     * @return mixed
     * 
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        if ( !empty($this->rules()) && !$this->validate() ) {

            if ( ! $this->allowValidationFailureOnConsole() ) {

                throw new InvalidArgumentException(
                    implode(PHP_EOL, $this->validator->errors()->all())
                );
            }

            $this->printValidationErrorsOnConsole($this->validator->errors());

            return 1;
        }

        return parent::execute($input, $output);
    }


    /**
     * Retrieve the command input validator
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validate() {

        $this->validator = Validator::make(
            $this->validatebales(),
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );

        return $this->validator->passes();
    }


    /**
     * Get the data to run the validation on them
     * Generate it form the merge of command arguments and options
     *
     * @return array
     */
    protected function validatebales() : array {

        return array_filter(array_merge($this->arguments(), $this->options()), function ($value) {
            
            return $value !== null;
        });
    }


    /**
     * Print the validation error in console
     *
     * @param  object<\Illuminate\Support\MessageBag> $errors
     * @return void
     */
    protected function printValidationErrorsOnConsole(MessageBag $errors) {
        
        foreach( $this->validatebales() as $validatable => $value ) {

            if ( ! $errors->has($validatable) ) {

                continue;
            }
            
            $this->error("validation failure for {$validatable}");

            foreach ( $errors->get($validatable) as $error ) {
                ray($error);
                $this->error("    --> {$error}");
            }
        }
    }


    /**
     * Any custom error message
     *
     * @return array
     */
    protected function messages(): array {

        return [];
    }


    /**
     * Any custom arrtibute names to associated with error messages
     *
     * @return array
     */
    protected function attributes(): array {

        return [];
    }

}