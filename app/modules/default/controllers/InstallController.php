<?php

class InstallController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $configFile = APPLICATION_PATH."/configs/config.ini";
        if (file_exists($configFile)) {
            $this->view->criticalError = "Le fichier de configuration existe déjà.";
            return;
        }
        if (!is_writeable(dirname($configFile))) {
            $this->view->criticalError = "'".$configFile."' doit être accessible en écriture.";
            return;
        }
        $form = new Form_Install();
        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $formValues = $form->getValues();
            $configDb = array(
                "charset" => "utf8",
                "host" => $formValues["host"],
                "username" => $formValues["user"],
                "password" => $formValues["password"],
                "dbname" => $formValues["dbname"]
            );
            $adapter = Zend_Db::factory("Mysqli", $configDb);
            try {
                $adapter->getConnection();
            } catch (Zend_Db_Adapter_Mysqli_Exception $e) {
                $form->getElement("dbname")->setErrors(array($e->getMessage()));
            }
            
            if ($adapter->isConnected()) {
                Zend_Db_Table::setDefaultAdapter($adapter);
                $queries = explode(";", file_get_contents(APPLICATION_PATH."/../sql/schema.sql"));
                foreach ($queries AS $query) {
                    if (!$query = trim($query)) {
                        continue;
                    }
                    $adapter->query($query);
                }
                if (!empty($formValues["user_email"]) && !empty($formValues["user_password"])) {
                    $tb = new Zend_Db_Table("User");
                    $user = $tb->createRow(array(
                        "email" => $formValues["user_email"],
                        "password" => sha1($formValues["user_password"]),
                        "date_created" => new Zend_Db_Expr("NOW()")
                    ));
                    $user->save();
                }
                $config = new Zend_Config(array(
                    "resources" => array("db" => array("params" => array(
                        "host" => $formValues["host"],
                        "username" => $formValues["user"],
                        "password" => $formValues["password"],
                        "dbname" => $formValues["dbname"]
                    ))),
                    "email" => array("from" => isset($formValues["email"])?$formValues["email"]:""),
                    "key" => isset($formValues["key"])?$formValues["key"]:""
                ));
                $writer = new Zend_Config_Writer_Ini();
                $writer->setRenderWithoutSections()->setConfig($config);
                $writer->write($configFile);
                $this->view->installed = true;
            }
        }
        $errors = array();
        if (!is_writable(APPLICATION_PATH."/../var/log")) {
            $errors[] = "« <strong>".realpath(APPLICATION_PATH."/../var/log")."</strong> »"
                ." devrait avoir les droits d'écriture.";
        }
        
        if (!is_writable(APPLICATION_PATH."/../var/sessions")) {
            $errors[] = "« <strong>".realpath(APPLICATION_PATH."/../var/sessions")."</strong> »"
                ." devrait avoir les droits d'écriture.";
        }
        
        if (!is_writable(APPLICATION_PATH."/../var/tmp")) {
            $errors[] = "« <strong>".realpath(APPLICATION_PATH."/../var/tmp")."</strong> »"
                ." devrait avoir les droits d'écriture.";
        }
        if ($errors) {
            $this->view->errors = $errors;
        }
        
        if (!$form->getValue("key")) {
            $password = "";
            $chaine = "abcdefghijklmnpqrstuvwxy0123456789";
            $lenght = strlen($chaine);
            srand((double)microtime()*1000);
            for($i=0; $i < 7; $i++) {
                $password .= $chaine[rand() % $lenght];
            }
            $key = sha1($password);
            $form->getElement("key")->setValue($key);
        }
        $this->view->form = $form;
    }
}

















