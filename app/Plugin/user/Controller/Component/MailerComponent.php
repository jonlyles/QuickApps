<?php
/**
 * User Mailer Component
 *
 * PHP version 5
 *
 * @category User.Controller/Component
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class MailerComponent extends Component {
    var $Controller;
    var $components = array('Email');

    public function initialize(&$Controller) {
        $this->Controller =& $Controller;
        return true;
    }

    function send($user_id, $type){
        $user = is_numeric($user_id) ? ClassRegistry::init('User.User')->findById($user_id) : $user_id;
        if(!$user) return false;

        $variables = $this->mailVariables();
        if( isset($variables["user_mail_{$type}_body"]) && 
            isset($variables["user_mail_{$type}_subject"])
        ){
            if(isset($variables["user_mail_{$type}_notify"]) && !$variables["user_mail_{$type}_notify"])
                return;

            $this->Email->to        = $user['User']['email'];
            $this->Email->subject   = $this->parseVariables($user, $variables["user_mail_{$type}_subject"]);
            $this->Email->from      = Configure::read('Variable.site_name') . ' <' . Configure::read('Variable.site_mail') . '>';
            
            return $this->Email->send($this->parseVariables($user, $variables["user_mail_{$type}_body"]));
        }
    }

    function mailVariables(){
        $v = array();
        $variables = ClassRegistry::init('System.Variable')->find('all',
            array(
                'conditions' => array(
                    'Variable.name LIKE' => 'user_mail_%'
                )
            )
        );

        foreach($variables as $var)
            $v[$var['Variable']['name']] = $var['Variable']['value'];
        return $v;
    }

    function parseVariables($user, $text, $__hooktags = true){
        if(is_numeric($user))
            $user = ClassRegistry::init('User.User')->findById($user);
        if(!isset($user['User']) || empty($text)) return false;

        preg_match_all('/\[user_(.+)\]/iUs', $text, $userVars);
        foreach ($userVars[1] as $var){
            if (isset($user['User'][$var])){
                $text = str_replace("[user_{$var}]", $user['User'][$var], $text);
            } else {
                switch($var){
                    case 'activation_url':
                        $text = str_replace("[user_{$var}]", Router::url("/user/activate/{$user['User']['id']}/{$user['User']['key']}", true), $text);
                    break;
                    
                    case 'cancel_url':
                        $text = str_replace("[user_{$var}]", Router::url("/user/cancell/{$user['User']['id']}/{$user['User']['key']}", true), $text);
                    break;
                }
            }
        }

        preg_match_all('/\[site_(.+)\]/iUs', $text, $siteVars);
        foreach ( $userVars[1] as $var ){
            if($v = Configure::read("Variable.site_{$var}")){
                $text = str_replace("[site_{$var}]", $v, $text);
            } else {
                switch($var){
                    case 'url':
                        $text = str_replace("[site_{$var}]", Router::url("/", true), $text);
                    break;
                    
                    case 'login_url':
                        $text = str_replace("[site_{$var}]", Router::url("/user/login", true), $text);
                    break;
                }
            }
        }

        if ($__hooktags) $text = $this->Controller->Hook->hookTags($text);
        return $text;        
    }
}