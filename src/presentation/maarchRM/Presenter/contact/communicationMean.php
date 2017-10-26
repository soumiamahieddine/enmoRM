<?php

/*
 * This file is part of the contact package.
 *
 * (c) Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace presentation\maarchRM\Presenter\contact;

/**
 * Bundle contact html serializer
 *
 * @package Contact
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class communicationMean
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    public $view;

    /**
     * __construct
     *
     * @param \dependency\html\Document $view the view
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog('contact/contact');
        $this->json->status = true;
    }

    /**
     *  Print the index contact
     * @param $commMeans list of commMean
     *
     * @return view View with the commMeans list
    **/
    public function index($commMeans)
    {
        $view = $this->view;

        //$view->addHeaders();
        //$view->useLayout();
        $view->addContentFile("contact/adminCommMean/index.html");

        $view->setSource("commMeans", $commMeans);
        $this->view->translate();
        $view->merge();

        return $view->saveHtml();        
    }

    /**
     * Serializer JSON for edit a commMean
     * @return object JSON object with a status and message parameters
     */
    public function  editCommMean($commMean)
    {      
        return json_encode($commMean);
    }

    /**
     * Result message from adding a commMean
     * @return result object
     */
    public function addCommMean($return)
    {
        $json = \laabs::Dependency('json')->JsonObject();
        if ($return != '') {
            $json->status = true;
            $json->message = $this->translator->getText("The communication mean has been successfully added.");
        } else {
            // Manage errors
            $json->status = false;
            $json->message = $return->message;
        }
        return $json->save();
    }

    /**
     * Result message from editing a commMean
     * @return result object
     */
    public function modifyCommMean($return)
    {
        $json = \laabs::Dependency('json')->JsonObject();
        if ($return != '') {
            $json->status = true;
            $json->message = $this->translator->getText("The communication mean has been successfully modified.");
        } else {
            // Manage errors
            $json->status = false;
            $json->message = $return->message;
        }
        return $json->save();
    }

    /**
     * Result message from deleting a commMean
     * @return result object
     */
    public function deleteCommMean($return)
    {
        $json = \laabs::Dependency('json')->JsonObject();
        if ($return != '') {
            $json->status = true;
            $json->message = $this->translator->getText("The communication mean has been successfully deleted.");
        } else {
            // Manage errors
            $json->status = false;
            $json->message = $return->message;
        }
        return $json->save();
    }
    
}