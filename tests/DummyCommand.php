<?php

namespace Touhidurabir\CommandValidator\Tests;

use Illuminate\Console\Command;
use Touhidurabir\CommandValidator\HasCommandValidator;

class DummyCommand extends Command {
    
    use HasCommandValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:run
                            {arg    : The command argument}
                            {--opt1= : The command option 1}
                            {--opt2= : The command option 2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dummy test command';


    /**
     * Will print the errors on console
     *
     * @var bool
     */
    protected $printValidationErrorsOnConsole = true;


    /**
     * Should the validation error print on the console
     *
     * @return bool
     */
    protected function allowValidationFailureOnConsole() {

        return $this->printValidationErrorsOnConsole;
    }


    /**
     * The command arguments and options validation rules
     *
     * @return array
     */
    protected function rules(): array {

        return [
            'arg'   => ['integer', 'required', 'min:100'],
            'opt1'  => ['integer', 'sometimes', 'nullable', 'max:10'],
            'opt2'  => ['sometimes', 'nullable', 'string'],
        ];
    }


    /**
     * Any custom error message
     *
     * @return array
     */
    protected function messages(): array {

        return [
            'min' => 'The minimum allowed :attribute is :min',
            'max' => 'The maximum allowed :attribute is :max',
        ];
    }


    /**
     * Any custom arrtibute names to associated with error messages
     *
     * @return array
     */
    protected function attributes(): array {

        return [
            'arg' => 'command argument',
        ];
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {

        $this->info('This is a success.');
    }


    /**
     * Configure the validation error handling of this command
     *
     * @param  bool $option
     * @return self
     */
    public function configureValidationErrorHandling(bool $option = true) {

        $this->printValidationErrorsOnConsole = $option;

        return $this;
    }
}