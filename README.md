# Command Validator

A simple laravel package to validate console commands arguments and options.


## Installation

Require/Install the package using composer:

```bash
composer require touhidurabir/command-validator
```

## Usage

Drop the trait **HasCommandValidator** in any command and then put the validation rules . thats all . 

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Touhidurabir\CommandValidator\HasCommandValidator;

class Test extends Command {
    
    use HasCommandValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:test
                            {arg     : The command argument}
                            {--opt1= : The command option 1}
                            {--opt2= : The command option 2}';


    /**
     * The command arguments and options validation rules
     *
     * @return array
     */
    protected function rules(): array {

        return [
            'arg'   => ['integer', 'required', 'min:100'],
            'opt1'  => ['integer', 'required', 'max:10'],
            'opt2'  => ['sometimes', 'nullable', 'string'],
        ];
    }
}    
```

This package also support the message or attribute override ability provided by laravel validation itself . 

To override the validation message put method in the command class and fill up as needed

```php
/**
 * Any custom error message
 *
 * @return array
 */
protected function messages(): array {

    return [];
}
```
To override the validation attributes put method in the command class and fill up as needed

```php
/**
 * Any custom arrtibute names to associated with error messages
 *
 * @return array
 */
protected function attributes(): array {

    return [];
}
```

By default this validator do not throw an exception when the validation of arguments or options failed but print those as error in the console in a formatted way to check what is missing/failed . But if want to throw exception rather than console printing , override the method **allowValidationFailureOnConsole** to return **false** as 

```php
/**
 * Should the validation error print on the console
 *
 * @return bool
 */
protected function allowValidationFailureOnConsole() {

    return false;
}
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)
