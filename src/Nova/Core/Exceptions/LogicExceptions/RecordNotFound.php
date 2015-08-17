<?php

namespace Nova\Core\Exceptions\LogicExceptions;


use Nova\Core\Exceptions\NovaException;
use Nova\Helpers\Hash;
use Nova\Helpers\Html;

class RecordNotFound extends NovaException {

    protected $exception;

    public function __construct($code = 0, Exception $previous = null)
    {
        $this->exception = Hash::last(explode('\\', __CLASS__));
        parent::__construct($code, $previous);
    }

    public function printTrace()
    {
        $header  = Html::tag('div', $this->exception . ' at ' . Hash::get($_SERVER, 'REQUEST_URI'), ['class' => 'head message']);
        $message = Html::tag('h2', "Record not found.", ['class' => 'message']);
        $debug   = Html::tag('div', $this->fullCallerString, ['class' => 'debug']);
        echo Html::tag('div', $header . $message . $debug, ['class' => 'exception']);
    }

} 