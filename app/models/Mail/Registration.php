<?php


class Model_Mail_Registration extends Model_Mail
{
    /**
     * @var Model_User
     */
    protected $_user;
    
    
    /**
    * @param Model_User $user
    * @return Model_Mail_Registration
    */
    public function setUser(Model_User $user)
    {
        $this->_user = $user;
        return $this;
    }
    
    /**
    * @return Model_User
    */
    public function getUser()
    {
        return $this->_user;
    }
    
    public function send($transport = null)
    {
        $controllerRouter = Zend_Controller_Front::getInstance()->getRouter();
        $this->setSubject('Inscription au service d\'alerte leboncoin.fr');
        $this->addTo($this->_user->getEmail());
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $this->setBodyText('Bonjour,
Votre inscription a bien été prise en compte. Afin de finaliser celle-ci, vous devez valider votre compte en cliquant sur le lien suivant :
http://'.$_SERVER["HTTP_HOST"].$controllerRouter->assemble(array(
    'module' => 'api',     'controller' => 'inscription',
    'action' => 'valider', 'key' => $this->_user->getValidationKey()), 'default', true).'

Rappel de vos identifiants :
Login : '.$this->_user->getEmail().'
Mot de passe : '.$this->_user->getPassword().'

Alerte LeBonCoin');
        
        return parent::send($transport);
    }
}
