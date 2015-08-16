<?php

namespace Nova\Core\Exceptions\LogicExceptions;

use Nova\Core\Exceptions\NovaException;
use Nova\Helpers\Html;
use Nova\Helpers\Hash;
use Nova\Interfaces\ExceptionInterface;


class RequireFileException extends NovaException implements ExceptionInterface {

    protected $exception;

    private $filePath;

    public function __construct($file, $code = 0, Exception $previous = null)
    {
        $this->exception = Hash::last(explode('\\', __CLASS__));
        $this->filePath = $file;
        parent::__construct($code, $previous);
    }

    public function printTrace()
    {
        $header  = Html::tag('div', $this->exception . ' at ' . Hash::get($_SERVER, 'REQUEST_URI'), ['class' => 'head message']);
        $message = Html::tag('h2', "Requested file '{$this->filePath}' not found", ['class' => 'message']);
        $debug   = Html::tag('div', $this->fullCallerString, ['class' => 'debug']);
        echo Html::tag('div', $header . $message . $debug, ['class' => 'exception']);
    }

} 