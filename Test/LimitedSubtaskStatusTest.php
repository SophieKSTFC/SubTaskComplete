<?php

require_once 'tests/units/Base.php';
use Kanboard\Plugin\SubTaskComplete\Controller\LimitedSubtaskStatusController;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectMetadataModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\UserModel;
use Kanboard\Core\Security\Role;



class LimitedSubtaskStatusTest extends base {


    public function testSubtaskAuthorisation(){
        
        $this->container['helper']->register('subtask', '\Kanboard\Plugin\SubTaskComplete\Helper\CustomSubtaskHelper');

        $limitedController = new LimitedSubtaskStatusController($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

    }
}

?>