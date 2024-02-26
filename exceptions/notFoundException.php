<?php

namespace emadisavi\phpmvc\exceptions;

class NotFoundException extends \Exception
{
    protected $message = 'Page Not Found';
    protected $code = 404;
}