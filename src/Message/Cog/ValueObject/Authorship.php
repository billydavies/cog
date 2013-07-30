<?php

namespace Message\Cog\ValueObject;

/**
 * Represents the created, updated and deleted metadata for a model.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 * @author Danny Hannah <danny@message.co.uk>
 */
class Authorship
{
	const DATE_FORMAT = 'j F Y \a\t g:ia';

	protected $_createdAt;
	protected $_createdBy;

	protected $_updatedAt;
	protected $_updatedBy;
	protected $_updatable;

	protected $_deletedAt;
	protected $_deletedBy;
	protected $_deletable;

	public function __construct()
	{
		$this->setUpdatable(true);
		$this->setDeletable(true);
	}

	/**
	 * Get the date & time of creation.
	 *
	 * @return DateTime|null The date & time of creation, null if not set
	 */
	public function createdAt()
	{
		return $this->_createdAt;
	}

	/**
	 * Get the user responsible for creation.
	 *
	 * @return mixed The user that created the model, null if not set
	 */
	public function createdBy()
	{
		return $this->_createdBy;
	}

	/**
	 * Get the date & time of last update.
	 *
	 * @return DateTime|null The date & time of last update, null if not set
	 */
	public function updatedAt()
	{
		return $this->_updatedAt;
	}

	/**
	 * Get the user responsible for the last edit.
	 *
	 * @return mixed The user that last edited the model, null if not set
	 */
	public function updatedBy()
	{
		return $this->_updatedBy;
	}

	/**
	 * Sets updatable to $bool
	 *
	 * @param  boolean $bool    Boolean updatable is set to
	 * @return Authorship       Returns $this for chainability
	 */
	private function setUpdatable($bool)
	{
		$this->_updatable = $bool;

		return $this;
	}

	/**
	 * Enables updating
	 */
	public function enableUpdate()
	{
		return $this->setUpdatable(true);
	}

	/**
	 * Disables updating
	 */
	public function disableUpdate()
	{
		return $this->setUpdatable(false);
	}


	/**
	 * Get the date & time of deletion.
	 *
	 * @return DateTime|null The date & time of deletion, null if not set
	 */
	public function deletedAt()
	{
		return $this->_deletedAt;
	}

	/**
	 * Get the user responsible for deletion.
	 *
	 * @return mixed The user that deleted the model, null if not set
	 */
	public function deletedBy()
	{
		return $this->_deletedBy;
	}

	/**
	 * Check if the model has been deleted.
	 *
	 * @return boolean True if a deleted timestamp and/or user is set
	 */
	public function isDeleted()
	{
		return !is_null($this->_deletedAt);
	}

	/**
	 * Sets deletable to $bool
	 *
	 * @param  boolean $bool    Boolean deletable is set to
	 * @return Authorship       Returns $this for chainability
	 */
	private function setDeletable($bool)
	{
		$this->_deletable = $bool;

		return $this;
	}

	/**
	 * Enables deleting and restoring
	 */
	public function enableDelete()
	{
		return $this->setDeletable(true);
	}

	/**
	 * Disables deleting and restoring
	 */
	public function disableDelete()
	{
		return $this->setDeletable(false);
	}

	/**
	 * Set the created metadata.
	 *
	 * @param  DateTimeImmutable|null $datetime The date & time of creation,
	 *                                          null to use current date & time
	 * @param  mixed $user                      The user responsible
	 *
	 * @return Authorship                       Returns $this for chainability
	 *
	 * @throws \LogicException If the created metadata already exists
	 */
	public function create(DateTimeImmutable $datetime = null, $user = null)
	{
		if (!is_null($this->_createdAt)) {
			throw new \LogicException('Cannot set created metadata: it has already been set');
		}

		$this->_createdAt = $datetime ?: new DateTimeImmutable('now');
		$this->_createdBy = $user;

		return $this;
	}

	/**
	 * Set the updated metadata.
	 *
	 * @param  DateTimeImmutable|null $datetime The date & time of the update,
	 *                                          null to use current date & time
	 * @param  mixed $user                      The user responsible
	 *
	 * @return Authorship                       Returns $this for chainability
	 *
	 * @throws \LogicException If updatable is false
	 */
	public function update(DateTimeImmutable $datetime = null, $user = null)
	{
		if(!$this->_updatable) {
			throw new \LogicException('Cannot set created metadata: updating is disabled');
		}
		$this->_updatedAt = $datetime ?: new DateTimeImmutable('now');
		$this->_updatedBy = $user;

		return $this;
	}

	/**
	 * Set the deleted metadata.
	 *
	 * @param  DateTimeImmutable|null $datetime The date & time of deletion,
	 *                                          null to use current date & time
	 * @param  mixed $user                      The user responsible
	 *
	 * @return Authorship                       Returns $this for chainability
	 *
	 * @throws \LogicException If the deleted metadata already exists
	 *
	 * @throws \LogicException If deletable is false
	 */
	public function delete(DateTimeImmutable $datetime = null, $user = null)
	{
		if (!is_null($this->_deletedAt)) {
			throw new \LogicException('Cannot set deleted metadata: it has already been set');
		}

		if(!$this->_deletable) {
			throw new \LogicException('Cannot set created metadata: deleting is disabled');
		}

		$this->_deletedAt = $datetime ?: new DateTimeImmutable('now');
		$this->_deletedBy = $user;

		return $this;
	}

	/**
	 * Restore the model by removing the deleted metadata.
	 *
	 * @return Authorship      Returns $this for chainability
	 *
	 * @return Authorship      Returns $this for chainability
	 *
	 * @throws \LogicException If the model hasn't been deleted yet
	 *
	 * @throws \LogicException If deletable is false
	 */
	public function restore()
	{
		if (is_null($this->_deletedAt)) {
			throw new \LogicException('Cannot restore an entity that has not been deleted');
		}

		if(!$this->_deletable) {
			throw new \LogicException('Cannot restore entity: deleting and restoring is disabled');
		}


		$this->_deletedAt = null;
		$this->_deletedBy = null;

		return $this;
	}

	/**
	 * Print out the authorship metadata as a string.
	 *
	 * @return string The authorship metadata represented as a string
	 */
	public function __toString()
	{
		$return = '';

		if (!is_null($this->_createdAt)) {
			$return .= 'Created ' . ($this->_createdBy ? 'by ' . $this->_createdBy . ' ' : '');
			$return .= 'on ' . $this->_createdAt->format(self::DATE_FORMAT) . "\n";
		}

		if (!is_null($this->_updatedAt)) {
			$return .= 'Last updated ' . ($this->_updatedBy ? 'by ' . $this->_updatedBy . ' ' : '');
			$return .= 'on ' . $this->_updatedAt->format(self::DATE_FORMAT) . "\n";
		}

		if (!is_null($this->_deletedAt)) {
			$return .= 'Deleted ' . ($this->_deletedBy ? 'by ' . $this->_deletedBy . ' ' : '');
			$return .= 'on ' . $this->_deletedAt->format(self::DATE_FORMAT);
		}

		return trim($return);
	}
}