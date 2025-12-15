<?php

namespace Kavenegar\Exceptions;

class BaseRuntimeException extends \RuntimeException 
{
	public function getName(): string
    {
        return 'BaseRuntimeException';
    }
    public function __construct(string $message, int $code = 0) {
        parent::__construct($message, $code);
    }
	public function errorMessage(): string {
		return "\r\n".$this->getName() . "[{$this->code}] : {$this->message}\r\n";
	}
}

?>