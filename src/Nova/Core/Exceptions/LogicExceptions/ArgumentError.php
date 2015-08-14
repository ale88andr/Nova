<?php

namespace Nova\Core\Exceptions\LogicExceptions;

use \Exception;
use Nova\Interfaces\ExceptionInterface;
use Nova\Helpers\Hash;

class ArgumentError extends Exception implements ExceptionInterface {

    private $fullCallerName;

    private $controllerWhoCall;
    private $controllerCallLine;
    private $methodWhoCall;
    private $calledFromClass;
    private $calledWithArgs;

    public function __construct($code =0, Exception $previous = null)
    {
        parent::__construct($this->setMessage(), $code, $previous);

        $callers = Hash::get($this->getTrace(), 0);
        $caller  = Hash::get($this->getTrace(), 1);
        $this->fullCallerName = Hash::get($caller, 'class') .
                                Hash::get($caller, 'type') .
                                Hash::get($caller, 'function') .
                                '() with arguments' .
                                implode(', ', Hash::get($caller, 'args'));
        $this->controllerWhoCall    = Hash::get($caller,  'class');
        $this->controllerCallLine   = Hash::get($callers, 'line');
        $this->methodWhoCall        = Hash::get($callers, 'function');
        $this->calledFromClass      = Hash::get($callers, 'class');
        $this->calledWithArgs       = Hash::get($callers, 'args')[0];
    }

    public function setMessage()
    {
        $header  = __CLASS__ . 'at ' . Hash::get($_SERVER, 'REQUEST_URI');
        $message = '"' . $this->calledWithArgs . '" not a valid argument for "' . $this->methodWhoCall . '"';
        $debug   =
//        return $traceMessage;
    }

    public function printTrace()
    {
        print_r("<pre>");
        print_r($this->getTrace());
    }

//echo "Message: " . $e->getMessage(). "\n\n";
//echo "File: " . $e->getFile(). "\n\n";
//echo "Line: " . $e->getLine(). "\n\n";
//echo "Trace: \n" . $e->getTraceAsString(). "\n\n";

} 