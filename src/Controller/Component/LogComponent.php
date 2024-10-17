<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

class LogComponent extends Component
{
    public function save($acao, $data, $model = 'Logs') {
        if(!$this->request->session()->read('Auth.User.id')) {
            return false;
        }

        $logModel = TableRegistry::get($model);
        $log = $logModel->patchEntity($logModel->newEntity(), [
            'colaboradores_id' => $this->request->session()->read('Auth.User.id'),
            'data_hora' => new \DateTime('now'),
            'log' => json_encode($data),
            'acao' => $acao
        ]);

        if(isset($data['id'])) {
            $log['registro_id'] = $data['id'];
        }

        if(isset($this->request->params['controller'])) {
            $log['tabela'] = $this->getTable();
        }

        $log['ip'] = $this->get_client_ip_env();
        return $logModel->save($log);
    }

    public function getTable()
    {
        $arr = preg_split('/(?=[A-Z])/',lcfirst($this->request->params['controller']));
        return strtolower(implode('_', $arr));
    }

    public function get_client_ip_env() {
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
      else
        $ipaddress = 'UNKNOWN';

      return $ipaddress;
    }

}
