<?php

namespace Kavenegar\Exceptions;

class ApiException extends BaseRuntimeException
{
	public function getName()
    {
        return 'ApiException';
    }
}

?>