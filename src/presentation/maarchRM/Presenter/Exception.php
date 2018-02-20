<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter;

/**
 * Exception serializer html
 *
 */
class Exception
{
    public $view;

    protected $json;
    protected $translator;

    /**
     * Constuctor of welcomePage html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The view
     * @param \dependency\localisation\TranslatorInterface $translator The translator object
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->json = $json;
        $this->translator = $translator;

        $this->translator->setCatalog('exceptions/messages');
    }

    /**
     * Display error
     * @param Exception $exception
     *
     * @return string
     */
    public function Exception($exception)
    {
        $exception->setMessage($this->translator->getText($exception->getFormat()));

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return $this->presentJson($exception);
        }

        return $this->presentHtml($exception);
    }

    /**
     * Display error
     * @param Exception $exception
     *
     * @return string
     */
    public function NotFoundException($exception)
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return $this->presentJson($exception);
        }

        return $this->presentHtml($exception);
    }

    protected function presentJson($exception)
    {
        $this->json->status = false;
        if (method_exists($exception, "setMessage")) {
            $exception->setMessage($this->translator->getText($exception->getFormat()));
            $this->json->message = $exception->getMessage();
        } else if ($message = $exception->getMessage()) {
            $this->json->message = $message;
        } else {
            $this->json->message = $this->translator->getText(
                "An error occured during the process of your request. Please contact the administrator of the application.");
            $this->json->exception = $exception;
        }
        
        if (isset($exception->errors)) {
            $errors = [];
            foreach ($exception->errors as $error) {
                $error->setMessage($this->translator->getText($error->getFormat()));

                $variables = [];

                $errorVariables = $error->getVariables();
                if (is_array($errorVariables)) {
                    foreach ($errorVariables as $name => $value) {
                        $name = $this->translator->getText($name);
                        $value = $this->translator->getText($value);
                        $variables[$name] = $value;
                    }
                }
                $error->setVariables($variables);
                
                $errors[] = $error;
            }
            $this->json->errors = $errors;
        }

        return $this->json->save();
    }

    protected function presentHtml($exception)
    {
        //$this->view->addHeaders();
        //$this->view->useLayout();
        $this->view->addContentFile("dashboard/error.html");

        if (method_exists($exception, "setMessage")) {
            $exception->setMessage($this->translator->getText($exception->getFormat()));

            $this->view->setSource('error', $exception);
        } else {
            $previous = null;

            if ($exception instanceof \core\Exception) {
                $previous = $exception;
            }

            $newException = new \core\Exception(
                $this->translator->getText("An error occured during the process of your request. Please contact the administrator of the application."),
                500,
                $previous
            );

            $this->view->setSource('error', $newException);
        }
        $this->view->merge();

        $this->view->translate();

        return $this->view->saveHtml();
    }
}
