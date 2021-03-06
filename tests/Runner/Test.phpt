<?php

/**
 * TEST: Runner\Test basics.
 */

use Tester\Assert;
use Tester\Runner\Test;

require __DIR__ . '/../../src/Runner/Test.php';
require __DIR__ . '/../bootstrap.php';


test(function() {
	$test = new Test('some/Test.phpt');

	Assert::null($test->title);
	Assert::null($test->message);
	Assert::same('', $test->stdout);
	Assert::same('', $test->stderr);
	Assert::same('some/Test.phpt', $test->getFile());
	Assert::same([], $test->getArguments());
	Assert::same('Test.phpt', $test->getName());
	Assert::false($test->hasResult());
	Assert::same(Test::PREPARED, $test->getResult());
});


test(function() {
	$test = new Test(__FILE__, 'My test');

	Assert::same('My test', $test->title);
	Assert::same('My test', $test->getName());
});


test(function() {
	$test = (new Test(__FILE__, 'My test'))->withResult(Test::PASSED, 'It is done');

	Assert::true($test->hasResult());
	Assert::same(Test::PASSED, $test->getResult());
	Assert::same('It is done', $test->message);

	Assert::exception(function () use ($test) {
		$test->withResult(Test::FAILED, 'Foo');
	}, 'LogicException', "Result of test is already set to 1 with message 'It is done'.");
});


test(function() {
	$test = new Test(__FILE__, 'My test');

	$test = $test->withArguments(['one', 'two' => 1]);
	Assert::same('My test', $test->title);
	Assert::same('My test [one two=1]', $test->getName());

	$test = $test->withArguments(['one', 'two' => [1, 2], 'three']);
	Assert::same('My test [one two=1 one two=1 two=2 three]', $test->getName());
	Assert::same('My test [one ...]', $test->getName(10));

	Assert::exception(function () use ($test) {
		$test->withResult(Test::PASSED, '')->withArguments([]);
	}, 'LogicException', 'Cannot change arguments of test which already has a result.');
});
