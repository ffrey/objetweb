<?php
namespace { 
    function owPrint($var = null) { return 'this is from GLOBAL owPrint function... : '.$var; }
    const OWCONST = 'hello';
    class Text 
    {
        public function show($arg)
        {
            return sprintf('GLOBAL argument is %s'.PHP_EOL, $arg);
        }
    }
    function echoGlob($var)
    {
        return sprintf('hello %s from %s', $var, __NAMESPACE__);
    }
}
namespace ow\lib {
    class Iam 
    {
        public function __toString()
        {
            return sprintf('je suis %s  dans %s !!!'.PHP_EOL, __CLASS__, __NAMESPACE__);
        }
    }
}