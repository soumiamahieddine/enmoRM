<?php

namespace core\Response;

interface ResponseInterface
{
    public function setMode($mode);

    public function setCode($code);

    public function setType($type);

    public function setLanguage($language);

    public function setBody($body);

    public function send();

}