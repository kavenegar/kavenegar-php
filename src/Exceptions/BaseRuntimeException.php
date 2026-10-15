<?php

namespace Kavenegar\Exceptions;

class BaseRuntimeException extends \RuntimeException 
{
	public function getName()
    {
        return 'BaseRuntimeException';
    }
    public function __construct($message, $code=0) {
        parent::__construct($message, $code);
    }
	public function errorMessage(){
		return "\r\n".$this->getName() . "[{$this->code}] : {$this->message}\r\n";
	}
}

?>