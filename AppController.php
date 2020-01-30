<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\I18n;



/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function initialize() {
        $this->loadComponent('Flash');
        
	
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ]
        ]);
    }

    public function beforeFilter(Event $event) {
        //I18n::locale('en_US');
        $session = $this->request->session();
        /*parent::beforeFilter($event);*/
         $lang = $session->read('Config.language');
        /*if (empty($lang)) {
        return;
        }*/
        if ($session->check('Config.language')) {
        $lang = $session->read('Config.language');
        
        if($lang == "en")
        {
                $this->set('cul','english');
        }
        if($lang == "ch")
        {
                
                I18n::locale('zh_CN');
                 $this->set('cul','chinese');
        }
      
    
} 
        $this->Auth->allow(['login','forgetpwd','changeLang']);
        $user = $this->Auth->user();
        $group_id = $user['group_id'];
        switch ($group_id) {
            case '3':
                break;
            case '2':
                break;
            case'1':
                $this->Auth->allow('all');
                break;
            default :
                break;
        }
        $this->set('user', $user);
        $this->set('group_id', $group_id);
        $this->set('current_user', $user);
    }
    
    public function changeLang($lang = null)
{
    I18n::locale($lang);
    return $this->redirect($this->request->referer());
}

}
