<?php

namespace Kanboard\Plugin\SubTaskComplete;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
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
}

