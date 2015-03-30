<?php

namespace Message\Cog\DB;

use Message\Cog\DB\Adapter\ConnectionInterface;

/**
* Query class
*
* Responsible for turning SQL queries into Result datasets.
*/
class Query implements QueryableInterface
{
	protected $_connection;
	protected $_params;
	protected $_query;
	protected $_parsedQuery;
	protected $_queryParser;

	private $_isSelect;
	private $_disableCache = false;

	protected static $_queryList = [];

	private static $_resultCache = [];

	protected $_typeTokens = array(
		's' => 'string',
		'i' => 'integer',
		'f' => 'float',
		'd'	=> 'datetime',
		'b'	=> 'boolean',
	);

	const TOKEN_REGEX = '/((\:[a-zA-Z0-9_\-\.]*)\??([a-z]*)?)|(\?([a-z]*))/us';

	public function __construct(ConnectionInterface $connection, QueryParser $queryParser, $query = null)
	{
		$this->setConnection($connection);
		$this->_query = $query;
		$this->_queryParser = $queryParser;
	}

	/**
	 * Runs a query against the data store
	 *
	 * Params to be interpolated in the query can be passed in via the second
	 * parameter. See readme for more info.
	 *
	 * @param  string $query  The query to run against the datastore.
	 * @param  mixed  $params Parameters to be interpolated in the query.
	 * @throws Exception      Throw exception if query fails
	 *
	 * @return Result         The data generated by the query.
	 */
	public function run($query = null, $params = array())
	{
		if ($query) {
			$this->_query  = $query;
		}
		$this->_params = (array)$params;

		$this->_parsedQuery = $this->_queryParser->parse($this->_query, $this->_params);

		if (!$this->_resultInCache()) {
			$result = $this->_connection->query($this->_parsedQuery);
			static::$_queryList[] = $this->_parsedQuery;

			if ($result === false) {
				throw new Exception($this->_connection->getLastError(), $this->_query);
			}

			if (!$this->_isSelect()) {
				$this->_clearResultCache();
			} elseif (false === $this->_disableCache) {
				$this->_cacheResult($result);
			}
		} else {
			$result = $this->_getCachedResult();
		}

		return new Result($result, clone $this);
	}

	/**
	 * Gets the static count of queries
	 *
	 * @return int
	 */
	public function getQueryCount()
	{
		return count(static::$_queryList);
	}

	/**
	 * Gets the static list of parsed queries run.
	 *
	 * @return array
	 */
	public function getQueryList()
	{
		return static::$_queryList;
	}

	/**
	 * Set the connection to use for this query. Useful if you want to run the
	 * same query against multiple connections.
	 *
	 * @param ConnectionInterface $connection
	 */
	public function setConnection(ConnectionInterface $connection)
	{
		$this->_connection = $connection;
	}

	/**
	 * Get the parsed query in its current state
	 *
	 * @return string       The parsed query
	 */
	public function getParsedQuery()
	{
		return $this->_parsedQuery;
	}

	/**
	 * Disable the result caching. To be used if running subqueries that have updates, deletes etc
	 */
	public function disableCache()
	{
		$this->_disableCache = true;
	}

	public function castValue($value, $type, $useNull)
	{
		// check for nullness
		if (is_null($value) && $useNull) {
			return 'NULL';
		}

		if ($value instanceof \DateTime) {
			$value = $value->getTimestamp();
		}

		// If a type is set to date then cast it to an int
		if ($type == 'd') {
		    $safe = (int) $value;
		} else {
			// Don't cast type if type is integer and value starts with @ (as it is an ID variable)
			if (!('i' === $type && '@' === substr($value, 0, 1))) {
				settype($value, $this->_typeTokens[$type]);
			}
			$safe = $this->_connection->escape($value);
		}
		// Floats are quotes to support all locales.
		// See: http://stackoverflow.com/questions/2030684/which-mysql-data-types-should-i-not-be-quoting-during-an-insert"
		if ($type == 's' || $type == 'f') {
			$safe = "'".$safe."'";
		}

		if ('b' === $type) {
			$safe = $value ? 1 : 0;
		}

		return $safe;
	}

	/**
	 * Check to see if query has been run before and the result exists in memory
	 *
	 * @return bool
	 */
	private function _resultInCache()
	{
		return array_key_exists($this->_getCacheKey(), static::$_resultCache);
	}

	/**
	 * Store the result in memory with the parsed query as the key
	 *
	 * @param Adapter\ResultInterface $result
	 */
	private function _cacheResult(Adapter\ResultInterface $result)
	{
		static::$_resultCache[$this->_getCacheKey()] = $result;
	}

	/**
	 * Get the result from the cache using the parsed query as the key
	 *
	 * @throws \LogicException   Throws exception if query result has not been cached
	 *
	 * @return Adapter\ResultInterface
	 */
	private function _getCachedResult()
	{
		if (!array_key_exists($this->_getCacheKey(), static::$_resultCache)) {
			throw new \LogicException('Attempting to get cached result that does not exist');
		}

		return static::$_resultCache[$this->_getCacheKey()];
	}

	/**
	 * Reset the result cache
	 */
	private function _clearResultCache()
	{
		static::$_resultCache = [];
	}

	/**
	 * Check to see if the query is a select query
	 *
	 * @return bool
	 */
	private function _isSelect()
	{
		if (null === $this->_isSelect) {
			$this->_isSelect = (bool) preg_match('/^select*/i', trim($this->_parsedQuery));
		}

		return $this->_isSelect;
	}

	/**
	 * Trim and hash the parsed query to create a key for the cached query
	 *
	 * @return string
	 */
	private function _getCacheKey()
	{
		return md5(trim($this->_parsedQuery));
	}
}