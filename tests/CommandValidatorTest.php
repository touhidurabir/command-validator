<?php

namespace Touhidurabir\CommandValidator\Tests;

use PHPUnit\Framework\Attributes\Test;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\MessageBag;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Touhidurabir\CommandValidator\CommandValidatorServiceProvider;
use Touhidurabir\CommandValidator\Exceptions\CommandValidationException;

class CommandValidatorTest extends TestCase
{
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
    protected function getPackageProviders($app)
    {

        return [
            CommandValidatorServiceProvider::class,
        ];
    }


    protected function configureTestCommand(bool $printErrorOnConsole = true)
    {

        $command = (new DummyCommand())->configureValidationErrorHandling($printErrorOnConsole);
        $command->setLaravel($this->app);

        $this->testableCommand = new CommandTester($command);
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {

        parent::setUp();

        $this->configureTestCommand();
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_fail_if_passed_invalid_data()
    {

        $status = $this->testableCommand->execute([
            'arg'       => 10,
            '--opt1'    => 1000,
        ]);

        $this->assertEquals($status, 1);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_print_errors_on_display()
    {

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
    #[Test]
    public function it_will_throw_exception_if_instructed_to_when_passed_invalid_data()
    {

        $this->configureTestCommand(false);

        $this->expectException(InvalidArgumentException::class);

        $this->testableCommand->execute([
            'arg'       => 10,
            '--opt1'    => 1000,
        ]);
    }


    /**
     * Build a command tester for the given dummy command instance
     *
     * @param  \Touhidurabir\CommandValidator\Tests\DummyCommand $command
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function testerFor(DummyCommand $command)
    {

        $command->setLaravel($this->app);

        return new CommandTester($command);
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_validate_the_data_from_overridden_validatables()
    {

        $tester = $this->testerFor(new class () extends DummyCommand {
            protected function validatables(): array
            {
                return ['arg' => 500];
            }
        });

        $this->assertEquals(0, $tester->execute(['arg' => 10]));
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_still_validate_the_data_from_overridden_misspelled_validatebales()
    {

        $tester = $this->testerFor(new class () extends DummyCommand {
            protected function validatebales(): array
            {
                return ['arg' => 500];
            }
        });

        $this->assertEquals(0, $tester->execute(['arg' => 10]));
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_prefer_validatables_over_misspelled_validatebales_when_both_overridden()
    {

        $tester = $this->testerFor(new class () extends DummyCommand {
            protected function validatables(): array
            {
                return ['arg' => 500];
            }

            protected function validatebales(): array
            {
                return ['arg' => 10];
            }
        });

        $this->assertEquals(0, $tester->execute(['arg' => 10]));
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_throw_package_exception_carrying_the_validation_errors()
    {

        $this->configureTestCommand(false);

        try {

            $this->testableCommand->execute([
                'arg'       => 10,
                '--opt1'    => 1000,
            ]);

            $this->fail('The command validation exception was not thrown.');

        } catch (CommandValidationException $exception) {

            // existing catch blocks for the Symfony exception keep working
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);

            $errors = $exception->getMessageBag();

            $this->assertInstanceOf(MessageBag::class, $errors);
            $this->assertTrue($errors->has('arg'));
            $this->assertTrue($errors->has('opt1'));
            $this->assertFalse($errors->has('opt2'));
            $this->assertSame($errors, $exception->getValidator()->errors());

            $this->assertSame(implode(PHP_EOL, $errors->all()), $exception->getMessage());
            $this->assertStringContainsString('The minimum allowed command argument is 100', $exception->getMessage());
        }
    }


    /**
     * @test
     */
    #[Test]
    public function it_will_pass_if_proper_data_provided()
    {

        $status = $this->testableCommand->execute([
            'arg'       => 100,
            '--opt1'    => 10,
            '--opt2'    => 'test',
        ]);

        $this->assertEquals($status, 0);
        $this->assertStringContainsString('This is a success.', $this->testableCommand->getDisplay());
    }
}
