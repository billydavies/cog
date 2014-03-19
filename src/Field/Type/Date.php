<?php

namespace Message\Cog\Field\Type;

use Message\Cog\Field\Field;
use Message\Cog\Form\Handler;

/**
 * A field for a single date.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class Date extends Field
{
	public function getFieldType()
	{
		return 'date';
	}

	public function getValue()
	{
		if ($this->_value instanceof \DateTime) {
			return $this->_value;
		}

		return new \DateTime(date('c', $this->_value));
	}

	public function getFormField(Handler $form)
	{
		$form->add($this->getName(), 'date', $this->getLabel(), $this->getFieldOptions());
	}
}