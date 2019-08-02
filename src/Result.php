<?php

namespace Nealis\Result;

class Result implements \JsonSerializable
{

    protected $success = true;
    protected $title = '';
    protected $successTitle = '';
    protected $warningTitle = '';
    protected $errorTitle = '';
    protected $messages = [];
    protected $warnings = [];
    protected $errors = [];
    protected $result;
    protected $data = [];

    protected $count;
    protected $totalRecords;
    protected $page;
    protected $totalPages;
    protected $limit;
    protected $offset;

    protected $command = '';

    public function __construct($config = [])
    {
        $this->fromArray($config);
    }

    public function fromArray($config)
    {
        foreach($config as $name=>$value)
        {
            if(property_exists($this, $name))
            {
                $this->$name = $value;
            }
        }

        $this->updateSuccess();
    }

    public function toArray()
    {
        return [
            'success' => $this->isSuccess(),
            'title' => $this->getTitle(),
            'messages' => $this->getMessages(),
            'warnings' => $this->getWarnings(),
            'errors' => $this->getErrors(),
            'result' => $this->getResult(),
            'data' => $this->getData(),

            'count' => $this->getCount(),
            'totalRecords' => $this->getTotalRecords(),
            'page' => $this->getPage(),
            'totalPages' => $this->getTotalPages(),
            'limit' => $this->limit,
            'offset' => $this->offset,

            'command' => $this->getCommand(),
        ];
    }

    public function addError($key, $message = null)
    {
        if(is_null($message))
            $this->errors[] = $key;
        else
            $this->errors[$key] = $message;

        $this->updateSuccess();

        return $this;
    }

    public function addWarning($key, $message = null)
    {
        if(is_null($message))
            $this->warnings[] = $key;
        else
            $this->warnings[$key] = $message;

        $this->updateSuccess();

        return $this;
    }

    public function addMessage($key, $message = null)
    {
        if(is_null($message))
            $this->messages[] = $key;
        else
            $this->messages[$key] = $message;

        $this->updateSuccess();

        return $this;
    }

    public function updateSuccess()
    {
        $this->setSuccess(empty($this->errors) && empty($this->warnings));

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getErrorAt($position)
    {
        $errors = $this->getErrors();
        if (!array_key_exists($position, $errors)) {
            throw new \Exception('No error at position ' . $position);
        }
        return $errors[$position];
    }

    public function getFirstError()
    {
        return $this->getErrorAt(0);
    }

    public function getLastError()
    {
        $errors = $this->getErrors();
        if (count($errors) == 0) {
            throw new \Exception('No errors found');
        }
        $position = count($this->getErrors()) - 1;
        return $this->getErrorAt($position);
    }

    public function getErrorsString($withKey = true)
    {
        $errors = $this->getErrors();

        return $this->messagesToString($errors, $withKey);
    }

    public function getWarningsString($withKey = true)
    {
        $warnings = $this->getWarnings();

        return $this->messagesToString($warnings, $withKey);
    }

    /**
     * @param array $messages
     * @param bool $withKey
     * @return array
     */
    protected function messagesToString($messages, $withKey = true)
    {
        $messagesString = [];
        if(!empty($messages)) {

            foreach ($messages as $key => $message) {
                if(is_array($message)) {
                    $messageString = PHP_EOL . implode(PHP_EOL, $message);
                    $messageString = ($withKey ? '[' . $key . ']' : '') . $messageString;
                    $messagesString[] =  $messageString;
                } else {
                    $messagesString[] = $message;
                }
            };
            $messagesString = implode(PHP_EOL, $messagesString);

        }

        return $messagesString;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if(!empty($this->title)) return $this->title;
        else if($this->isSuccess()) return $this->successTitle;
        else if(!empty($this->errors)) return $this->errorTitle;
        else if(!empty($this->warnings)) return $this->warningTitle;
        else return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $successTitle
     */
    public function setSuccessTitle($successTitle)
    {
        $this->successTitle = $successTitle;
    }

    /**
     * @param string $warningTitle
     */
    public function setWarningTitle($warningTitle)
    {
        $this->warningTitle = $warningTitle;
    }

    /**
     * @param string $errorTitle
     */
    public function setErrorTitle($errorTitle)
    {
        $this->errorTitle = $errorTitle;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages($messages)
    {
        if(!is_array($messages)) $messages = [$messages];
        $this->messages = $messages;
        $this->updateSuccess();
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param array $warnings
     */
    public function setWarnings($warnings)
    {
        if(!is_array($warnings)) $warnings = [$warnings];
        $this->warnings = $warnings;
        $this->updateSuccess();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        if(!is_array($errors)) $errors = [$errors];
        $this->errors = $errors;
        $this->updateSuccess();
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     * @param mixed $totalRecords
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param mixed $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }
}
