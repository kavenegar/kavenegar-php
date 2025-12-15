<?php

namespace Kavenegar\Exceptions;

class HttpException extends BaseRuntimeException
{
	public function getName(): string
    {
        return 'HttpException';
    }	
}

?>