<?php

require_once 'tests/units/Base.php';

use Kanboard\Plugin\SubTaskComplete\Controller\LimitedSubtaskStatusController;
use Kanboard\Plugin\SubTaskComplete\Helper\CustomSubtaskHelper;

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

    public function setUp(){

        $this->container['helper']->register('subtask', '\Kanboard\Plugin\SubTaskComplete\Helper\CustomSubtaskHelper');

        $this->$projectModel = new ProjectModel($this->container);
        $this->$projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $this->$userModel = new UserModel($this->container);
        $this->$taskCreationModel = new TaskCreationModel($this->container);
        $this->$taskFinderModel = new TaskFinderModel($this->container);
        $this->$subtaskModel = new SubtaskModel($this->container);
        $this->$subtaskStatusModel = new SubtaskStatusModel($this->container);

        //create a project
        $this->assertEquals(1, $this->$projectModel->create(array('name' => 'test_project')));
        //create a task
        $this->assertEquals(1, $this->$taskCreationModel->create(array('title' => 'test_task', 'project_id' => 1)));
        //create a subtask
        $this->assertEquals(1, $this->$subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));

        //check subtask exists.
        $this->$subtask = $this->$subtaskModel->getById(1);
        $this->$task = $this->$taskFinderModel->getById(1);
        $this->$project = $this->$projectModel->getById(1);

        $this->assertNotEmpty($this->$subtask);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $this->$subtask['status']);

        //create two users
        $this->assertEquals(2, $this->$userModel->create(array('username' => 'test_project_manager', 'email' => 'manager@localhost')));
        $this->assertEquals(3, $this->$userModel->create(array('username' => 'test_project_member', 'email' => 'member@localhost')));

        //assign roles to the users
        $this->$projectUserRoleModel->addUser(1, 2, Role::PROJECT_MANAGER);
        $this->$projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER);

        $this->assertEquals(Role::PROJECT_MANAGER, $this->$projectUserRoleModel->getUserRole(1, 2));
        $this->assertEquals(Role::PROJECT_MEMBER, $this->$projectUserRoleModel->getUserRole(1, 3));

        $this->$helper = new CustomSubtaskHelper($this->container);

    }
    public function testGIVEN_user_is_project_mananger_WHEN_subtask_is_todo_THEN_user_can_set_to_in_progress(){
        
        $this->assertEquals(SubtaskModel::STATUS_TODO, $this->$subtask['status']);

        $_SESSION['user'] = array('id' => 2, 'role' => Role::APP_ADMIN);
        $this->$helper->renderToggleStatus($this->$task, $this->$subtask, $fragment = '', $userId = 2, $debug=true);
        
        $this->$subtask = $this->$subtaskModel->getById(1);
        $this->assertNotEmpty($this->$subtask);
        error_log("status: ".$this->$subtask['status']);

        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $this->$subtask['status']);
        
 
    }
}

?>