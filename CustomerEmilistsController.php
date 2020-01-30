<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * CustomerEmilists Controller
 *
 * @property \App\Model\Table\CustomerEmilistsTable $CustomerEmilists
 */
class CustomerEmilistsController extends AppController {

    /**
     * Index method
     *
     * @return void
     */
    public function index() {
        $this->paginate = [
            'contain' => ['Customers']
        ];
        $this->set('customerEmilists', $this->paginate($this->CustomerEmilists));
        $this->set('_serialize', ['customerEmilists']);
    }

    /**
     * View method
     *
     * @param string|null $id Customer Emilist id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null) {
        $customerEmilist = $this->CustomerEmilists->get($id, [
            'contain' => ['Customers']
        ]);
        $this->set('customerEmilist', $customerEmilist);
        $this->set('_serialize', ['customerEmilist']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $customerEmilist = $this->CustomerEmilists->newEntity();
        if ($this->request->is('post')) {
            $customerEmilist = $this->CustomerEmilists->patchEntity($customerEmilist, $this->request->data);
            if ($this->CustomerEmilists->save($customerEmilist)) {
                $this->Flash->success(__('The customer emilist has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The customer emilist could not be saved. Please, try again.'));
            }
        }
        $customers = $this->CustomerEmilists->Customers->find('list', ['limit' => 200]);
        $this->set(compact('customerEmilist', 'customers'));
        $this->set('_serialize', ['customerEmilist']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Customer Emilist id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $customerEmilist = $this->CustomerEmilists->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $customerEmilist = $this->CustomerEmilists->patchEntity($customerEmilist, $this->request->data);
            if ($this->CustomerEmilists->save($customerEmilist)) {
                $this->Flash->success(__('The customer emilist has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The customer emilist could not be saved. Please, try again.'));
            }
        }
        $customers = $this->CustomerEmilists->Customers->find('list', ['limit' => 200]);
        $this->set(compact('customerEmilist', 'customers'));
        $this->set('_serialize', ['customerEmilist']);
    }

/*** show filter **/

 

    /**
     * Delete method
     *
     * @param string|null $id Customer Emilist id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $customerEmilist = $this->CustomerEmilists->get($id);
        if ($this->CustomerEmilists->delete($customerEmilist)) {
            $this->Flash->success(__('The customer emilist has been deleted.'));
        } else {
            $this->Flash->error(__('The customer emilist could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function changeStatus($id = null, $status = null, $emi_amt = null) {
        
        $this->viewBuilder()->layout('');
        $this->autoRender = false;
        if (isset($_POST['id']) && isset($_POST['status']) && isset($_POST['emi_amt'])) {
            $customer_emi = TableRegistry::get('CustomerEmilists');
            $lid = $customer_emi->find('all')->where(['id' => $_POST['id']])->toArray();
           $loan_id = $lid[0]->loan_id;
            $customer_loans = TableRegistry::get('CustomerLoans');
            $all = $customer_loans->find('all')->where(['id'=> $loan_id])->toArray();
           $ret_amt = $all[0]->pending_amt;
           
          
          $s = $_POST['status'];
          if($s == 1){
          
                  $date = date('y-m-d');
                     $ret_amt = $ret_amt-$_POST['emi_amt'];
                    $query = $customer_loans->query();
                    $query->update()
                    ->set(['pending_amt' => $ret_amt])
                    ->where(['id' => $loan_id])
                    ->execute();
                    if($query){
                         $query = $customer_emi->query();
                            $query->update()
                                    ->set(['status' => $_POST['status'],'last_update_date' => $date])
                                    ->where(['id' => $_POST['id']])
                                    ->execute();
                    
                                        if($query){
                                                 echo "success";
                                        }else{
                                                 "Error in updating status";
                                        }
                                }
                                else
            {
                echo "false";
            }
             }
             else
             {
                         $date = date('y-m-d');
                   
            $ret_amt = $ret_amt + $_POST['emi_amt'];
           
           
            $query = $customer_loans->query();
            $query->update()
            ->set(['pending_amt' => $ret_amt])
            ->where(['id' => $loan_id])
            ->execute();
            if($query){
                 $query = $customer_emi->query();
                    $query->update()
                            ->set(['status' => $_POST['status'],'last_update_date' => $date])
                            ->where(['id' => $_POST['id']])
                            ->execute();
            
                                if($query){
                                         echo "success";
                                }else{
                                         "Error in updating status";
                                }
             
             }
             else
            {
                echo "false";
            }
            }
            
            
            
           /*
        }*/
    

}
}
}
