<?php 
namespace My {

    class A
    {
        /* Some useful functionality */
    	public function hello() 
    	{
    		echo 'hello !';
    	}
    }

    class B
    {
        protected $a = null;
        public function __construct(A $a)
        {
            $this->a = $a;
        }
        public function hello()
        {
        	return $this->a->hello();
        }
    }
}