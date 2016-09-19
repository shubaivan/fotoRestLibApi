<?php

namespace AppBundle\Exception;


class ValidatorException extends \Exception
{
    /**
     * @var array
     */
    private $errorsArray = [];

    /**
     * @param array           $errors
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(array $errors = [], $message = '', $code = 0, \Exception $previous = null)
    {
        $this->errorsArray = $errors;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errorsArray;
    }

    /**
     * @param array $message
     *
     * @return array
     */
    public function addError(array $message)
    {
        $this->errorsArray[] = $message;
    }
}