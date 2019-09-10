<?php

require_once 'tests/units/Base.php';
require_once __DIR__.'app/Controller/BaseController.php';

use Kanboard\Plugin\SubTaskComplete\Controller\LimitedSubtaskStatusController;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectMetadataModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\ProjectGroupRoleModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\SubtaskStatusModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use Kanboard\Core\Security\Role;



class LimitedSubtaskStatusTest extends base {


    public function testGIVEN_user_is_project_mananger_WHEN_subtask_is_complete_THEN_user_can_set_to_not_started(){
        
        $this->container['helper']->register('subtask', '\Kanboard\Plugin\SubTaskComplete\Helper\CustomSubtaskHelper');
        
        $limitedController = new LimitedSubtaskStatusController($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskStatusModel = new SubtaskStatusModel($this->container);

        //create a project
        $this->assertEquals(1, $projectModel->create(array('name' => 'test_project')));
        //create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test_task', 'project_id' => 1)));
        //create a subtask
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));

        //check subtask exists.
        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);

        //create two users
        $this->assertEquals(2, $userModel->create(array('username' => 'test_project_manager', 'email' => 'manager@localhost')));
        $this->assertEquals(3, $userModel->create(array('username' => 'test_project_member', 'email' => 'member@localhost')));

        //assign roles to the users
        $projectUserRoleModel->addUser(1, 2, Role::PROJECT_MANAGER);
        $projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER);

        
        // Set the current logged user
        $_SESSION['user'] = array('id' => 2);

        //think we need a session
        $limitedController->change();
        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $subtask['status']);

        $limitedController->change();
        $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);
        
        
        
       
    }
}

?>