<?php
/**
 * Install Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
App::uses('AppController', 'Controller');
class InstallController extends Controller {
    public $name = 'Install';
    public $uses = array();
    public $components = array('Session');
    public $helpers = array('Layout', 'Html', 'Form');
    private $__defaultDbConfig = array(
        'name' => 'default',
        'datasource'=> 'Database/Mysql',
        'persistent'=> false,
        'host'=> 'localhost',
        'login'=> 'root',
        'password'=> '',
        'database'=> 'quickapps',
        'schema'=> null,
        'prefix'=> 'qa_',
        'encoding' => 'UTF8',
        'port' => '3306'
    );    
    
    public function beforeFilter() {
        $this->viewClass = 'View';
        $this->layout    = 'install';

        # already installed ?
        if (file_exists(APP . DS . 'Config' . DS . 'database.php') &&  
             file_exists(APP . DS . 'Config' . DS . 'install') )
            $this->redirect('/');
    }
    
    public function index() {
       $this->redirect('/install/license');
    }
    
    /* Step 1: License agreement */
    public function license() {
        if (isset($this->data['License'])) {
            $this->__stepSuccess('license');
            $this->redirect('/install/server_test');
        }
    }

    /* Step 2: Server test */
    public function server_test() {
        if (!$this->__stepSuccess('license', true) )
            $this->redirect('/install/license');
            
        if (!empty($this->data['Test'])) {
            $this->__stepSuccess('server_test');
            $this->redirect('/install/database');
        }
        
        $tests = array(
            'php' => array(
                'test' => version_compare(PHP_VERSION, '5.2', '>='),
                'msg'  => __t('Your php version is not suported. check that your version is 5.2 or newer.')
            ),
            'mysql' => array(
                'test' => (extension_loaded('mysql') || extension_loaded('mysqli')),
                'msg'  => __t('MySQL extension is not loaded on your server.')
            ),
            'no_safe_mode' => array(
                'test' => (ini_get('safe_mode') == false || ini_get('safe_mode') == '' || strtolower(ini_get('safe_mode')) == 'off'),
                'msg'  => __t('Your server has SafeMode on, please turn it off before continue.')
            ),
            'tmp_writable' => array(
                'test' => is_writable(APP . DS . 'tmp'),
                'msg'  => __t('APP/tmp folder is not writable')
            ),
            'cache_writable' => array(
                'test' => is_writable(APP . DS . 'tmp' . DS . 'cache'),
                'msg'  => __t('APP/tmp/cache folder is not writable')
            ),
            'installer_writable' => array(
                'test' => is_writable(APP . DS . 'tmp' . DS . 'cache' . DS . 'installer'),
                'msg'  => __t('APP/tmp/cache/installer folder is not writable')
            ),
            'models_writable' => array(
                'test' => is_writable(APP . DS . 'tmp' . DS . 'cache' . DS . 'models'),
                'msg'  => __t('APP/tmp/cache/models folder is not writable')
            ),
            'persistent_writable' => array(
                'test' => is_writable(APP . DS . 'tmp' . DS . 'cache' . DS . 'persistent'),
                'msg'  => __t('APP/tmp/cache/persistent folder is not writable')
            ),
            'i18n_writable' => array(
                'test' => is_writable(APP . DS . 'tmp' . DS . 'cache' . DS . 'i18n'),
                'msg'  => __t('APP/tmp/cache/i18n folder is not writable')
            ),
            'Config_writable' => array(
                'test' => is_writable(APP . DS . 'Config'),
                'msg'  => __t('APP/Config folder is not writable')
            ),
            'core.php_writable' => array(
                'test' => is_writable(APP . DS . 'Config'),
                'msg'  => __t('APP/Config/core.php file is not writable')
            )            
        );
        
        $results = array_unique(Set::extract('{s}.test', $tests));
        if (!(count($results) === 1 && $results[0] === true)) {
            $this->set('success', false);
            $this->set('tests', $tests);
        } else {
            $this->set('success', true);
        }
    }

    /* Step 3: Database  */
    public function database() {
        if (!$this->__stepSuccess(array('license', 'server_test'), true) )
            $this->redirect('/install/license');
    
        if (!empty($this->data['Database'])) {
            copy(APP . 'Config' . DS . 'database.php.install', APP . 'Config' . DS . 'database.php');
            App::import('Utility', 'File');
            $file = new File(APP . 'Config' . DS . 'database.php', true);
            $dbSettings = $file->read();
            
            $data = $this->data;
            $data['Database']['datasource'] = 'Database/Mysql';
            $data['Database']['persistent'] = 'false';
            
            $dbSettings = str_replace(
                array(
                    '{db_datasource}',
                    '{db_persistent}',
                    '{db_host}',
                    '{db_login}',
                    '{db_password}',
                    '{db_database}',
                    '{db_prefix}'
                ), 
                array(
                    $data['Database']['datasource'],
                    $data['Database']['persistent'],
                    $data['Database']['host'],
                    $data['Database']['login'],
                    $data['Database']['password'],
                    $data['Database']['database'],
                    $data['Database']['prefix']
                ),
                $dbSettings
            );
            
            $this->__defaultDbConfig = Set::merge($this->__defaultDbConfig, $data['Database']);
            if ($file->write($dbSettings)) {
            
                $MySQLConn = @mysql_connect($this->__defaultDbConfig['host'] . ':' . $this->__defaultDbConfig['port'], $this->__defaultDbConfig['login'], $this->__defaultDbConfig['password'], true);
                if (@mysql_select_db($this->__defaultDbConfig['database'], $MySQLConn)) {
                    @App::import('Model', 'ConnectionManager');
                    @ConnectionManager::create('default');
                    $db = ConnectionManager::getDataSource('default', $this->__defaultDbConfig);
                
                    $file =& new File(APP . 'Config' . DS . 'Schema' . DS . 'quickapps.sql');
                    $sql = $file->read();
                    $queries = $this->__splitSql($sql);
                    
                    # dump DB
                    foreach ($queries as $query)
                        if (!empty($query) && $query != "--")
                            $db->execute(str_replace('#__', $data['Database']['prefix'], $query));
                    
                    # random keys values
                    $file =& new File(APP . 'Config' . DS . 'core.php');
                    App::uses('Security', 'Utility');
                    App::load('Security');
                    $salt = Security::generateAuthKey();
                    $seed = mt_rand() . mt_rand();
                    $contents = $file->read();
                    $contents = preg_replace('/(?<=Configure::write\(\'Security.salt\', \')([^\' ]+)(?=\'\))/', $salt, $contents);
                    $contents = preg_replace('/(?<=Configure::write\(\'Security.cipherSeed\', \')(\d+)(?=\'\))/', $seed, $contents);
                    $file->write($contents);
                    
                    Cache::write('QaInstallDatabase', 'success'); # fix: Security keys change
                    //$this->__stepSuccess('database'); # unneded
                    $this->redirect('/install/user_account');
                } else {
                    $file->close();
                    unlink(APP . 'Config' . DS . 'database.php');
                    $this->Session->setFlash(__t('Could not connect to database.'), 'default', 'error');
                }
            } else {
                $this->Session->setFlash(__t('Could not write database.php file.'), 'default', 'error');
            }
        }
    }

    /* Step 4: User account */
    public function user_account() {
        if (
            Cache::read('QaInstallDatabase') == 'success' || 
            $this->__stepSuccess(array('license', 'server_test', 'database'), true)
       ) {
            $this->__stepSuccess('license');
            $this->__stepSuccess('server_test');
            $this->__stepSuccess('database');
            Cache::delete('QaInstallDatabase');
        } else {
            $this->redirect('/install/license');
        }
        
        if (isset($this->data['User'])) {
            $this->loadModel('User.User');
            $data = $this->data;
            $data['User']['status'] = 1;
            $data['Role']['Role'] = array(1);
            if ($this->User->save($data)) {
                $this->__stepSuccess('user_account');
                $this->redirect('/install/finish');
            } else {
                $errors = '';
                foreach ($this->User->invalidFields() as $field => $error)
                    $errors .= "<b>{$field}:</b> {$error}<br/>";
                $this->Session->setFlash(
                    '<b>' . __t('Could not create new user, please try again.') . "</b><br/>" . 
                    $errors
                , 'default', 'error');
            }
        }
    }
    
    /* Step 5: Finish */
    public function finish() {
        if (!$this->__stepSuccess(array('license', 'server_test', 'database', 'user_account'), true) )
            $this->redirect('/install/license');
            
        App::import('Utility', 'File');
        $file = new File(APP . 'Config' . DS . 'install', true);
        if ($file->write(time())) {
            $this->__stepSuccess('finish');
            $this->Session->delete('QaInstall');
            $this->redirect('/admin');
        } else {
            $this->Session->setFlash(__t("Could not write 'install' file. Check file/folder permissions and refresh this page"), 'default', 'error');
        }
    }
    
    private function __stepSuccess($step, $check = false) {
        if (!$check )
            return $this->Session->write("QaInstall.{$step}", 'success');
        if (is_array($step)) {
            foreach ($step as $s )
                if (!$this->Session->check("QaInstall.{$s}") )
                    return false;
            return true;
        } else {
            return $this->Session->check("QaInstall.{$step}");
        }
        return false;
    }
    
    private function __splitSql($sql) {
        $sql = trim($sql);
        $sql = ereg_replace("\n#[^\n]*\n", "\n", $sql);

        $buffer = array();
        $ret = array();
        $in_string = false;

        for($i=0; $i<strlen($sql)-1; $i++) {
            if ($sql[$i] == ";" && !$in_string) {
                $ret[] = substr($sql, 0, $i);
                $sql = substr($sql, $i + 1);
                $i = 0;
            }

            if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
                $in_string = false;
            }
            elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
                $in_string = $sql[$i];
            }
            if (isset($buffer[1]))
                $buffer[0] = $buffer[1];
            $buffer[1] = $sql[$i];
        }

        if (!empty($sql))
            $ret[] = $sql;
        return($ret);
    }    
}