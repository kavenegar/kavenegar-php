<?php

namespace Kavenegar\Exceptions;

class NotProperlyConfiguredException extends BaseRuntimeException
{
    public function getName()
    {
        return 'NotProperlyConfigured';
    }	
}

?>