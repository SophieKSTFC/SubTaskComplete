<?php

require_once 'tests/units/Base.php';

use Kanboard\Plugin\SubTaskComplete\Plugin;

class PluginTest extends Base
{
    public function testPlugin(){

        $this->plugin = new Plugin($this->container);
        $this->assertSame(null, $this->$plugin->initialize());
        $this->assertSame(null, $this->$plugin->onStartup());
        $this->assertNotEmpty($this->$plugin->getPluginName());
        $this->assertNotEmpty($this->$plugin->getPluginDescription());
        $this->assertNotEmpty($this->$plugin->getPluginAuthor());
        $this->assertNotEmpty($this->$plugin->getPluginVersion());
        $this->assertNotEmpty($this->$plugin->getPluginHomepage());
    }

}
