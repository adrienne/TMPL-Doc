<?php 

class Template_Parser_Buffer implements ArrayAccess, Countable, Iterator {
	
	protected $buffer = array();
	
	protected $pointer = 0;

	public function reset() 
	{
		$this->buffer = array();
	}
	
	public function append($value) 
	{
		$this->buffer[] = $value;
	}
	
	public function offsetSet($offset, $value) 
	{
		if (empty($offset)) 
		{
			$offset = $this->pointer;
			$this->pointer++;
		}
		
		$this->buffer[$offset] = $value;
	}
	
	public function offsetGet($offset) 
	{
		return (array_key_exists($offset, $this->buffer)) ? $this->buffer[$offset] : '';
	}
	
	public function offsetExists($offset) 
	{
		return array_key_exists($offset, $this->buffer);
	}
	
	public function offsetUnset($offset) 
	{
		unset($this->buffer[$offset]);
	}
	
	public function rewind()
	{	
		reset($this->buffer);
	}

	public function current()
	{
		return current($this->buffer);
	}

	public function key() 
	{
		return key($this->buffer);
	}

	public function next() 
	{
		return next($this->buffer);
	}

	public function valid()
	{
		$key = key($this->buffer);
 		return ($key !== NULL && $key !== FALSE);
	}
		
	public function __toString() 
	{
		return implode('', $this->buffer);
	}
		
	public function as_string() 
	{
		return $this->__toString();
	}
	
	public function count() 
	{	
		return count($this->buffer);
	}
	
}