<?php

namespace App\Controller;
use Cake\Datasource\ConnectionManager;
use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;


/**
 * AdvanceMoneyDetails Controller
 *
 * @property \App\Model\Table\AdvanceMoneyDetailsTable $AdvanceMoneyDetails
 */
class AdvanceMoneyDetailsController extends AppController {

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        // $curr_id = $this->Auth->user('id');
        // $g_id = $this->Auth->user('group_id');
        // $this->loadModel('Users');
        // TableRegistry::get('Users');
        // if ($g_id == 1 || $g_id == 2) {
            // $this->paginate = [
                // 'contain' => ['Users']
            // ];
        // }
        // if ($g_id == 3) {
            // $this->paginate = ["join" => array(
                    // 'Users' => [
                        // 'table' => 'users',
                        // 'type' => 'inner',
                        // 'conditions' => 'Users.id=AdvanceMoneyDetails.agent_id'
                    // ]
                // ),
                // 'conditions' => ["or" => ['AdvanceMoneyDetails.agent_id' => $curr_id]]
            // ];
        // }
        // $advanceMoneyDetails = $this->paginate($this->AdvanceMoneyDetails);
        // $users = $this->AdvanceMoneyDetails->Users;
        // $this->set(compact('advanceMoneyDetails', 'users'));
        // $this->set('_serialize', ['advanceMoneyDetails', 'users']);
    }
	
	public function advancemoneydata() {
		$this->viewBuilder()->layout('ajax');
        $this->autoRender = false;
		$curr_id = $this->Auth->user('id');
        $g_id = $this->Auth->user('group_id');
        $this->loadModel('Users');
        TableRegistry::get('Users');
		$this->loadModel('user_profile');
        TableRegistry::get('user_profile');
		//$advanceMoneyDetails = $this->paginate($this->AdvanceMoneyDetails);
        $users = $this->AdvanceMoneyDetails->Users;
		$totalRecords = 0;
		
		if ($_GET['iSortCol_0'] == 0) {
            $sortcoloum = 'Users.name';
        } else if ($_GET['iSortCol_0'] == 1) {
            $sortcoloum = 'user_profiles.ic_number';
        } else if ($_GET['iSortCol_0'] == 2) {
            $sortcoloum = 'AdvanceMoneyDetails.advance_money';
        } else if ($_GET['iSortCol_0'] == 3) {
            $sortcoloum = 'AdvanceMoneyDetails.issue_date';
        } 
		else if ($_GET['iSortCol_0'] == 4) {
            $sortcoloum = 'Users_d.name';
        }else{
            $sortcoloum = 'AdvanceMoneyDetails.status';
        } 
		$sortvalue = $_GET["sSortDir_0"];
		
		$sLimit = "";
        if ($_GET['iDisplayLength'] != '-1') {
            $sLimit = intval($_GET['iDisplayLength']);
        }
        $page = ($_GET['iDisplayStart'] == 0) ? 1 : (($_GET['iDisplayStart'] / $_GET['iDisplayLength']) + 1);
       if ($g_id == 1 || $g_id == 2) {
            $this->paginate = [
                'contain' => ['Users']
            ];
        }
		if ($g_id == 3) {
			
		if(!empty($_GET['sSearch']) &&  !empty($_GET['datefrom'])){
			if (!empty($_GET['datefrom'])) {
				$datef = str_replace("/", "-", $_GET['datefrom']);
				$date1 = date("Y-m-d", strtotime($datef));
				} else {
                    $date1 = '';
                }
				
                $datet = str_replace("/", "-", $_GET['dateto']);
				$date2 = date("Y-m-d", strtotime($datet));	
				
		    $replacedate = str_replace('/', '-', $_GET['sSearch'] );
			$dateissuesearch =   date('Y-m-d', strtotime($replacedate));
			$conn = ConnectionManager::get('default');
			$rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
			INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
			LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
			LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
			LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update 
			WHERE AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.agent_id = '".$curr_id."' AND AdvanceMoneyDetails.issue_date >= '".$date1."' AND AdvanceMoneyDetails.issue_date <= '".$date2."' AND (Users.name like '%".$_GET['sSearch']."%' OR
			user_profiles.ic_number like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.advance_money like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.issue_date like '%".$dateissuesearch ."%' OR Users_d.name like '%".$_GET['sSearch']."%' OR
			user_profiles_d.ic_number like '%".$_GET['sSearch']."%'  OR AdvanceMoneyDetails.status like '%".$_GET['sSearch']."%')
			GROUP BY agent_id 
			ORDER BY ".$sortcoloum." desc 
			LIMIT 30 OFFSET 0");
            $MoneyDetails = $rs ->fetchAll('assoc');
		    $totalRecords = count($MoneyDetails);	
				
		}		
				
			
		elseif (!empty($_GET['sSearch'])) {
			$replacedate = str_replace('/', '-', $_GET['sSearch'] );
			$dateissuesearch =   date('Y-m-d', strtotime($replacedate));
            $conn = ConnectionManager::get('default');
			$rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
			INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
			LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
			LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
			LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update WHERE AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.agent_id = '".$curr_id."' AND (Users.name like '%".$_GET['sSearch']."%' OR
			user_profiles.ic_number like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.advance_money like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.issue_date like '%".$dateissuesearch."%' OR Users_d.name like '%".$_GET['sSearch']."%' OR
			user_profiles_d.ic_number like '%".$_GET['sSearch']."%'  OR AdvanceMoneyDetails.status like '%".$_GET['sSearch']."%') GROUP BY agent_id 
			ORDER BY ".$sortcoloum." desc 
			LIMIT 30 OFFSET 0");
			
            $MoneyDetails = $rs ->fetchAll('assoc');
		    $totalRecords = count($MoneyDetails);
			}
			
			elseif (!empty($_GET['datefrom'])) {
                if (!empty($_GET['datefrom'])) {
				$datef = str_replace("/", "-", $_GET['datefrom']);
				$date1 = date("Y-m-d", strtotime($datef));
				} else {
                    $date1 = '';
                }
				
                $datet = str_replace("/", "-", $_GET['dateto']);
				$date2 = date("Y-m-d", strtotime($datet));
				$conn = ConnectionManager::get('default');
				$rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
				INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
				LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
			    LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
			    LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update 
				WHERE AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.agent_id = '".$curr_id."' AND AdvanceMoneyDetails.issue_date >= '".$date1."' AND AdvanceMoneyDetails.issue_date <= '".$date2."' 
			    GROUP BY agent_id 
				ORDER BY ".$sortcoloum." desc 
				LIMIT 30 OFFSET 0");
				
			    $MoneyDetails = $rs ->fetchAll('assoc');
				$totalRecords = count($MoneyDetails);
				
			}
			
			else{
			$currentDateTime = date('Y-m-d H:i:s');
		    $date2 = date("Y-m-d", strtotime($currentDateTime));
            $date1 = date("Y-m-d", strtotime('-30 days')); 
			$conn = ConnectionManager::get('default');
			 $rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
			 INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
			 LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
			 LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
			 LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update 
			 WHERE AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.agent_id = '".$curr_id."' AND AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.issue_date >= '".$date1."' AND AdvanceMoneyDetails.issue_date <= '".$date2."' 
			 GROUP BY agent_id 
			 ORDER BY ".$sortcoloum." desc 
			 LIMIT 30 OFFSET 0");
			
            $MoneyDetails = $rs ->fetchAll('assoc');
			$totalRecords = count($MoneyDetails);
            }	
		
			
        }
		else{
			if(!empty($_GET['sSearch']) &&  !empty($_GET['datefrom'])){
			if (!empty($_GET['datefrom'])) {
				$datef = str_replace("/", "-", $_GET['datefrom']);
				$date1 = date("Y-m-d", strtotime($datef));
				} else {
                    $date1 = '';
                }
				
                $datet = str_replace("/", "-", $_GET['dateto']);
				$date2 = date("Y-m-d", strtotime($datet));	
			$replacedate = str_replace('/', '-', $_GET['sSearch'] );
			$dateissuesearch =   date('Y-m-d', strtotime($replacedate));
			$conn = ConnectionManager::get('default');
			$rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
			INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
			LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
			LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
			LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update 
			WHERE AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.issue_date >= '".$date1."' AND AdvanceMoneyDetails.issue_date <= '".$date2."' AND (Users.name like '%".$_GET['sSearch']."%' OR
			user_profiles.ic_number like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.advance_money like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.issue_date like '%".$dateissuesearch."%' OR Users_d.name like '%".$_GET['sSearch']."%' OR
			user_profiles_d.ic_number like '%".$_GET['sSearch']."%'  OR AdvanceMoneyDetails.status like '%".$_GET['sSearch']."%')
			GROUP BY agent_id 
			ORDER BY ".$sortcoloum." desc 
			LIMIT 30 OFFSET 0");
            $MoneyDetails = $rs ->fetchAll('assoc');
		    $totalRecords = count($MoneyDetails);	
				
		    }		
			
			
			elseif (!empty($_GET['sSearch'])) {
			$replacedate = str_replace('/', '-', $_GET['sSearch'] );
			$dateissuesearch =   date('Y-m-d', strtotime($replacedate));
		    $conn = ConnectionManager::get('default');
		    $rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
			INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
			LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
			LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
			LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update
			WHERE AdvanceMoneyDetails.status = 1 AND Users.name like '%".$_GET['sSearch']."%' OR
			user_profiles.ic_number like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.advance_money like '%".$_GET['sSearch']."%' OR AdvanceMoneyDetails.issue_date like '%".$dateissuesearch."%' OR Users_d.name like '%".$_GET['sSearch']."%' OR
			user_profiles_d.ic_number like '%".$_GET['sSearch']."%'  OR AdvanceMoneyDetails.status like '%".$_GET['sSearch']."%' GROUP BY agent_id 
			ORDER BY ".$sortcoloum." desc 
			LIMIT 30 OFFSET 0");
            $MoneyDetails = $rs ->fetchAll('assoc');
		    $totalRecords = count($MoneyDetails);
			}
			
			elseif (!empty($_GET['datefrom'])) {
                if (!empty($_GET['datefrom'])) {
				$datef = str_replace("/", "-", $_GET['datefrom']);
				$date1 = date("Y-m-d", strtotime($datef));
				} else {
                    $date1 = '';
                }
				
                $datet = str_replace("/", "-", $_GET['dateto']);
				$date2 = date("Y-m-d", strtotime($datet));
				$conn = ConnectionManager::get('default');
				$rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
				INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
				LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
				LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
				LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update 
				WHERE AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.issue_date >= '".$date1."' AND AdvanceMoneyDetails.issue_date <= '".$date2."' 
			    GROUP BY agent_id 
				ORDER BY ".$sortcoloum." desc 
				LIMIT 30 OFFSET 0");
				
			    $MoneyDetails = $rs ->fetchAll('assoc');
				$totalRecords = count($MoneyDetails);
			}
			
			else{
			$currentDateTime = date('Y-m-d H:i:s');
		    $date2 = date("Y-m-d", strtotime($currentDateTime));
            $date1 = date("Y-m-d", strtotime('-30 days')); 		
			
			 $conn = ConnectionManager::get('default');
			 $rs = $conn->query("SELECT * FROM advance_money_details AdvanceMoneyDetails 
			 INNER JOIN users Users ON Users.id = AdvanceMoneyDetails.agent_id
			 LEFT JOIN users Users_d ON Users_d.id = AdvanceMoneyDetails.last_update
			 LEFT JOIN user_profiles ON user_profiles.user_id = AdvanceMoneyDetails.agent_id
			 LEFT JOIN user_profiles user_profiles_d ON user_profiles_d.user_id = AdvanceMoneyDetails.last_update 
			 WHERE AdvanceMoneyDetails.status = 1 AND AdvanceMoneyDetails.issue_date >= '".$date1."' AND AdvanceMoneyDetails.issue_date <= '".$date2."' 
			 GROUP BY agent_id 
			 ORDER BY ".$sortcoloum." desc 
			 LIMIT 30 OFFSET 0");
			
			
            $MoneyDetails = $rs ->fetchAll('assoc');
			$totalRecords = count($MoneyDetails);
            }
           
		}
		$i = 0;
        $data = array();
		
        foreach ($MoneyDetails as $MoneyDetail):
		
		if(isset($MoneyDetail->agent_id))
		{
			$agent = $this->Users->find()->where(array('Users.id' => $MoneyDetail->agent_id))->contain(['UserProfiles'])->first();
			$data['data'][$i][] = $agent->name;
			if(!isset($agent->user_profile)){
				$data['data'][$i][] = 'NA';
			}else{
				$data['data'][$i][] = $agent->user_profile->ic_number;
			}
            $data['data'][$i][] = $MoneyDetail->advance_money;
			$createduser = $this->Users->find()->where(array('Users.id'=> $MoneyDetail->last_update))->contain(['UserProfiles'])->first();
			$date_i = str_replace("-", "/",$MoneyDetail->issue_date);
		    $issue_date = date("d/m/Y", strtotime($date_i));
			$data['data'][$i][] = $issue_date;
			$data['data'][$i][] = $createduser->name.','.$createduser->user_profile->ic_number;
            $data['data'][$i][] = $MoneyDetail->status;
        }
		else{
			
			$agent = $this->Users->find()->where(array('Users.id' => $MoneyDetail['agent_id']))->contain(['UserProfiles'])->first();
			$data['data'][$i][] = $agent->name;
			if(!isset($agent->user_profile)){
				$data['data'][$i][] = 'NA';
			}else{
				$data['data'][$i][] = $agent->user_profile->ic_number;
			}
            $data['data'][$i][] = $MoneyDetail['advance_money'];
			$createduser = $this->Users->find()->where(array('Users.id'=> $MoneyDetail['last_update']))->contain(['UserProfiles'])->first();
		    $date_i = str_replace("-", "/",$MoneyDetail['issue_date']);
		    $issue_date = date("d/m/Y", strtotime($date_i));
			$data['data'][$i][] = $issue_date;
			$data['data'][$i][] = $createduser->name.','.$createduser->user_profile->ic_number;
            $data['data'][$i][] = $MoneyDetail['status'];
	    }
		$i++;
        endforeach;
		if (!empty($data)) {
            if ($totalRecords == 0) {
                $totalRecords = $i;
            }
            $data["iTotalRecords"] = intval($totalRecords);
            $data["iTotalDisplayRecords"] = intval($totalRecords);
            echo json_encode($data);
        } else {
            echo '{

                "iTotalRecords": "0",
                "iTotalDisplayRecords": "0",
                "aaData": []
            }';
        }
       
        $this->set(compact('advanceMoneyDetails', 'users'));
        $this->set('_serialize', ['advanceMoneyDetails', 'users']);
    }
	/**
     * View method
     *
     * @param string|null $id Advance Money Detail id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $advanceMoneyDetail = $this->AdvanceMoneyDetails->get($id, [
            'contain' => ['Users']
        ]);
        $users = $this->AdvanceMoneyDetails->Users;
        $this->set(compact('advanceMoneyDetails', 'users'));
        $this->set('advanceMoneyDetail', $advanceMoneyDetail);
        $this->set('_serialize', ['advanceMoneyDetail', 'users']);
		
		
		
		
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
        $this->loadModel('Users');
        $UsersData = $this->Users->find('all', ['contain' => 'UserProfiles'])->where(['group_id' => 3, 'status' => 1])->order(['Users.id' => 'ASC'])->limit(10)->toArray();
        $options = array();
        foreach ($UsersData as $UsersDatas) {
            $options[$UsersDatas->id]['value'] = $UsersDatas->name . ',' . $UsersDatas->user_profile->ic_number;
			$last_agent_id = $UsersDatas->id;
        }


        $advanceMoneyDetail = $this->AdvanceMoneyDetails->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['AdvanceMoneyDetail']['user_id'] = $created_id;
            $this->request->data['AdvanceMoneyDetail']['last_update'] = $created_id;
            $advanceMoneyDetail->issue_datee = str_replace('/', '-', $this->request->data['AdvanceMoneyDetail']['issue_datee']);
            $advanceMoneyDetail->issue_date = date_create($advanceMoneyDetail->issue_datee)->format('Y-m-d');
            $advanceMoneyDetail = $this->AdvanceMoneyDetails->patchEntity($advanceMoneyDetail, $this->request->data['AdvanceMoneyDetail']);
            if ($this->AdvanceMoneyDetails->save($advanceMoneyDetail)) {
                $this->Flash->success(__('The advance money detail has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The advance money detail could not be saved. Please, try again.'));
            }
        }
        $users = $this->AdvanceMoneyDetails->Users->find('list', ['limit' => 200]);
        $this->set(compact('advanceMoneyDetail', 'users', 'options','last_agent_id'));
        $this->set('_serialize', ['advanceMoneyDetail', 'options']);
    }
	public function getagents() {
		$user = $this->Auth->user();
        $group_id = $user['group_id'];
        $created_id = $user["id"];
		$last_agnt_id = $_POST['last_agnt_id'];
        $this->loadModel('Users');
        $UsersData = $this->Users->find('all', ['contain' => 'UserProfiles'])->where(['Users.id >'=>$last_agnt_id,'group_id' => 3, 'status' => 1])->order(['Users.id' => 'ASC'])->limit(10)->toArray();
        $options = array();
        foreach ($UsersData as $UsersDatas) {
            $options[$UsersDatas->id]['value'] = $UsersDatas->name . ',' . $UsersDatas->user_profile->ic_number;
			$last_agent_id = $UsersDatas->id;
        }


        $advanceMoneyDetail = $this->AdvanceMoneyDetails->newEntity();
       
        $users = $this->AdvanceMoneyDetails->Users->find('list', ['limit' => 200]);
		
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
		 }
        $this->set(compact('advanceMoneyDetail', 'users', 'options','last_agent_id'));
        $this->set('_serialize', ['advanceMoneyDetail', 'options']);
	
	}
    /**
     * Edit method
     *
     * @param string|null $id Advance Money Detail id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $advanceMoneyDetail = $this->AdvanceMoneyDetails->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $advanceMoneyDetail = $this->AdvanceMoneyDetails->patchEntity($advanceMoneyDetail, $this->request->data);
            if ($this->AdvanceMoneyDetails->save($advanceMoneyDetail)) {
                $this->Flash->success(__('The advance money detail has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The advance money detail could not be saved. Please, try again.'));
            }
        }
        $users = $this->AdvanceMoneyDetails->Users->find('list', ['limit' => 200]);
        $this->set(compact('advanceMoneyDetail', 'users'));
        $this->set('_serialize', ['advanceMoneyDetail']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Advance Money Detail id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $advanceMoneyDetail = $this->AdvanceMoneyDetails->get($id);
        if ($this->AdvanceMoneyDetails->delete($advanceMoneyDetail)) {
            $this->Flash->success(__('The advance money detail has been deleted.'));
        } else {
            $this->Flash->error(__('The advance money detail could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
//// update loan status starts here ///////////////
    public function updatedeletestatus() {
        $this->viewBuilder()->layout(false);
        $user = $this->Auth->user();

        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            if ($this->request->is(['patch', 'post', 'put'])) {
                $id = $this->request->data['id'];
                $users = TableRegistry::get('AdvanceMoneyDetails');
                $query = $users->query();
                $query->update()
                        ->set(['status' => 0, 'last_update' => $user["id"]])
                        ->where(['id' => $id])
                        ->execute();
            }
            $result = 'true';
            $this->set(compact('result'));
            $this->set('_serialize', ['result']);
            echo $result;
        }
    }
//// update loan ends here ///////////////
    public function getdata() {
          $this->autoRender = false ;
     if ($this->request->is('ajax')) {          
            $to =  $_POST['todate'];
            $from =  $_POST['fromdate'];
            $this->request->session()->write('advancemoney.to', $to);
            $this->request->session()->write('advancemoney.from', $from);
         
     }
//        $this->viewBuilder()->layout('');
//
//        $curr_id = $this->Auth->user('id');
//        $g_id = $this->Auth->user('group_id');
//        $this->loadModel('Users');
//        $datee1 = str_replace('/', '-', $_POST['fromdate']);
//        $datee2 = str_replace('/', '-', $_POST['todate']);
//        $date1 = date_create($datee1)->format('Y-m-d');
//        $date2 = date_create($datee2)->format('Y-m-d');
//        TableRegistry::get('Users');
//        if ($g_id == 1 || $g_id == 2) {
//            $advanceMoneyDetails = $this->AdvanceMoneyDetails->find('all')->where(['AdvanceMoneyDetails.issue_date BETWEEN :start AND :end'])
//                    ->bind(':start', new \DateTime($date1), 'date')
//                    ->bind(':end', new \DateTime($date2), 'date');
//        }
//        if ($g_id == 3) {
//            $advanceMoneyDetails = $this->AdvanceMoneyDetails->find('all'
//                            , ["join" => array(
//                            'Users' => [
//                                'table' => 'users',
//                                'type' => 'inner',
//                                'conditions' => 'Users.id=AdvanceMoneyDetails.agent_id'
//                            ]
//                        ),
//                        'conditions' => ["or" => ['AdvanceMoneyDetails.agent_id' => $curr_id, 'Users.superior_id' => $curr_id]]
//                    ])->where(['AdvanceMoneyDetails.issue_date BETWEEN :start AND :end'])
//                    ->bind(':start', new \DateTime($date1), 'date')
//                    ->bind(':end', new \DateTime($date2), 'date');
//        }
//        $users = $this->AdvanceMoneyDetails->Users;
//        $this->set(compact('advanceMoneyDetails', 'users'));
//        $this->set('_serialize', ['advanceMoneyDetails', 'users']);
    }

}
