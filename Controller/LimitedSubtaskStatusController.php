<?php


namespace Kanboard\Plugin\SubTaskComplete\Controller;

use Kanboard\Core\Security\Role;
use Kanboard\Controller\BaseController;

/**
 * Subtask Status
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class LimitedSubtaskStatusController extends BaseController
{
    /**
     * Change status to the next status: Toto -> In Progress -> Done
     * Only if the user is the project mananger.
     *
     * @access public
     */
    public function change($debug=false, $debug_params=null)

    {

        if($debug){
            $task = $debug_params['task'];
            $subtask = $debug_params['subtask'];
            $fragment = $debug_params['fragment']; 
            $projectid = $debug_params['project_id'];

        }
        else{
            $task = $this->getTask();
            $subtask = $this->getSubtask($task);
            $fragment = $this->request->getStringParam('fragment');
            $projectid = $this->request->getIntegerParam('project_id');
        }


        $project_role = $this->helper->projectRole->getProjectUserRole($projectid);

        error_log("role: ".$project_role);
  
        // only change the status from complete to not started if user is the project manager
        switch($subtask['status']){
            
            case 0:
                $this->updateStatus($subtask);
                break;
            case 1:
                $this->updateStatus($subtask);
                break;
            case 2:
                if ($project_role == Role::PROJECT_MANAGER){
                    $this->updateStatus($subtask);
                }
                break;
            default:
                error_log("Subtask status not recognised.");
                break;
        }

        if ($fragment === 'table') {
            $html = $this->renderTable($task);
        } elseif ($fragment === 'rows') {
            $html = $this->renderRows($task);
        } else {
            $html = $this->helper->subtask->renderToggleStatus($task, $subtask);
        }
        if(!$debug){
            $this->response->html($html);
        }
    }

    /**
     * Update the status of the subtask
     *
     * @access public
     * @param array $subtask 
     */
    private function updateStatus(array $subtask){

        $status = $this->subtaskStatusModel->toggleStatus($subtask['id']);
        $subtask['status'] = $status;

        error_log("status in controller: ".$status);
    }

    /**
     * Start/stop timer for subtasks
     *
     * @access public
     */
    public function timer()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);
        $timer = $this->request->getStringParam('timer');

        if ($timer === 'start') {
            $this->subtaskTimeTrackingModel->logStartTime($subtask['id'], $this->userSession->getId());
        } elseif ($timer === 'stop') {
            $this->subtaskTimeTrackingModel->logEndTime($subtask['id'], $this->userSession->getId());
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($task['id']);
        }

        $this->response->html($this->template->render('subtask/timer', array(
            'task'    => $task,
            'subtask' => $this->subtaskModel->getByIdWithDetails($subtask['id']),
        )));
    }

    /**
     * Render table
     *
     * @access protected
     * @param  array  $task
     * @return string
     */
    protected function renderTable(array $task)
    {
        return $this->template->render('subtask/table', array(
            'task'     => $task,
            'subtasks' => $this->subtaskModel->getAll($task['id']),
            'editable' => true,
        ));
    }

    /**
     * Render task list rows
     *
     * @access protected
     * @param  array  $task
     * @return string
     */
    protected function renderRows(array $task)
    {
        $userId = $this->request->getIntegerParam('user_id');

        if ($userId > 0) {
            $task['subtasks'] = $this->subtaskModel->getAllByTaskIdsAndAssignee(array($task['id']), $userId);
        } else {
            $task['subtasks'] = $this->subtaskModel->getAll($task['id']);
        }

        return $this->template->render('task_list/task_subtasks', array(
            'task'    => $task,
            'user_id' => $userId,
        ));
    }
}
