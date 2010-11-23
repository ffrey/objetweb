<?php
/**
 * 
 *
 */
class owArrayIterator implements ArrayAccess, Iterator {
	protected $data = null;

	public function __construct($init) {
		$this->data = $init;
	}

	/*** ArrayAccess ***/
	// http://www.lastown.com/forum/viewtopic.php?f=17&t=876&start=0
	/**
	 * @param offset
	 */
	public function offsetExists ($offset) {
		return array_key_exists($offset, $this->data);
	}

	/**
	 * @param offset
	 */
	public function offsetGet ($offset) {
		return $this->data[$offset];
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet ($offset, $value) {
		$this->data[$offset] = $value;
	}

	/*** Iterator ***/
	/**
	 * @param offset
	 */
	public function offsetUnset ($offset) {
		unset($this->data[$offset]);
	}

	public function rewind()
	{
		$this->index = 0;
	}

	public function current()
	{
		$k = array_keys($this->data);
		$var = $this->data[$k[$this->index]];
		return $var;
	}

	public function key()
	{
		$k = array_keys($this->data);
		$var = $k[$this->index];
		return $var;
	}

	public function next()
	{
		$k = array_keys($this->data);
		if (isset($k[++$this->index])) {
			$var = $this->data[$k[$this->index]];
			return $var;
		} else {
			return false;
		}
	}

	public function valid()
	{
		$k = array_keys($this->data);
		$var = isset($k[$this->index]);
		return $var;
	}
}