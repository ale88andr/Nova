<?php

namespace Nova\Core\Exceptions;

use \Nova\Helpers\Hash;

class NovaException extends \Exception {

    protected $fullCallerString;

    protected $controllerCallLine;

    protected $methodWhoCall;

    protected $calledFromClass;

    protected $calledWithArgs;

    public function __construct($code = 0, Exception $previous = null)
    {
        parent::__construct($code, $previous);

        $callers = Hash::get($this->getTrace(), 0);
        $caller  = Hash::get($this->getTrace(), 1);

        $this->fullCallerString = Hash::get($caller, 'class') .
            Hash::get($caller, 'type') .
            Hash::get($caller, 'function') .
            '() with arguments: \'' .
            implode(', ', Hash::get($caller, 'args')) .
            '\' at line: ' . Hash::get($caller, 'line');

        $this->controllerCallLine = Hash::get($callers, 'line');
        $this->methodWhoCall      = Hash::get($callers, 'function');
        $this->calledFromClass    = Hash::get($callers, 'class');
        $this->calledWithArgs     = Hash::get($callers, 'args')[0];
    }
} 