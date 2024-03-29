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

  
    public function testGIVEN_user_is_project_mananger_WHEN_subtask_is_todo_THEN_user_can_set_to_in_progress(){
        

        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskStatusModel = new SubtaskStatusModel($this->container);

        //create a project
        $this->assertEquals(1, $projectModel->create(array('name' => 'test_project')));
        //create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test_task', 'project_id' => 1)));
        //create a subtask
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));

        //get subtask, task and project.
        $subtask = $subtaskModel->getById(1);
        $task = $taskFinderModel->getById(1);
        $project = $projectModel->getById(1);

        //check subtask, task and project are not empty 
        $this->assertNotEmpty($subtask);
        $this->assertNotEmpty($task);
        $this->assertNotEmpty($project);

        // assert subtask is TODO initially
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);

        //create two users
        $this->assertEquals(2, $userModel->create(array('username' => 'test_project_manager', 'email' => 'manager@localhost')));
        $this->assertEquals(3, $userModel->create(array('username' => 'test_project_member', 'email' => 'member@localhost')));

        //assign roles to the users
        $projectUserRoleModel->addUser(1, 2, Role::PROJECT_MANAGER);
        $projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER);

        // check roles are correct
        $this->assertEquals(Role::PROJECT_MANAGER, $projectUserRoleModel->getUserRole(1, 2));
        $this->assertEquals(Role::PROJECT_MEMBER, $projectUserRoleModel->getUserRole(1, 3));

        // create out custom subtask helper
        $helper = new CustomSubtaskHelper($this->container);

        // set the user to be a project manager
        $_SESSION['user'] = array('id' => 2, 'role' => Role::APP_ADMIN);

        // toggle status to in progress
        $helper->renderToggleStatus($task, $subtask, $fragment = '', $userId = 2, $debug=true);
        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $subtask['status']);

        // toggle status to complete
        $helper->renderToggleStatus($task, $subtask, $fragment = '', $userId = 2, $debug=true);
        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);

        // toggle status to to do again.
        $helper->renderToggleStatus($task, $subtask, $fragment = '', $userId = 2, $debug=true);
        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);

        //switch user to project member and confirm status changes
        $_SESSION['user'] = array('id' => 3, 'role' => Role::APP_ADMIN);

        // toggle status to in progress
        $this->assertEquals(SubtaskModel::STATUS_TODO, $subtask['status']);
        $helper->renderToggleStatus($task, $subtask, $fragment = '', $userId = 3, $debug=true);
        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_INPROGRESS, $subtask['status']);

        // toggle status to complete
        $helper->renderToggleStatus($task, $subtask, $fragment = '', $userId = 3, $debug=true);
        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);

        // toggle status to to do again and assert it does not change
        $helper->renderToggleStatus($task, $subtask, $fragment = '', $userId = 3, $debug=true);
        $subtask = $subtaskModel->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(SubtaskModel::STATUS_DONE, $subtask['status']);

    }

}

?>