<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2015 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Stubs;

use Akeeba\Subscriptions\Site\Model\Subscribe\StateData;
use Akeeba\Subscriptions\Site\Model\Subscribe\ValidatorFactory;
use FOF30\Container\Container;
use JUser;

abstract class ValidatorTest extends \PHPUnit_Framework_TestCase
{
	/** @var   ValidatorFactory  The validator factory for this class */
	public static $factory = null;

	/** @var   Container  The container of the component */
	public static $container = null;

	/** @var   StateData  The state data we're operating on */
	public static $state = null;

	/** @var   JUser  The currently active Joomla! user object */
	public static $jUser = null;

	/** @var   string  Which validator are we testing? */
	public static $validatorType = '';

	/**
	 * Set up the static objects before the class is created
	 */
	public static function setUpBeforeClass()
	{
		if (is_null(static::$container))
		{
			static::$container = Container::getInstance('com_akeebasubs');
		}

		if (is_null(static::$jUser))
		{
			static::$jUser = new JUser();
		}

		$model = static::$container->factory->model('Subscribe');
		static::$state = new StateData($model);

		static::$factory = new ValidatorFactory(static::$container, static::$state, static::$jUser);
	}

	/**
	 * The data to set up and run tests.
	 *
	 * The return is an array of arrays. Each second level array has three keys:
	 * – state, array. The state variables to set up.
	 * - expected, mixed. The expected return value of the validator.
	 * - message. Message to show if the test fails.
	 *
	 * @return  array  See above
	 */
	public function getTestData()
	{
		return [
			[
				'state' => [
					'name' => 'Foobar'
				],
				'expected' => false,
				'message' => 'Single word names are not allowed'
			],
			[
				'state' => [
					'name' => 'Foo bar'
				],
				'expected' => true,
				'message' => 'Two word names are allowed'
			],
			[
				'state' => [
					'name' => 'Foo bar baz'
				],
				'expected' => true,
				'message' => 'Three word names are allowed'
			],
			[
				'state' => [
					'name' => 'a b'
				],
				'expected' => true,
				'message' => 'Single letter names with two parts are allowed'
			],
		];
	}

	public function testGetValidationResult($state, $expected, $message)
	{
		static::$state->reset();

		foreach ($state as $k => $v)
		{
			static::$state->$k = $v;
		}

		$validator = static::$factory->getValidator(self::$validatorType);
		$actual = $validator->execute(true);

		$this->assertEquals($expected, $actual, $message);
	}
}