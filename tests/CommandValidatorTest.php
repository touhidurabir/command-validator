<?php

namespace Touhidurabir\CommandValidator\Tests;

use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Touhidurabir\CommandValidator\Tests\DummyCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Touhidurabir\CommandValidator\CommandValidatorServiceProvider;

class CommandValidatorTest extends TestCase {

    /**
     * The testable dummy command
     *
     * @var object<\Symfony\Component\Console\Tester\CommandTester>
     */
    protected $testableCommand;

    
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app) {

        return [
            CommandValidatorServiceProvider::class,
        ];
    }


    protected function configureTestCommand(bool $printErrorOnConsole = true) {

        $command = (new DummyCommand)->configureValidationErrorHandling($printErrorOnConsole);
        $command->setLaravel($this->app);

        $this->testableCommand = new CommandTester($command);
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void {

        parent::setUp();

        $this->configureTestCommand();
    }


    /**
     * @test
     */
    public function it_will_fail_if_passed_invalid_data() {

        $status = $this->testableCommand->execute([
            'arg'       => 10,
            '--opt1'    => 1000,
        ]);

        $this->assertEquals($status, 1);
    }


    /**
     * @test
     */
    public function it_will_print_errors_on_display() {

        $status = $this->testableCommand->execute([
            'arg'       => 10,
            '--opt1'    => 1000,
        ]);

        $this->assertEquals($status, 1);

        $displayOutput = $this->testableCommand->getDisplay();

        $this->assertStringContainsString('validation failure for arg', $displayOutput);
        $this->assertStringContainsString('validation failure for opt1', $displayOutput);
        $this->assertStringContainsString('The minimum allowed command argument is 100', $displayOutput);
        $this->assertStringContainsString('The maximum allowed opt1 is 10', $displayOutput);
    }


    /**
     * @test
     */
    public function it_will_throw_exception_if_instructed_to_when_passed_invalid_data() {

        $this->configureTestCommand(false);

        $this->expectException(InvalidArgumentException::class);

        $this->testableCommand->execute([
            'arg'       => 10,
            '--opt1'    => 1000,
        ]);
    }


    /**
     * @test
     */
    public function it_will_pass_if_proper_data_provided() {
        
        $status = $this->testableCommand->execute([
            'arg'       => 100,
            '--opt1'    => 10,
            '--opt2'    => 'test',
        ]);

        $this->assertEquals($status, 0);
        $this->assertStringContainsString('This is a success.', $this->testableCommand->getDisplay());
    }
}