<?php

namespace Message\Cog\Test\Validation\Rule;

use Message\Cog\Validation\Rule\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Text
	 */
	protected $_rule;

	public function setUp()
	{
		$this->_rule = new Text;
	}

	public function testAlnumAllNumbers()
	{
		$this->assertTrue($this->_rule->alnum('0123456789'));
	}

	public function testAlnumAllLettersLowerCase()
	{
		$this->assertTrue($this->_rule->alnum('jkhasdasd'));
	}

	public function testAlnumAllLettersUpperCase()
	{
		$this->assertTrue($this->_rule->alnum('KAHSDJKHASD'));
	}

	public function testAlnumMixed()
	{
		$this->assertTrue($this->_rule->alnum('jJD96bdk'));
	}

	public function testAlnumFalse()
	{
		$this->assertFalse($this->_rule->alnum('ahs89da98sh£dahi(asd'));
	}

	public function testAlphaTrue()
	{
		$this->assertTrue($this->_rule->alpha('jhdfhsdfsdf'));
	}

	public function testAlphaFalse()
	{
		$this->assertFalse($this->_rule->alpha(23234234));
	}

	public function testAlphaFalseInvalid()
	{
		$this->assertFalse($this->_rule->alpha(array('asdasd')));
	}

	public function testDigitTrueWithInt()
	{
		$this->assertTrue($this->_rule->digit(123123));
	}

	public function testDigitTrueWithString()
	{
		$this->assertTrue($this->_rule->digit('123123'));
	}

	public function testDigitFalse()
	{
		$this->assertFalse($this->_rule->digit('asdasd'));
	}

	public function testDigitFalseInvalid()
	{
		$this->assertFalse($this->_rule->digit(array()));
	}

	public function testLengthTrue()
	{
		$this->assertTrue($this->_rule->length('red', 3));
	}

	public function testLengthTrueWithMax()
	{
		$this->assertTrue($this->_rule->length('red', 2, 4));
	}

	public function testLengthTrueAtMin()
	{
		$this->assertTrue($this->_rule->length('red', 3, 4));
	}

	public function testLengthTrueAtMax()
	{
		$this->assertTrue($this->_rule->length('green', 3, 5));
	}

	public function testLengthFalseBelowMin()
	{
		$this->assertFalse($this->_rule->length('red', 4));
	}

	public function testLengthFalseAboveMin()
	{
		$this->assertFalse($this->_rule->length('red', 2));
	}

	public function testLengthFalseWithMaxBelowMin()
	{
		$this->assertFalse($this->_rule->length('red', 4, 6));
	}

	public function testLengthFalseWithMaxAboveMax()
	{
		$this->assertFalse($this->_rule->length('turquoise', 3, 5));
	}

	public function testLengthInvalidMinGreaterThanMax()
	{
		try {
			$this->_rule->length('red', 5, 3);
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

	public function testLengthInvalidMinNonNumeric()
	{
		try {
			$this->_rule->length('red', 'two');
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

	public function testLengthInvalidMaxNonNumeric()
	{
		try {
			$this->_rule->length('red', 2, 'five');
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

	public function testMinLengthTrue()
	{
		$this->assertTrue($this->_rule->minLength('red', 2));
	}

	public function testMinLengthAtMin()
	{
		$this->assertTrue($this->_rule->minLength('red', 3));
	}

	public function testMinLengthFalse()
	{
		$this->assertFalse($this->_rule->minLength('red', 4));
	}

	public function testMinLengthInvalidNonNumeric()
	{
		try {
			$this->_rule->minLength('fish', 'fingers');
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

	public function testMaxLengthTrue()
	{
		$this->assertTrue($this->_rule->maxLength('red', 6));
	}

	public function testMaxLengthTrueAtMax()
	{
		$this->assertTrue($this->_rule->maxLength('red', 3));
	}

	public function testMaxLengthFalse()
	{
		$this->assertFalse($this->_rule->maxLength('yellow', 3));
	}

	public function testMaxLengthInvalidNonNumeric()
	{
		try {
			$this->_rule->maxLength('fish', 'fingers');
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

	public function testEmailTrue()
	{
		$this->assertTrue($this->_rule->email('thomas@message.co.uk'));
	}

	public function testEmailFalse()
	{
		$this->assertFalse($this->_rule->email('thomasatmessagedotcodotuk'));
	}

	public function testEmailNonString()
	{
		try {
			$this->_rule->email(false);
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

	public function testUrlTrue()
	{
		$this->assertTrue($this->_rule->url('http://message.co.uk'));
		$this->assertTrue($this->_rule->url('https://message.co.uk'));
	}

	public function testUrlFalse()
	{
		$this->assertFalse($this->_rule->url('message.co.uk'));
		$this->assertFalse($this->_rule->url('message'));
	}

	public function testUrlNonString()
	{
		try {
			$this->_rule->url(true);
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

	public function testMatchTrue()
	{
		$this->assertTrue($this->_rule->match('Hello there', '/[A-Z][a-z]+\s[a-z]+/'));
		$this->assertTrue($this->_rule->match('123abc', '/[0-9]+[a-z]+/'));
	}

	public function testMatchFalse()
	{
		$this->assertFalse($this->_rule->match('123abc', '/[A-Z][a-z]+\s[a-z]+/'));
		$this->assertFalse($this->_rule->match('Hello there', '/[0-9]+[a-z]+/'));
	}

	public function testMatchNonString()
	{
		try {
			$this->_rule->match('123', 123);
		}
		catch (\Exception $e) {
			return;
		}
		$this->fail('Exception not thrown');
	}

}