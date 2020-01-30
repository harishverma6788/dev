<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use DateTime;

/**
 * Reports Controller
 *
 * @property \App\Model\Table\ReportsTable $Reports
 */
class ReportsController extends AppController {

    /**
     * Index method
     *
     * @return void
     */
    public function index() {
        //code added
        $curr_id = $this->Auth->user('id');

        $g_id = $this->Auth->user('group_id');
        $this->loadModel('Users');
        $this->loadModel('UserProfiles');
        // TableRegistry::get('Users');
        $users = $this->Reports->Users;

        //   $this->set(compact('users'));
        if ($g_id == 3) {
            $reports = $this->Reports->find('all')->where(['user_id' => $curr_id]);
            $i = 0;
            foreach ($reports as $list) {
                $query = TableRegistry::get('Users')->find('all')->where(['id' => $list->user_id]);
                $user[$i] = $query->first();
                $query1 = TableRegistry::get('UserProfiles')->find('all')->where(['user_id' => $list->user_id]);
                $userprofile[$i] = $query1->first();
                $query2 = TableRegistry::get('Users')->find('all')->where(['id' => $list->last_update]);
                $lastupdate[$i] = $query2->first();
                $i++;
            }

            // $this->paginate = [
            //    'conditions' => ['generatedby' => $curr_id],
            //     'contain' => ['Users']
            //   ];
        } else {
            $reports = $this->Reports->find('all');
            $i = 0;
            foreach ($reports as $list) {
                $query = TableRegistry::get('Users')->find('all')->where(['id' => $list->user_id]);
                $user[$i] = $query->first();
                $query1 = TableRegistry::get('UserProfiles')->find('all')->where(['user_id' => $list->user_id]);
                $userprofile[$i] = $query1->first();
                $query2 = TableRegistry::get('Users')->find('all')->where(['id' => $list->last_update]);
                $lastupdate[$i] = $query2->first();
                $i++;
            }

          
        }
     
        $this->set(compact('user', 'reports', 'userprofile', 'lastupdate'));
        $this->set('_serialize', ['user', 'reports', 'userprofile', 'lastupdate']);
        //end code added
    }

    /**
     * View method
     *
     * @param string|null $id Report id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null, $agentid = null) {
        $report = $this->Reports->get($id, ['contain' => ['ReportAccbalances']]);
        $this->loadModel('Users');
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        $this->loadModel('Customers');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        $users = $this->Reports->Users;
        /////////////// For Advance Money //////////////////    
        if (!empty($report->genrate_from)) {
            $date1 = $report->genrate_from->i18nFormat('yyyy-MM-dd');
        } else {
            $date1 = '';
        }
        $date2 = $report->genrate_to->i18nFormat('yyyy-MM-dd');

        if (!empty($date1)) {
            $query = $this->AdvanceMoneyDetails->find('all')->where([
                        'AdvanceMoneyDetails.issue_date BETWEEN :start AND :end',
                        'AdvanceMoneyDetails.agent_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query = $this->AdvanceMoneyDetails->find('all', ['conditions' => ['AdvanceMoneyDetails.agent_id' => $agentid, 'AdvanceMoneyDetails.Issue_date <=' => $date2]]);
        }
        /////////////// For Advance Money //////////////////
        /////////////// For Loans Report //////////////////
        if (!empty($date1)) {
            $query1 = $this->Loans->find('all')->where([
                        'Loans.Issue_date BETWEEN :start AND :end',
                        'Loans.agent_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $agentid, 'Loans.Issue_date <=' => $date2]]);
        }
        /////////////// For Loans Report //////////////////
        /////////////// For Expenses Report //////////////////
        if (!empty($date1)) {
            $query2 = $this->DailyExpenses->find('all')->where([
                        'DailyExpenses.release_date BETWEEN :start AND :end',
                        'DailyExpenses.user_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $agentid, 'DailyExpenses.release_date <=' => $date2]]);
        }
        /////////////// For Expenses Report //////////////////
        /////////////// For Bonuses Report //////////////////
        if (!empty($date1)) {
            $customerData = array();
            $customerName = array();
            $query3 = $this->Bonuses->find('all')->where([
                        'Bonuses.release_date BETWEEN :start AND :end',
                        'Bonuses.agent_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
            foreach ($query3 as $list) {
                $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                if (!empty($customerData[$list->id])) {
                    $customerName[$list->id] = $customerData[$list->id]->name;
                } else {
                    $customerName[$list->id] = '';
                }
            }
        } else {
            $customerData = array();
            $customerName = array();
            $query3 = $this->Bonuses->find('all', ['conditions' => ['Bonuses.agent_id' => $agentid, 'Bonuses.release_date <=' => $date2]]);

            foreach ($query3 as $list) {
                $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                if (!empty($customerData[$list->id])) {
                    $customerName[$list->id] = $customerData[$list->id]->name;
                } else {
                    $customerName[$list->id] = '';
                }
            }
        }
        /////////////// For Bonuses Report //////////////////
        /////////////// For Money collected Report //////////////////
        if (!empty($date1)) {
            $query4 = $this->MoneyCollectedDetails->find('all')->where([
                        'MoneyCollectedDetails.collected_date BETWEEN :start AND :end',
                        'MoneyCollectedDetails.user_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $agentid, 'MoneyCollectedDetails.collected_date <=' => $date2]]);
        }
        /////////////// For Money Collected Report //////////////////
//            $users = $this->Reports->Users->find('list', ['limit' => 200]);
        $this->set(compact('report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'customerName'));
        $this->set('_serialize', ['report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'customerName']);

        $this->set('report', $report);
        $this->set('_serialize', ['report']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add() {

        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $this->loadModel('ReportAccbalances');
        TableRegistry::get('ReportAccbalances');
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        $this->loadModel('Customers');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        //added code
        $query = $this->Reports->find('all', [
            'contain' => ['ReportAccbalances'],
            'order' => ['Reports.id' => 'DESC'],
            'limit' => 1
        ]);
        $reportlast = $query->first();
          if (!empty($reportlast)){
             $reportlasts = $this->ReportAccbalances->find('all', [
            'conditions' => ['ReportAccbalances.report_id' => $reportlast->id],
                // 'order' => ['ReportAccbalances.id' => 'DESC'],        
                // 'limit' => 1
        ]);   
          }
      
        // =   $query2->first();
        //added code 
        $AgentData = $this->Users->find('list', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1, 'Users.id' => $user['id']])->toArray();
        $UsersData = $this->Users->find('all', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1])->order(['Users.id' => 'ASC'])->limit(10)->toArray();


        $customerData = $customerData = $this->Customers->find('all', ['contain' => 'Users'])->toArray();

        $options1 = array();
        foreach ($customerData as $customerDatas) {
            $options1[$customerDatas->id]['value'] = $customerDatas->name;
        }


        $options = array();
        foreach ($UsersData as $UsersDatas) {
            $options[$UsersDatas->id]['value'] = $UsersDatas->name . ',' . $UsersDatas->user_profile->ic_number;
			$last_agent_id = $UsersDatas->id;
        }

        $report = $this->Reports->newEntity();

        if ($this->request->is('post', 'put', 'get')) {

            $report = $this->Reports->patchEntity($report, $this->request->data);

            if ($this->Reports->save($report)) {
                $this->Flash->success(__('The report has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The report could not be saved. Please, try again.'));
            }
        }
        $users = $this->Reports->Users->find('list', ['limit' => 200]);
        $this->set(compact('report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'AgentData', 'reportlasts', 'options1','last_agent_id'));
        $this->set('_serialize', ['report', 'options', 'query', 'query1', 'query2', 'query3', 'AgentData', 'options1']);
    }
	public function getagents(){
		$user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
		$last_agnt_id = $_POST['last_agnt_id'];
        $this->loadModel('Users');
        $this->loadModel('Customers');
        $lastpage = '';
       
        $UsersData = 	$UsersData = $this->Users->find('all', ['contain' => 'UserProfiles'])->where(['Users.id >'=>$last_agnt_id, 'group_id' => 3, 'status' => 1])->order(['Users.id' => 'ASC'])->limit(10)->toArray();
        $options = array();
        foreach ($UsersData as $UsersDatas) {
            $options[$UsersDatas->id]['value'] = $UsersDatas->name . ',' . $UsersDatas->user_profile->ic_number;
			$last_agent_id = $UsersDatas->id;
        }
      
		
		
		$dd  = '';
		$ee  = '';
		$array = array();
		if(!empty($options)){
		$count = 1;
		foreach($options as $customer_id => $customer_value){
		if(!empty($customer_value) && !in_array($customer_id, $array)){
			$dd  .= '<li class="active" data-value = "'.$customer_id.'" >'.$customer_value['value'].'</li>';
			$ee .= '<option value="'.$customer_id.'">'.$customer_value['value'] .'</option>';
			$count++;
			array_push($array,$customer_id);
		}
		 }
		echo $dd.'___'.$ee.'___'.$last_agent_id;
		die();
		 }
	
		//$this->set(compact('options','last_agent_id'));
       // $this->set('_serialize', ['options']);
	}
    /**
     * Edit method
     *
     * @param string|null $id Report id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $agentid = null) {
//        print_r($id);exit;
        $report = $this->Reports->get($id, ['contain' => ['ReportAccbalances']]);
        $this->loadModel('Users');
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        $users = $this->Reports->Users;
        /////////////// For Advance Money //////////////////    
        if (!empty($report->genrate_from)) {
            $date1 = $report->genrate_from->i18nFormat('yyyy-MM-dd');
        } else {
            $date1 = '';
        }
        $date2 = $report->genrate_to->i18nFormat('yyyy-MM-dd');

        if (!empty($date1)) {
            $query = $this->AdvanceMoneyDetails->find('all')->where([
                        'AdvanceMoneyDetails.issue_date BETWEEN :start AND :end',
                        'AdvanceMoneyDetails.agent_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query = $this->AdvanceMoneyDetails->find('all', ['conditions' => ['AdvanceMoneyDetails.agent_id' => $agentid, 'AdvanceMoneyDetails.Issue_date <=' => $report->$date2]]);
        }
        /////////////// For Advance Money //////////////////
        /////////////// For Loans Report //////////////////
        if (!empty($date1)) {
            $query1 = $this->Loans->find('all')->where([
                        'Loans.Issue_date BETWEEN :start AND :end',
                        'Loans.agent_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $agentid, 'Loans.Issue_date <=' => $date2]]);
        }
        /////////////// For Loans Report //////////////////
        /////////////// For Expenses Report //////////////////
        if (!empty($date1)) {
            $query2 = $this->DailyExpenses->find('all')->where([
                        'DailyExpenses.release_date BETWEEN :start AND :end',
                        'DailyExpenses.user_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $agentid, 'DailyExpenses.release_date <=' => $date2]]);
        }
        /////////////// For Expenses Report //////////////////
        /////////////// For Bonuses Report //////////////////
        if (!empty($date1)) {
            $query3 = $this->Bonuses->find('all')->where([
                        'Bonuses.release_date BETWEEN :start AND :end',
                        'Bonuses.agent_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query3 = $this->Bonuses->find('all', ['conditions' => ['Bonuses.agent_id' => $agentid, 'Bonuses.release_date <=' => $date2]]);
        }
        /////////////// For Bonuses Report //////////////////
        /////////////// For Money collected Report //////////////////
        if (!empty($date1)) {
            $query4 = $this->MoneyCollectedDetails->find('all')->where([
                        'MoneyCollectedDetails.collected_date BETWEEN :start AND :end',
                        'MoneyCollectedDetails.user_id' => $agentid
                    ])
                    ->bind(':start', new \DateTime($date1), 'date')
                    ->bind(':end', new \DateTime($date2), 'date');
        } else {
            $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $agentid, 'MoneyCollectedDetails.collected_date <=' => $date2]]);
        }


        $this->set(compact('report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4'));
        $this->set('_serialize', ['report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4']);

        $this->set('report', $report);
        $this->set('_serialize', ['report']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Report id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $report = $this->Reports->get($id);
        if ($this->Reports->delete($report)) {
            $this->Flash->success(__('The report has been deleted.'));
        } else {
            $this->Flash->error(__('The report could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function getreportdata() {
        $this->viewBuilder()->layout('');
        $this->loadModel('Users');
        TableRegistry::get('Users');
        $this->loadModel('Customers');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');

        /////////////// For Advance Money //////////////////
        $verify = 'true';
        if ($this->request->is('ajax')) {
            $datee1 = str_replace('/', '-', $_POST['generatefrom']);
            $datee2 = str_replace('/', '-', $_POST['generateto']);
            if (!empty($datee1)) {
                $date1 = date_create($datee1)->format('Y-m-d');
            } else {
                $date1 = '';
            }
            $date2 = date_create($datee2)->format('Y-m-d');
            //print_r($date1);
            //print_r($_POST['agentid']);exit;

            $report = $query = $this->Reports->find('all', [
                        'order' => ['Reports.created' => 'DESC']
                    ])->where(['user_id' => $_POST["agentid"]])->first();
            if (!empty($date1)) {
                $query = $this->AdvanceMoneyDetails->find('all')->where([
                            'AdvanceMoneyDetails.issue_date BETWEEN :start AND :end',
                            'AdvanceMoneyDetails.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query = $this->AdvanceMoneyDetails->find('all', ['conditions' => ['AdvanceMoneyDetails.agent_id' => $_POST['agentid'], 'AdvanceMoneyDetails.issue_date <=' => $date2]]);
            }
           
            /////////////// For Advance Money //////////////////
            /////////////// For Loans Report //////////////////
            if (!empty($date1)) {
                $query1 = $this->Loans->find('all')->where([
                            'Loans.Issue_date BETWEEN :start AND :end',
                            'Loans.agent_id' => $_POST['agentid'],
                             'Loans.status' => 1
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $_POST['agentid'], 'Loans.Issue_date <=' => $date2,'Loans.status' => 1]]);
            }
//            
            /////////////// For Loans Report //////////////////
            /////////////// For Expenses Report //////////////////
            if (!empty($date1)) {
                $query2 = $this->DailyExpenses->find('all')->where([
                            'DailyExpenses.release_date BETWEEN :start AND :end',
                            'DailyExpenses.user_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $_POST['agentid'], 'DailyExpenses.release_date <=' => $date2]]);
            }
//            foreach ($query2 as $querys2) {
//                if ($verify == 'true')
//                    if ($querys2->verify == 0) {
//                        $verify = 'false';
//                    }
//            }
            /////////////// For Expenses Report //////////////////
            /////////////// For Bonuses Report //////////////////
            if (!empty($date1)) {
                $customerData = array();
                $customerName = array();

                $query3 = $this->Bonuses->find('all')->where([
                            'Bonuses.release_date BETWEEN :start AND :end',
                            'Bonuses.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                foreach ($query3 as $list) {
                    $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                    if (!empty($customerData[$list->id])) {
                        $customerName[$list->id] = $customerData[$list->id]->name;
                    } else {
                        $customerName[$list->id] = '';
                    }
                }
            } else {
                $customerData = array();
                $customerName = array();

                $query3 = $this->Bonuses->find('all', ['conditions' => ['Bonuses.agent_id' => $_POST['agentid'], 'Bonuses.release_date <=' => $date2]]);
                foreach ($query3 as $list) {
                    $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                    if (!empty($customerData[$list->id])) {
                        $customerName[$list->id] = $customerData[$list->id]->name;
                    } else {
                        $customerName[$list->id] = '';
                    }
                }
            }
            /////////////// For Bonuses Report //////////////////
            /////////////// For Money Collectd Report //////////////////
            if (!empty($date1)) {
                $query4 = $this->MoneyCollectedDetails->find('all')->where([
                            'MoneyCollectedDetails.collected_date BETWEEN :start AND :end',
                            'MoneyCollectedDetails.user_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $_POST['agentid'], 'MoneyCollectedDetails.collected_date <=' => $date2]]);
            }
//            foreach ($query4 as $querys4) {
//                if ($verify == 'true')
//                    if ($querys4->verify == 1) {
//                        $verify = 'false';
//                    }
//            }
            /////////////// For Money Collected Report //////////////////


            $users = $this->Reports->Users->find('list', ['limit' => 200]);
            $this->set('verify', $verify);
            $this->set(compact('report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'customerName'));
            $this->set('_serialize', ['report', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'customerName']);
        }
    }

    public function getlastreportdata() {
        $this->autoRender = false;
        $UsersData = $query = $this->Reports->find('all', [
                    'order' => ['Reports.created' => 'DESC']
                ])->where(['user_id' => $_POST["agentid"]]);
        $newarray = array();
        $row = $query->first();

    //    $profit = $this->getprofit($_POST["agentid"]);
        if (empty($row)) {
            $newarray = array('user_id' => '', 'carry_forward_amount' => '', 'genrate_to' => '', );
        } else {
            $genrate_to = $row->genrate_to->modify('+24 hours')->i18nFormat('dd/MM/yyyy');

            $newarray = array('user_id' => $row->user_id, 'carry_forward_amount' => $row->carry_forward_amount, 'genrate_to' => $genrate_to);
        }
        echo json_encode($newarray);
    }

    public function getprofit($id = null) { 
        $this->autoRender = false;
        if ($_POST['agentid'] == '-1') {
             if ($_POST['generatefrom'] == ''){ 
            $_POST['generatefrom'] = '2015/01/01' ;
        }
        $agentid = $_POST['agentid'];
        $datee1 = date('Y-m-d',strtotime(str_replace('/', '-', $_POST['generatefrom'])));
        $datee2 = date('Y-m-d',strtotime(str_replace('/', '-', $_POST['generateto'])));
        
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $this->loadModel('ReportAccbalances');
       
        $this->loadModel('AdvanceMoneyDetails');
       
        $this->loadModel('Loans');
       
        $this->loadModel('DailyExpenses');
        $this->loadModel('Customers');
     
        $this->loadModel('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
      
        $moneyCollectedData = $this->MoneyCollectedDetails->find('all')->where(['collected_date >='=>$datee1])->where(['collected_date <='=>$datee2]);

        $totalCollectedquery = $moneyCollectedData->select(['sum' => $moneyCollectedData->func()->sum('money_collected')])
                ->toArray();

        $totalCollectedAmount = $totalCollectedquery[0]->sum;


        $issuedLoanData = $this->Loans->find('all')->where(['status' => 1])->where(['Issue_date >='=>$datee1])->where(['Issue_date <='=>$datee2]);
        $totalIssuedLoanquery = $issuedLoanData->select(['sum' => $issuedLoanData->func()->sum('issued_loan')])
                ->toArray();

        $totalIssuedLoanAmount = $totalIssuedLoanquery[0]->sum;

        $expenseData = $this->DailyExpenses->find('all')->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalExpensequery = $expenseData->select(['sum' => $expenseData->func()->sum('expense')])
                ->toArray();

        $totalExpenseAmount = $totalExpensequery[0]->sum;

        $bonusData = $this->Bonuses->find('all')->where(['status' => 1])->where(['type' => 'in'])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalBonusquery = $bonusData->select(['sum' => $bonusData->func()->sum('bonus_amount')])
                ->toArray();

        $totalBounsAmount = $totalBonusquery[0]->sum;
      // echo $totalCollectedAmount.'-'.$totalBounsAmount.'-'.$totalExpenseAmount.'-'.$totalIssuedLoanAmount;exit;
       $totalProfit = (((int) $totalCollectedAmount + (int) $totalBounsAmount) -  (int)$totalExpenseAmount -  (int)$totalIssuedLoanAmount);
 
        echo $totalProfit;
            
        }else {
            if ($_POST['generatefrom'] == ''){ 
            $_POST['generatefrom'] = '2015/01/01' ;
        }
        $agentid = $_POST['agentid'];
        $datee1 = date('Y-m-d',strtotime(str_replace('/', '-', $_POST['generatefrom'])));
        $datee2 = date('Y-m-d',strtotime(str_replace('/', '-', $_POST['generateto'])));
        
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $this->loadModel('ReportAccbalances');
       
        $this->loadModel('AdvanceMoneyDetails');
       
        $this->loadModel('Loans');
       
        $this->loadModel('DailyExpenses');
        $this->loadModel('Customers');
     
        $this->loadModel('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
      
        $moneyCollectedData = $this->MoneyCollectedDetails->find('all')->where(['user_id' => $agentid])->where(['collected_date >='=>$datee1])->where(['collected_date <='=>$datee2]);

        $totalCollectedquery = $moneyCollectedData->select(['sum' => $moneyCollectedData->func()->sum('money_collected')])
                ->toArray();

        $totalCollectedAmount = $totalCollectedquery[0]->sum;


        $issuedLoanData = $this->Loans->find('all')->where(['agent_id' => $agentid])->where(['status' => 1])->where(['Issue_date >='=>$datee1])->where(['Issue_date <='=>$datee2]);
        $totalIssuedLoanquery = $issuedLoanData->select(['sum' => $issuedLoanData->func()->sum('issued_loan')])
                ->toArray();

        $totalIssuedLoanAmount = $totalIssuedLoanquery[0]->sum;

        $expenseData = $this->DailyExpenses->find('all')->where(['user_id' => $agentid])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalExpensequery = $expenseData->select(['sum' => $expenseData->func()->sum('expense')])
                ->toArray();

        $totalExpenseAmount = $totalExpensequery[0]->sum;

        $bonusData = $this->Bonuses->find('all')->where(['agent_id' => $agentid])->where(['status' => 1])->where(['type' => 'in'])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalBonusquery = $bonusData->select(['sum' => $bonusData->func()->sum('bonus_amount')])
                ->toArray();

        $totalBounsAmount = $totalBonusquery[0]->sum;
      // echo $totalCollectedAmount.'-'.$totalBounsAmount.'-'.$totalExpenseAmount.'-'.$totalIssuedLoanAmount;exit;
       $totalProfit = (((int) $totalCollectedAmount + (int) $totalBounsAmount) -  (int)$totalExpenseAmount -  (int)$totalIssuedLoanAmount);
 
        echo $totalProfit; 
        }
       
    }

    
      public function getprofitmonthly($id = null) { 
          
     if($id == '-1'){
         
         $date =   '01'.'-'.date("m").'-'.date("Y");           
         $_POST['generatefrom'] = date('Y-m-d',strtotime($date));
         $_POST['generateto'] = date("Y-m-t", strtotime($_POST['generatefrom']));  
                                 
        $datee1 = date('Y-m-d',strtotime($_POST['generatefrom']));
        $datee2 = date('Y-m-d',strtotime($_POST['generateto']));
        
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $this->loadModel('ReportAccbalances');
       
        $this->loadModel('AdvanceMoneyDetails');
       
        $this->loadModel('Loans');
       
        $this->loadModel('DailyExpenses');
        $this->loadModel('Customers');
     
        $this->loadModel('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
      
        $moneyCollectedData = $this->MoneyCollectedDetails->find('all')->where(['collected_date >='=>$datee1])->where(['collected_date <='=>$datee2]);

        $totalCollectedquery = $moneyCollectedData->select(['sum' => $moneyCollectedData->func()->sum('money_collected')])
                ->toArray();

        $totalCollectedAmount = $totalCollectedquery[0]->sum;


        $issuedLoanData = $this->Loans->find('all')->where(['status' => 1])->where(['Issue_date >='=>$datee1])->where(['Issue_date <='=>$datee2]);
        $totalIssuedLoanquery = $issuedLoanData->select(['sum' => $issuedLoanData->func()->sum('issued_loan')])
                ->toArray();

        $totalIssuedLoanAmount = $totalIssuedLoanquery[0]->sum;

        $expenseData = $this->DailyExpenses->find('all')->where(['status' => 1])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalExpensequery = $expenseData->select(['sum' => $expenseData->func()->sum('expense')])
                ->toArray();

        $totalExpenseAmount = $totalExpensequery[0]->sum;

        $bonusData = $this->Bonuses->find('all')->where(['status' => 1])->where(['type' => 'in'])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalBonusquery = $bonusData->select(['sum' => $bonusData->func()->sum('bonus_amount')])
                ->toArray();

        $totalBounsAmount = $totalBonusquery[0]->sum;
     //   echo $totalCollectedAmount.'-'.$totalBounsAmount.'-'.$totalExpenseAmount.'-'.$totalIssuedLoanAmount;exit;
        $totalProfit = (((int) $totalCollectedAmount + (int) $totalBounsAmount) - ( (int)$totalExpenseAmount +  (int)$totalIssuedLoanAmount));
 
          return  $totalProfit;
          
       }elseif ($_POST['agentid'] == '-1'){
        
           $date =   '01'.'-'.$_POST['month'].'-'.$_POST['year'];           
         $_POST['generatefrom'] = date('Y-m-d',strtotime($date));
         $_POST['generateto'] = date("Y-m-t", strtotime($_POST['generatefrom']));   
        $datee1 = date('Y-m-d',strtotime($_POST['generatefrom']));
        $datee2 = date('Y-m-d',strtotime($_POST['generateto']));
        
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $this->loadModel('ReportAccbalances');
       
        $this->loadModel('AdvanceMoneyDetails');
       
        $this->loadModel('Loans');
       
        $this->loadModel('DailyExpenses');
        $this->loadModel('Customers');
     
        $this->loadModel('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
      
        $moneyCollectedData = $this->MoneyCollectedDetails->find('all')->where(['collected_date >='=>$datee1])->where(['collected_date <='=>$datee2]);

        $totalCollectedquery = $moneyCollectedData->select(['sum' => $moneyCollectedData->func()->sum('money_collected')])
                ->toArray();

        $totalCollectedAmount = $totalCollectedquery[0]->sum;


        $issuedLoanData = $this->Loans->find('all')->where(['status' => 1])->where(['Issue_date >='=>$datee1])->where(['Issue_date <='=>$datee2]);
        $totalIssuedLoanquery = $issuedLoanData->select(['sum' => $issuedLoanData->func()->sum('issued_loan')])
                ->toArray();

        $totalIssuedLoanAmount = $totalIssuedLoanquery[0]->sum;

        $expenseData = $this->DailyExpenses->find('all')->where(['status' => 1])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalExpensequery = $expenseData->select(['sum' => $expenseData->func()->sum('expense')])
                ->toArray();

        $totalExpenseAmount = $totalExpensequery[0]->sum;

        $bonusData = $this->Bonuses->find('all')->where(['status' => 1])->where(['type' => 'in'])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalBonusquery = $bonusData->select(['sum' => $bonusData->func()->sum('bonus_amount')])
                ->toArray();

        $totalBounsAmount = $totalBonusquery[0]->sum;
      //  echo $totalCollectedAmount.'-'.$totalBounsAmount.'-'.$totalExpenseAmount.'-'.$totalIssuedLoanAmount;exit;
        $totalProfit = (((int) $totalCollectedAmount + (int) $totalBounsAmount) - ( (int)$totalExpenseAmount +  (int)$totalIssuedLoanAmount));
 
           echo $totalProfit;exit;
           
       }else {
        $this->autoRender = false;
        $date =   '01'.'-'.$_POST['month'].'-'.$_POST['year'];           
        $_POST['generatefrom'] = date('Y-m-d',strtotime($date));
        $_POST['generateto'] = date("Y-m-t", strtotime($_POST['generatefrom']));
        $agentid = $_POST['agentid'];
        $datee1 = date('Y-m-d',strtotime($_POST['generatefrom']));
        $datee2 = date('Y-m-d',strtotime($_POST['generateto']));
        
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $this->loadModel('ReportAccbalances');
       
        $this->loadModel('AdvanceMoneyDetails');
       
        $this->loadModel('Loans');
       
        $this->loadModel('DailyExpenses');
        $this->loadModel('Customers');
     
        $this->loadModel('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
      
        $moneyCollectedData = $this->MoneyCollectedDetails->find('all')->where(['user_id' => $agentid])->where(['collected_date >='=>$datee1])->where(['collected_date <='=>$datee2]);

        $totalCollectedquery = $moneyCollectedData->select(['sum' => $moneyCollectedData->func()->sum('money_collected')])
                ->toArray();

        $totalCollectedAmount = $totalCollectedquery[0]->sum;


        $issuedLoanData = $this->Loans->find('all')->where(['agent_id' => $agentid])->where(['status' => 1])->where(['Issue_date >='=>$datee1])->where(['Issue_date <='=>$datee2]);
        $totalIssuedLoanquery = $issuedLoanData->select(['sum' => $issuedLoanData->func()->sum('issued_loan')])
                ->toArray();

        $totalIssuedLoanAmount = $totalIssuedLoanquery[0]->sum;

        $expenseData = $this->DailyExpenses->find('all')->where(['user_id' => $agentid])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalExpensequery = $expenseData->select(['sum' => $expenseData->func()->sum('expense')])
                ->toArray();

        $totalExpenseAmount = $totalExpensequery[0]->sum;

        $bonusData = $this->Bonuses->find('all')->where(['agent_id' => $agentid])->where(['status' => 1])->where(['type' => 'in'])->where(['release_date >='=>$datee1])->where(['release_date <='=> $datee2]);
        $totalBonusquery = $bonusData->select(['sum' => $bonusData->func()->sum('bonus_amount')])
                ->toArray();

        $totalBounsAmount = $totalBonusquery[0]->sum;
      //  echo $totalCollectedAmount.'-'.$totalBounsAmount.'-'.$totalExpenseAmount.'-'.$totalIssuedLoanAmount;exit;
        $totalProfit = (((int) $totalCollectedAmount + (int) $totalBounsAmount) - ( (int)$totalExpenseAmount +  (int)$totalIssuedLoanAmount));
 
        echo $totalProfit;
       }
    }
    public function generatenewreport() {
        $verify = 'true';
        $user_id = $this->Auth->user('id');
        $this->loadModel('Users');
        TableRegistry::get('Users');
        $this->loadModel('ReportAccbalances');
        TableRegistry::get('ReportAccbalances');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        if ($this->request->is('ajax')) {

            $CurrentUser = $this->Auth->user('id');
            $postForm = $_POST;


            $status = $postForm['Report']['type'];
            $datee1 = str_replace('/', '-', $postForm['genrate_from']);
            if (!empty($datee1)) {
                $date1 = date_create($datee1)->format('Y-m-d');
            } else {
                $date1 = '';
            }
            $datee2 = str_replace('/', '-', $postForm['release_datee']);
            $date2 = date_create($datee2)->format('Y-m-d');
            $cfa = $postForm['newcfa'];
            $oldcfa = $postForm['oldcfa'];
            if ($oldcfa != '') {
                $oldcfa = $_POST['oldcfa'];
            } else {
                $oldcfa = 0;
            }
            $agentid = $postForm['agent_id'];

            $ReportsTable = TableRegistry::get('Reports');
            $ReportsTabledetail = $ReportsTable->newEntity();
            $ReportsTabledetail->user_id = $agentid;
            $ReportsTabledetail->status = $status;
            $ReportsTabledetail->generatedby = $user_id;
            //  $ReportsTabledetail->type = $postForm['type'];
            //  $ReportsTabledetail->customerin = $postForm['customerin'];
            // $ReportsTabledetail->agentout = $postForm['agentout'];
            $ReportsTabledetail->totalprofit = (int) $postForm['profit'];
            if (!empty($date1)) {
                $ReportsTabledetail->genrate_from = $date1;
            } else {
                $ReportsTabledetail->genrate_from = '';
            }

            $ReportsTabledetail->genrate_to = $date2;
            $ReportsTabledetail->carry_forward_amount = $cfa;
            $ReportsTabledetail->old_carry_forward_amount = $oldcfa;
            $ReportsTabledetail->last_update = $CurrentUser;

            if ($status == 1) { 
                if ($date1 != ''){
                   $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $agentid, 'Loans.Issue_date >=' => $date1, 'Loans.Issue_date <=' => $date2,'Loans.status' =>1]]);
  
                }else {
                   $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $agentid, 'Loans.Issue_date <=' => $date2,'Loans.status' =>1]]);
  
                }
               
                foreach ($query1 as $querys1) {
                    if ($verify == 'true')
                        if ($querys1->typ == 2) {
                            $verify = 'false';
                         
                        }
                }
                if ($date1 != ''){
                    $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $agentid, 'MoneyCollectedDetails.collected_date >=' => $date1, 'MoneyCollectedDetails.collected_date <=' => $date2]]);
                }else{
                   $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $agentid, 'MoneyCollectedDetails.collected_date <=' => $date2]]); 
                }  
                
                foreach ($query4 as $querys4) {
                    if ($verify == 'true')
                        if ($querys4->status != 1) {
                            $verify = 'false';
                        }
                }
                if ($date1 != ''){
                   $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $agentid, 'DailyExpenses.release_date >=' => $date1, 'DailyExpenses.release_date <=' => $date2]]);  
                }else{
                    $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $agentid, 'DailyExpenses.release_date <=' => $date2]]); 
                }
               
                foreach ($query2 as $querys2) {
                    if ($verify == 'true')
                        if ($querys2->verify == 0) {
                            $verify = 'false';
                        }
                }
            }
            if ($verify == 'false') {
                echo 'error';
                die();
            } else {

                if ($ReportsTable->save($ReportsTabledetail)) {
                    if (!empty($_POST['accbalid'])) {
                        $tableReportAccBal = TableRegistry::get('ReportAccbalances');
                        $i = 0;
                        foreach ($_POST['accbalid'] as $id) {
                            $datatable = $tableReportAccBal->get($id);
                            if (!empty($_POST['last'])) {
                               $datatable->title = $_POST['last'][$i]['title'];
                            $datatable->amount = $_POST['last'][$i]['amount']; 
                            }else {
                                $datatable->title = '';
                            $datatable->amount = 0;
                            }
                            
                            if ($tableReportAccBal->save($datatable)) {
                                
                            }
                        }
                    }
                    $Reportid = $ReportsTabledetail->id;

                    if (!empty($postForm['ReportAccbalance']) && $postForm['ReportAccbalance'][0]['title'] != "") {

                        $ReportAccbalanceTable = TableRegistry::get('ReportAccbalances');
                        $BalanceCount = $postForm['ReportAccbalance'];
                        $BalanceCountLoop = count(array_filter($BalanceCount));

                        $ReportAccbalance_arr = array_values($postForm['ReportAccbalance']);
                        for ($i = 0; $i < $BalanceCountLoop; $i++) {
                            $ReportAccbalance = $ReportAccbalanceTable->newEntity();
                            $ReportAccbalance->report_id = $Reportid;
                            if (!empty($ReportAccbalance_arr[$i]['title'])) {
                                $ReportAccbalance->title = $ReportAccbalance_arr[$i]['title'];
//                            print_r($postForm['ReportAccbalance'][$i]['title']);
//                            $LoanEmilist->updated_by = $created_id;
                            } else {
                                $ReportAccbalance->title = '';
                            }
                            if (!empty($ReportAccbalance_arr[$i]['amount'])) {
                                $ReportAccbalance->amount = $ReportAccbalance_arr[$i]['amount'];
                            } else {
                                $ReportAccbalance->amount = '';
                            }
//                        echo "<pre>"; 
//                        print_r($ReportAccbalance);exit;
//                        $LoanEmilist->last_update = $date = date('Y/m/d', time());
                            if ($ReportAccbalanceTable->save($ReportAccbalance)) {
                                
                            }
                        }
                    }
                    echo 'success';
                } else {
                    echo 'errors';
                }
                die();
            }
        }
    }

    public function regenerate() {
        $this->loadModel('Users');
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('ReportAccbalances');
        TableRegistry::get('ReportAccbalances');
        $UsersData = $this->Users->find('all', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1])->order(['Users.id' => 'ASC'])->limit(10)->toArray();
        $options = array();
        foreach ($UsersData as $UsersDatas) {
            $options[$UsersDatas->id]['value'] = $UsersDatas->name . ',' . $UsersDatas->user_profile->ic_number;
			$last_agent_id = $UsersDatas->id;
        }
        $report = $this->Reports->newEntity();
        if ($this->request->is('post')) {
            
            $report = $this->Reports->patchEntity($report, $this->request->data);
            if ($this->Reports->save($report)) {
                $this->Flash->success(__('The report has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The report could not be saved. Please, try again.'));
            }
        }
        $users = $this->Reports->Users->find('list', ['limit' => 200]);
        $this->set(compact('report', 'users', 'options', 'query', 'query1', 'query2', 'query3','last_agent_id'));
        $this->set('_serialize', ['report', 'options', 'query', 'query1', 'query2', 'query3']);
    }

    public function regeneratereportdata() {
            $this->loadModel('Customers');
        $this->viewBuilder()->layout('');
        $this->loadModel('Users');
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        $this->loadModel('ReportAccbalances');
        TableRegistry::get('ReportAccbalances');
        $reports = $this->ReportAccbalances->newEntity();
        /////////////// For Advance Money //////////////////
        $verify = 'true';
        if ($this->request->is('ajax')) {

            $fromdate = $_POST['generatefrom'];
            $todate = $_POST['generateto'];

            $datee1 = str_replace('/', '-', $fromdate);
            $datee2 = str_replace('/', '-', $todate);

            if (!empty($datee1)) {
                $date1 = date_create($datee1)->format('Y-m-d');
            } else {
                $date1 = '';
            }
            $date2 = date_create($datee2)->format('Y-m-d');
            $checktoData = $query = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $_POST['agentid']],
                        'order' => ['Reports.id' => 'DESC'], 'limit' => 1
                    ])->first();
            if (!empty($checktoData)) {
                $lastreporttodate = strtotime($checktoData->genrate_to->i18nFormat('yyyy-MM-dd'));
                $newtodate = strtotime($date2);
                if ($newtodate < $lastreporttodate) {
                    $this->set('error', "You can't regenerate this report because no report exist from this date.");
                }
            }
            $UsersData = $query = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $_POST['agentid'], 'Reports.genrate_from =' => $date1],
                'order' => ['Reports.id' => 'ASC'], 'limit' => 1
            ]);
            $newarray = array();
            $row = $query->first();
            if (!empty($row)) {
                $getcfa = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $_POST['agentid'], 'Reports.id <' => $row->id], 'order' => ['Reports.id' => 'DESC']])->first();
                if (!empty($getcfa)) {
                    $newarray = array('carry_forward_amount' => $getcfa->carry_forward_amount, 'error' => '');
                } else {
                    $newarray = array('carry_forward_amount' => 0, 'error' => '');
                }
            } else {
                $getfirstreport = $getcfa = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $_POST['agentid']]])->first();
                if (!empty($getfirstreport) && $getfirstreport->genrate_from == '') {
                    $newarray = array('carry_forward_amount' => 0, 'error' => '');
                } else {
                    $newarray = array('error' => "You can't regenerate this report because no report exist from this date.");
                }
            }
            if (!empty($fromdate)) {
                $query = $this->AdvanceMoneyDetails->find('all')->where([
                            'AdvanceMoneyDetails.issue_date BETWEEN :start AND :end',
                            'AdvanceMoneyDetails.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query = $this->AdvanceMoneyDetails->find('all', ['conditions' => ['AdvanceMoneyDetails.agent_id' => $_POST['agentid'], 'AdvanceMoneyDetails.issue_date <=' => $date2]]);
            }

            /////////////// For Advance Money //////////////////
            /////////////// For Loans Report //////////////////
            if (!empty($date1)) {
                $query1 = $this->Loans->find('all')->where([
                            'Loans.Issue_date BETWEEN :start AND :end',
                            'Loans.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $_POST['agentid'], 'Loans.Issue_date <=' => $date2]]);
            }
//            foreach ($query1 as $querys1) {
//                if ($verify == 'true')
//                    if ($querys1->typ == 2) {
//                        $verify = 'false';
//                    }
//            }
            /////////////// For Loans Report //////////////////
            /////////////// For Expenses Report //////////////////
            if (!empty($date1)) {
                $query2 = $this->DailyExpenses->find('all')->where([
                            'DailyExpenses.release_date BETWEEN :start AND :end',
                            'DailyExpenses.user_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $_POST['agentid'], 'DailyExpenses.release_date <=' => $date2]]);
            }
//            foreach ($query2 as $querys2) {
//                if ($verify == 'true')
//                    if ($querys2->verify == 0) {
//                        $verify = 'false';
//                    }
//            }
            /////////////// For Expenses Report //////////////////
            /////////////// For Bonuses Report //////////////////
            if (!empty($date1)) {
                $query3 = $this->Bonuses->find('all')->where([
                            'Bonuses.release_date BETWEEN :start AND :end',
                            'Bonuses.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query3 = $this->Bonuses->find('all', ['conditions' => ['Bonuses.agent_id' => $_POST['agentid'], 'Bonuses.release_date <=' => $date2]]);
            }  
            
             foreach ($query3 as $list) {
                    $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                    if (!empty($customerData[$list->id])) {
                        $customerName[$list->id] = $customerData[$list->id]->name;
                    } else {
                        $customerName[$list->id] = '';
                    }
                }

            /////////////// For Bonuses Report //////////////////
            /////////////// For Money Collectd Report //////////////////
            if (!empty($date1)) {
                $query4 = $this->MoneyCollectedDetails->find('all')->where([
                            'MoneyCollectedDetails.collected_date BETWEEN :start AND :end',
                            'MoneyCollectedDetails.user_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
            } else {
                $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $_POST['agentid'], 'MoneyCollectedDetails.collected_date <=' => $date2]]);
            }
//            foreach ($query4 as $querys4) {
//                if ($verify == 'true')
//                    if ($querys2->verify == 0) {
//                        $verify = 'false';
//                    }
//            }
            /////////////// For Money Collected Report //////////////////
            $users = $this->Reports->Users->find('list', ['limit' => 200]);
            $this->set('verify', $verify);
            $this->set(compact('newarray', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'reports','customerName'));
            $this->set('_serialize', ['newarray', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'reports','customerName']);
        }
    }

    public function getlastregeneratereportdata() {
        $this->autoRender = false;

        $fromdate = $_POST['generatefrom'];
        $datee1 = str_replace('/', '-', $fromdate);

        $date1 = date_create($datee1)->format('Y-m-d');
        $UsersData = $query = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $_POST['agentid'], 'Reports.genrate_from =' => $date1],
            'order' => ['Reports.id' => 'ASC'], 'limit' => 1
        ]);
        $newarray = array();
        $row = $query->first();
        if (!empty($row)) {
            $getcfa = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $_POST['agentid'], 'Reports.id <' => $row->id], 'order' => ['Reports.id' => 'DESC']])->first();
            if (!empty($getcfa)) {
                $newarray = array('carry_forward_amount' => $getcfa->carry_forward_amount, 'error' => '', 'from' => 'yes');
            } else {
                $newarray = array('carry_forward_amount' => 0, 'error' => '', 'from' => 'yes');
            }
        } else {
            $getfirstreport = $getcfa = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $_POST['agentid']]])->first();
            if (!empty($getfirstreport) && $getfirstreport->genrate_from == '') {
                $newarray = array('carry_forward_amount' => 0, 'error' => '', 'from' => '');
            } else {
                $newarray = array('error' => "You can't regenerate this report because no report exist from this date.");
            }
        }
        echo json_encode($newarray);
    }

    public function regeneratenewreport() {

        $verify = 'true';
        $this->loadModel('Users');
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        $this->loadModel('ReportAccbalances');
        TableRegistry::get('ReportAccbalances');
        if ($this->request->is('ajax')) {
            $CurrentUser = $this->Auth->user('id');
            $todate = $_POST['generateto'];
            $fromdate = $_POST['generatefrom'];
            $datee1 = str_replace('/', '-', $fromdate);
            $datee2 = str_replace('/', '-', $todate);
            if (!empty($datee1)) {
                $date1 = date_create($datee1)->format('Y-m-d');
            } else {
                $date1 = '';
            }
            $date2 = date_create($datee2)->format('Y-m-d');
            $cfa = $_POST['cfa'];
            $oldcfa = $_POST['oldcfa'];
            $agentid = $_POST['agentid'];
            $status = $_POST['status'];
            $profit = $_POST['profit']; 
               
            /////////// To  check the data is verified or not /////////////
            if ($status == 1) {
                $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $agentid, 'Loans.Issue_date <=' => $date2]]);

                foreach ($query1 as $querys1) {
                    if ($verify == 'true')
                        if ($querys1->typ == 2) {
                            $verify = 'false'; 
                              
                        }
                }

                $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $agentid, 'MoneyCollectedDetails.collected_date <=' => $date2]]);
                
                foreach ($query4 as $querys4) {
                    if ($verify == 'true')
                        if ($querys4->status == 0) {
                            $verify = 'false';
                         
                        }
                }

                $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $agentid, 'DailyExpenses.release_date <=' => $date2]]);
                foreach ($query2 as $querys2) {
                    if ($verify == 'true')
                        if ($querys2->verify == 0) {
                            $verify = 'false';
                            
                        }
                }
            }

            /////////// To  check the data is verified or not /////////////

            if ($verify == 'false') {
                echo 'error';
                die();
            } else {
                if (!empty($fromdate)) {
                    $UsersData = $query = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $agentid, 'Reports.genrate_from >=' => $date1, 'Reports.genrate_to <=' => $date2],
                        'order' => ['Reports.id' => 'ASC']
                    ]);
                } else {
                    $UsersData = $query = $this->Reports->find('all', ['conditions' => ['Reports.user_id' => $agentid],
                        'order' => ['Reports.id' => 'ASC']
                    ]);
                }
                foreach ($UsersData as $UsersDatas) {
                    $report = $this->Reports->get($UsersDatas->id);
                    $getreportacc = $this->ReportAccbalances->find('all', ['conditions' => ['ReportAccbalances.report_id =' => $report->id]]);
                    foreach ($getreportacc as $getreportaccs) {
                        $reportaccs = $this->ReportAccbalances->get($getreportaccs->id);
                        $this->ReportAccbalances->delete($reportaccs);
                    }
                    $this->Reports->delete($report);
                }
                $ReportsTable = TableRegistry::get('Reports');
                $ReportsTabledetail = $ReportsTable->newEntity();
                $ReportsTabledetail->user_id = $agentid;
                if (!empty($date1)) {
                    $ReportsTabledetail->genrate_from = $date1;
                } else {
                    $ReportsTabledetail->genrate_from = '';
                }
                $ReportsTabledetail->genrate_to = $date2;
                $ReportsTabledetail->carry_forward_amount = $cfa;
                $ReportsTabledetail->old_carry_forward_amount = $oldcfa;
                $ReportsTabledetail->status = $status;
                $ReportsTabledetail->last_update = $CurrentUser;
                $ReportsTabledetail->generatedby = $CurrentUser;
                  $ReportsTabledetail->totalprofit = $profit;
                if ($ReportsTable->save($ReportsTabledetail)) {

                    $Reportid = $ReportsTabledetail->id;

                    $postForm = $_POST;
                    if (!empty($postForm['ReportAccbalance']) && $postForm['ReportAccbalance'][0]['title'] != "") {

                        $ReportAccbalanceTable = TableRegistry::get('ReportAccbalances');
                        $BalanceCount = $postForm['ReportAccbalance'];
                        $BalanceCountLoop = count(array_filter($BalanceCount));
                        $ReportAccbalance_arr = array_values($postForm['ReportAccbalance']);
                        for ($i = 0; $i < $BalanceCountLoop; $i++) {
                            $ReportAccbalance = $ReportAccbalanceTable->newEntity();
                            $ReportAccbalance->report_id = $Reportid;
                            if (!empty($ReportAccbalance_arr[$i]['title'])) {
                                $ReportAccbalance->title = $ReportAccbalance_arr[$i]['title'];
                            } else {
                                $ReportAccbalance->title = '';
                            }
                            if (!empty($ReportAccbalance_arr[$i]['amount'])) {
                                $ReportAccbalance->amount = $ReportAccbalance_arr[$i]['amount'];
                            } else {
                                $ReportAccbalance->amount = '';
                            }
                            if ($ReportAccbalanceTable->save($ReportAccbalance)) {
                                
                            }
                        }
                    }


                    echo 'success';
                } else {
                    echo 'errors';
                }
            }
            die();
        }
    }

    public function monthlyreport() {

        $this->loadModel('AdvanceMoneyDetails');
        $this->loadModel('Customers');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        $users = $this->Reports->Users;
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $carry_farward = $this->Reports->find('all');
        $carryfarwordamount = 0;
        $customerData = array();
        $customerName = array();
        foreach ($carry_farward as $list) {
            $carryfarwordamount += $list->carry_forward_amount;
        }

        $UsersData = $this->Users->find('all', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1])->order(['Users.id' => 'ASC'])->limit(10)->toArray();
        $AgentData = $this->Users->find('list', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1, 'Users.id' => $user['id']])->toArray();
        $options = array();
       // $options = array('-1' => 'All Agent');
        foreach ($UsersData as $UsersDatas) {
            $options[$UsersDatas->id]['value'] = $UsersDatas->name . ',' . $UsersDatas->user_profile->ic_number;
			$last_agent_id = $UsersDatas->id;
        }
        $month = date('m');
        $year = date('Y');
        /////// for Advance money monthly Report ///////
        $query = $this->AdvanceMoneyDetails->find('all', [
            'conditions' => [
                'MONTH(issue_date)' => $month,
                'YEAR(issue_date)' => $year
            ],
                ]
        );
        /////// for Advance money monthly Report ///////
        /////// for Loans monthly Report ///////
        $query1 = $this->Loans->find('all', [
            'conditions' => [
                'MONTH(issue_date)' => $month,
                'YEAR(issue_date)' => $year
            ],
                ]
        );
        /////// for Loans monthly Report ///////
        /////// for Daily Expenses monthly Report ///////
        $query2 = $this->DailyExpenses->find('all', [
            'conditions' => [
                'MONTH(release_date)' => $month,
                'YEAR(release_date)' => $year
            ],
                ]
        );
        /////// for Daily Expenses monthly Report ///////
        /////// for Bonuses monthly Report ///////
        $query3 = $this->Bonuses->find('all', [
            'conditions' => [
                'MONTH(release_date)' => $month,
                'YEAR(release_date)' => $year
            ],
                ]
        );


        foreach ($query3 as $list) {
            $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
            if (!empty($customerData[$list->id])) {
                $customerName[$list->id] = $customerData[$list->id]->name;
            } else {
                $customerName[$list->id] = '';
            }
        }

        /////// for Bonuses monthly Report ///////
        /////// for Bonuses monthly Report ///////
        $query4 = $this->MoneyCollectedDetails->find('all', [
            'conditions' => [
                'MONTH(collected_date)' => $month,
                'YEAR(collected_date)' => $year
            ],
                ]
        );
        /////// for Bonuses monthly Report ///////
       $netprofit =  $this->getprofitmonthly($id = '-1');
          

        $this->set(compact('options', 'query', 'users', 'query1', 'query2', 'query3', 'query4', 'AgentData', 'carryfarwordamount','customerName','netprofit','last_agent_id'));
        $this->set('_serialize', ['options', 'query', 'users', 'query1', 'query2', 'query3', 'query4', 'AgentData', 'carryfarwordamount','customerName','netprofit']);
    }

    public function getmonthlyreportdata() { 
     
           $this->loadModel('Customers');
        $this->viewBuilder()->layout('');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        $customerData = array();
        $customerName = array();
        if ($this->request->is('post')) {
            if ($_POST["agentid"] != -1) {
                $carryfw = $this->Reports->find('all')->where(['user_id' => $_POST["agentid"]])->first();

                /////// for Advance money monthly Report ///////
                $query = $this->AdvanceMoneyDetails->find('all', [
                    'conditions' => [
                        'AdvanceMoneyDetails.agent_id ' => $_POST["agentid"],
                        'MONTH(issue_date)' => $_POST["month"],
                        'YEAR(issue_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Advance money monthly Report ///////
                /////// for Loans monthly Report ///////
                $query1 = $this->Loans->find('all', [
                    'conditions' => [
                        'Loans.agent_id ' => $_POST["agentid"],
                        'MONTH(issue_date)' => $_POST["month"],
                        'YEAR(issue_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Loans monthly Report ///////
                /////// for Daily Expenses monthly Report ///////
                $query2 = $this->DailyExpenses->find('all', [
                    'conditions' => [
                        'DailyExpenses.user_id ' => $_POST["agentid"],
                        'MONTH(release_date)' => $_POST["month"],
                        'YEAR(release_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Daily Expenses monthly Report ///////
                /////// for Bonuses monthly Report ///////
                $query3 = $this->Bonuses->find('all', [
                    'conditions' => [
                        'Bonuses.agent_id ' => $_POST["agentid"],
                        'MONTH(release_date)' => $_POST["month"],
                        'YEAR(release_date)' => $_POST["year"]
                    ],
                        ]
                );
                 
                foreach ($query3 as $list) {
                    $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                    if (!empty($customerData[$list->id])) {
                        $customerName[$list->id] = $customerData[$list->id]->name;
                    } else {
                        $customerName[$list->id] = '';
                    }
                }
              

                /////// for Bonuses monthly Report ///////
                /////// for Money Collected Report ///////
                $query4 = $this->MoneyCollectedDetails->find('all', [
                    'conditions' => [
                        'MoneyCollectedDetails.user_id ' => $_POST["agentid"],
                        'MONTH(collected_date)' => $_POST["month"],
                        'YEAR(collected_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Money Collected Report ///////
//            $report = $this->Reports->get($id);



                $this->set(compact('query', 'query1', 'query2', 'query3', 'query4', 'carryfw','customerName'));
                $this->set('_serialize', ['query', 'query1', 'query2', 'query3', 'query4', 'carryfw','customerName']);
            } else {
                $carryfw = $this->Reports->find('all');
                $carryforwordamount = 0;
                foreach ($carryfw as $list) {
                    $carryforwordamount += $list->carry_forward_amount;
                }
                /////// for Advance money monthly Report ///////
                $query = $this->AdvanceMoneyDetails->find('all', [
                    'conditions' => [
                        'MONTH(issue_date)' => $_POST["month"],
                        'YEAR(issue_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Advance money monthly Report ///////
                /////// for Loans monthly Report ///////
                $query1 = $this->Loans->find('all', [
                    'conditions' => [
                        'MONTH(issue_date)' => $_POST["month"],
                        'YEAR(issue_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Loans monthly Report ///////
                /////// for Daily Expenses monthly Report ///////
                $query2 = $this->DailyExpenses->find('all', [
                    'conditions' => [
                        'MONTH(release_date)' => $_POST["month"],
                        'YEAR(release_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Daily Expenses monthly Report ///////
                /////// for Bonuses monthly Report ///////
                $query3 = $this->Bonuses->find('all', [
                    'conditions' => [
                        'MONTH(release_date)' => $_POST["month"],
                        'YEAR(release_date)' => $_POST["year"]
                    ],
                        ]
                );
                
                 foreach ($query3 as $list) {
                    $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                    if (!empty($customerData[$list->id])) {
                        $customerName[$list->id] = $customerData[$list->id]->name;
                    } else {
                        $customerName[$list->id] = '';
                    }
                }
                   
                /////// for Bonuses monthly Report ///////
                /////// for Money Collected Report ///////
                $query4 = $this->MoneyCollectedDetails->find('all', [
                    'conditions' => [
                        'MONTH(collected_date)' => $_POST["month"],
                        'YEAR(collected_date)' => $_POST["year"]
                    ],
                        ]
                );
                /////// for Money Collected Report ///////
//            $report = $this->Reports->get($id);



                $this->set(compact('query', 'query1', 'query2', 'query3', 'query4', 'carryforwordamount','customerName'));
                $this->set('_serialize', ['query', 'query1', 'query2', 'query3', 'query4', 'carryforwordamount','customerName']);
            }
        }
    }

    public function customreport() {

        $users = $this->Reports->Users;
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $UsersData = $this->Users->find('all', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1])->order(['Users.id' => 'ASC'])->limit(10)->toArray();
        //$options = array('-1' => 'All Agent');
        $AgentData = $this->Users->find('list', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1, 'Users.id' => $user['id']])->toArray();
        foreach ($UsersData as $UsersDatas) {
            $options[$UsersDatas->id]['value'] = $UsersDatas->name . ',' . $UsersDatas->user_profile->ic_number;
			$last_agent_id = $UsersDatas->id;
        }
        if ($this->request->is('ajax')) {
            
        }
        $this->set(compact('options', 'AgentData','last_agent_id'));
        $this->set('_serialize', ['options', '$AgentData']);
    }

    public function customreportdat() {
         $this->loadModel('Customers');
        $this->viewBuilder()->layout('');
        $this->loadModel('Users');
        $users = $this->Reports->Users;
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');

        /////////////// For Advance Money //////////////////
        if ($this->request->is('ajax')) {

            $datee1 = str_replace('/', '-', $_POST['datefrom']);
            $datee2 = str_replace('/', '-', $_POST['dateto']);
            $date1 = date_create($datee1)->format('Y-m-d');
            $date2 = date_create($datee2)->format('Y-m-d');
            if ($_POST["agentid"] != -1) { 
                $carryfw = $query = $this->Reports->find('all')->where(['user_id' => $_POST["agentid"]])->first();
                $query = $this->AdvanceMoneyDetails->find('all')->where([
                            'AdvanceMoneyDetails.issue_date BETWEEN :start AND :end',
                            'AdvanceMoneyDetails.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');

                /////////////// For Advance Money //////////////////
                /////////////// For Loans Report //////////////////
                $query1 = $this->Loans->find('all')->where([
                            'Loans.Issue_date BETWEEN :start AND :end',
                            'Loans.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                /////////////// For Loans Report //////////////////
                /////////////// For Expenses Report //////////////////
                $query2 = $this->DailyExpenses->find('all')->where([
                            'DailyExpenses.release_date BETWEEN :start AND :end',
                            'DailyExpenses.user_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                /////////////// For Expenses Report //////////////////
                /////////////// For Bonuses Report //////////////////
                $customerName = array();
                $customerData = array();
                $query3 = $this->Bonuses->find('all')->where([
                            'Bonuses.release_date BETWEEN :start AND :end',
                            'Bonuses.agent_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                
                  foreach ($query3 as $list) {
                    $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                    if (!empty($customerData[$list->id])) {
                        $customerName[$list->id] = $customerData[$list->id]->name;
                    } else {
                        $customerName[$list->id] = '';
                    }
                }
                /////////////// For Bonuses Report //////////////////
                /////////////// For Bonuses Report //////////////////
                $query4 = $this->MoneyCollectedDetails->find('all')->where([
                            'MoneyCollectedDetails.collected_date BETWEEN :start AND :end',
                            'MoneyCollectedDetails.user_id' => $_POST['agentid']
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                /////////////// For Bonuses Report //////////////////
                $this->set(compact('report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'carryfw'));
                $this->set('_serialize', ['report', 'options', 'query', 'query1', 'query2', 'query3', 'query4'], 'carryfw');
            } else {
                $carryfw = $query = $this->Reports->find('all');
                $carryforwordamount = 0;
                foreach ($carryfw as $list) {
                    $carryforwordamount += $list->carry_forward_amount;
                }
                $query = $this->AdvanceMoneyDetails->find('all')->where([
                            'AdvanceMoneyDetails.issue_date BETWEEN :start AND :end'
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');

                /////////////// For Advance Money //////////////////
                /////////////// For Loans Report //////////////////
                $query1 = $this->Loans->find('all')->where([
                            'Loans.Issue_date BETWEEN :start AND :end'
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                /////////////// For Loans Report //////////////////
                /////////////// For Expenses Report //////////////////
                $query2 = $this->DailyExpenses->find('all')->where([
                            'DailyExpenses.release_date BETWEEN :start AND :end'
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                /////////////// For Expenses Report //////////////////
                /////////////// For Bonuses Report //////////////////
                    $customerName = array();
                $customerData = array();
                $query3 = $this->Bonuses->find('all')->where([
                            'Bonuses.release_date BETWEEN :start AND :end'
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                 foreach ($query3 as $list) {
                    $customerData[$list->id] = $this->Customers->find('all')->where(['id' => $list->customerin])->first();
                    if (!empty($customerData[$list->id])) {
                        $customerName[$list->id] = $customerData[$list->id]->name;
                    } else {
                        $customerName[$list->id] = '';
                    }
                }
                /////////////// For Bonuses Report //////////////////
                /////////////// For Bonuses Report //////////////////
                $query4 = $this->MoneyCollectedDetails->find('all')->where([
                            'MoneyCollectedDetails.collected_date BETWEEN :start AND :end'
                        ])
                        ->bind(':start', new \DateTime($date1), 'date')
                        ->bind(':end', new \DateTime($date2), 'date');
                /////////////// For Bonuses Report //////////////////
                $sample_arr = 'yes';
                $this->set('sample_arr', $sample_arr);
              
            } 
              $this->set(compact('report', 'users', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'carryforwordamount','customerName'));
                $this->set('_serialize', ['report', 'options', 'query', 'query1', 'query2', 'query3', 'query4', 'carryforwordamount','customerName']);
        }
    }

    function getagentlastreportdata() {
        $this->autoRender = false;
        $UsersData = $query = $this->Reports->find('all', [
                    'order' => ['Reports.genrate_to' => 'DESC']
                ])->where(['user_id' => $_POST["id"]]);
        $newarray = array();
        $row = $query->first();

        if (!empty($row)) {

            $genrate_to = $row->genrate_to->i18nFormat('yyyy-MM-dd');
            $newarray = array('last_generate_to' => $genrate_to);
        } else {
            $newarray = array('last_generate_to' => 'fromstart');
        }
        echo json_encode($newarray);
    }

    function deletereports() {
        $this->loadModel('ReportAccbalances');
        TableRegistry::get('ReportAccbalances');
        if ($this->request->is('ajax')) {
            if (isset($_POST['reportid'])) {
                $reportid = $_POST['reportid'];
                $agentid = $_POST['agentid'];
//                print_r($reportid);die();
                $getreports = $this->Reports->find('all', ['conditions' => ['Reports.id >=' => $reportid, 'Reports.user_id' => $agentid]]);
//                $del = 'no';
                foreach ($getreports as $getreport) {
                    $report = $this->Reports->get($getreport->id);
                    $getreportacc = $this->ReportAccbalances->find('all', ['conditions' => ['ReportAccbalances.report_id =' => $getreport->id]]);
                    foreach ($getreportacc as $getreportaccs) {
                        $reportaccs = $this->ReportAccbalances->get($getreportaccs->id);
                        $this->ReportAccbalances->delete($reportaccs);
                    }
                    if ($this->Reports->delete($report)) {
                        $del = 'yes';
                    }
                }
            } else {
                $del = 'no';
            }
            echo $del;
            die();
        }
    }

    public function getreports() {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $from = $_POST['fromdate'];
            $to = $_POST['todate'];
            $this->request->session()->write('reports.to', $to);
            $this->request->session()->write('reports.from', $from);
        }
//        $user = $this->Auth->user();
//        $group_id = $user['group_id'];
//        $this->viewBuilder()->layout('');
//        $this->paginate = [
//            'contain' => ['Users']
//        ];
//        $users = $this->Reports->Users;
//
//        $datee1 = str_replace('/', '-', $_POST['fromdate']);
//        $datee2 = str_replace('/', '-', $_POST['todate']);
//
//        $newDate = date('Y-m-d', strtotime($datee2 . " + 1 days"));
//        $date1 = date_create($datee1)->format('Y-m-d');
//        if($group_id == 3)
//        {
//               $query = $this->Reports->find('all')->where([
//
//                    'generatedby'=>$user["id"],'Reports.created BETWEEN :start AND :end'
//                ])
//                ->bind(':start', new \DateTime($date1), 'date')
//                ->bind(':end', new \DateTime($newDate), 'date');
//        }
//        else {
//        $query = $this->Reports->find('all')->where([
//
//                    'Reports.created BETWEEN :start AND :end'
//                ])
//                ->bind(':start', new \DateTime($date1), 'date')
//                ->bind(':end', new \DateTime($newDate), 'date');
//        }
//
//        $this->set(compact('users'));
//        $this->set('_serialize', ['users']);
//        $this->set('reports', $this->paginate($query));
//        $this->set('_serialize', ['reports']);
//
//
//        $this->set(
//                'reportslist', $this->Reports->find('list')
//        );
    }

    public function updateverifystatus() {

        $this->viewBuilder()->layout(false);
        $this->autoRender = false;
        TableRegistry::get('Users');
        $this->loadModel('AdvanceMoneyDetails');
        TableRegistry::get('AdvanceMoneyDetails');
        $this->loadModel('Loans');
        TableRegistry::get('Loans');
        $this->loadModel('DailyExpenses');
        TableRegistry::get('DailyExpenses');
        $this->loadModel('Bonuses');
        TableRegistry::get('Bonuses');
        $this->loadModel('MoneyCollectedDetails');
        TableRegistry::get('MoneyCollectedDetails');
        $curr_id = $this->Auth->user('id');
        $verify = 'true';
        if ($this->request->is('ajax')) {
            if ($this->request->is(['patch', 'post', 'put'])) {
                $date2 = $this->request->data['todate'];
                $agentid = $this->request->data['agentid'];
                $status = $this->request->data['verify'];
//                print_r($agentid);die;
                if ($status == 1) {
                    $query1 = $this->Loans->find('all', ['conditions' => ['Loans.agent_id' => $agentid, 'Loans.Issue_date <=' => $date2]]);

                    foreach ($query1 as $querys1) {
                        if ($verify == 'true')
                            if ($querys1->typ == 2) {
                                $verify = 'false';
                            }
                    }

                    $query4 = $this->MoneyCollectedDetails->find('all', ['conditions' => ['MoneyCollectedDetails.user_id' => $agentid, 'MoneyCollectedDetails.collected_date <=' => $date2]]);
                    foreach ($query4 as $querys4) {
                        if ($verify == 'true')
                            if ($querys4->verify == 1) {
                                $verify = 'false';
                            }
                    }

                    $query2 = $this->DailyExpenses->find('all', ['conditions' => ['DailyExpenses.user_id' => $agentid, 'DailyExpenses.release_date <=' => $date2]]);
                    foreach ($query2 as $querys2) {
                        if ($verify == 'true')
                            if ($querys2->verify == 0) {
                                $verify = 'false';
                            }
                    }
                }
                if ($verify == 'false') {
                    echo 'error';
                    die();
                } else {
                    $id = $this->request->data['id'];
                    $reports = TableRegistry::get('Reports');
                    $query = $reports->query();
                    $query->update()
                            ->set(['status' => $_POST['verify'], 'last_update' => $curr_id])
                            ->where(['id' => $id])
                            ->execute();
                    $result = 'true';
                    $this->set(compact('result'));
                    $this->set('_serialize', ['result']);
                    echo $result;
                }
            }
        }
    }

    public function updatereportdata() {
        $this->viewBuilder()->layout(false);
        $this->autoRender = false;
        $user = $this->Auth->user();
//        echo "<pre>";
//        print_r($_POST);
//        die();
        $id = $_POST["ReportAccbalanc"]["report_id"];
        //    if ($this->request->is('ajax')) {
        $ReportsTable = TableRegistry::get('Reports');
        $query = $ReportsTable->query();
        $query->update()
                ->set(['last_update' => $user["id"]])
                ->where(['id' => $id])
                ->execute();
//        $ReportsTable->last_update = $user["id"];

        if ($this->request->is(['patch', 'post', 'put'])) {
            $postForm = $_POST;
            //              die("raghav");
            //             echo "<pre>";
            //             print_r($this->request->data['ReportAccbalance']);
            //               exit;
//            $user = $this->Users->patchEntity($user, $this->request->data);
//            if ($userq = $this->Users->save($user)) {
            $this->loadModel('ReportAccbalances');


            $ReportAccbalanceTable = TableRegistry::get('ReportAccbalances');
            foreach ($postForm['ReportAccbalance'] as $ReportAccbalanceData) {
                if (!empty($ReportAccbalanceData['id'])) {
                    $ReportAccdetail = $ReportAccbalanceTable->get($ReportAccbalanceData['id']);
                    $ReportAccdetail->report_id = $ReportAccbalanceData['report_id'];
                    $ReportAccdetail->id = $ReportAccbalanceData['id'];
                    $ReportAccdetail->title = $ReportAccbalanceData['title'];
                    $ReportAccdetail->amount = $ReportAccbalanceData['amount'];
                    $ReportAccbalanceTable->save($ReportAccdetail);
                } else {
                    $ReportAccdetail = $ReportAccbalanceTable->newEntity();
                    $ReportAccdetail->report_id = $postForm['ReportAccbalanc']['report_id'];
                    $ReportAccdetail->title = $ReportAccbalanceData['title'];
                    $ReportAccdetail->amount = $ReportAccbalanceData['amount'];
                    $ReportAccbalanceTable->save($ReportAccdetail);
                }
            }
            $result = 'yes';
            echo $result;


        }
    }

    public function deleteaccdetails() {
        $this->viewBuilder()->layout(false);
        $this->autoRender = false;
        $this->loadModel('ReportAccbalances');
        TableRegistry::get('ReportAccbalances');
        $this->request->allowMethod(['post', 'delete']);
        $accid = $this->request->data["accis"];
        $accdata = $this->ReportAccbalances->get($accid);
        if ($this->ReportAccbalances->delete($accdata)) {
            echo "yes";
        } else {
            echo "no";
        }
    }

}
