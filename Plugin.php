<?php

namespace Kanboard\Plugin\SubTaskComplete;

use Kanboard\Core\Plugin\Base;
use Kanboard\Model\SubtaskModel;
use Kanboard\Core\Translator;
use Kanboard\Core\Security\Role;
use Kanboard\Plugin\SubTaskComplete\Controller\LimitedSubtaskStatusController;

class Plugin extends Base
{
    public function initialize()
    {   
        //overwrite the subtask helper with our own custom helper.
        $this->helper->register('subtask', '\Kanboard\Plugin\SubTaskComplete\Helper\CustomSubtaskHelper');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        return t('SubTaskComplete');
    }

    public function getPluginDescription()
    {
        return t('Kanboard plugin to stop non project managers amending subtask complete status');
    }

    public function getPluginAuthor()
    {
        return 'Sophie Kirkham';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'tbc';
    }

    public function getCompatibleVersion(){
        return  '>= 1.0.0';
    }

}

