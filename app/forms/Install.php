<?php

class Form_Install extends Zend_Form
{
    public function init()
    {
        $this->setAttrib("autocomplete", "Off");
        $this->setAttrib("style", "width: 500px;");
        $this->addElement("text", "host", array(
            "label" => "Host",
            "value" => "localhost"
        ));
        $this->addElement("text", "user", array(
            "label" => "Utilisateur"
        ));
        $this->addElement("password", "password", array(
            "label" => "Mot de passe"
        ));
        $this->addElement("text", "dbname", array(
            "label" => "Base de données"
        ));
        $this->addElement("text", "email", array(
            "label" => "Expéditeur des emails",
            "required" => true
        ));
        $this->addElement("text", "key", array(
            "label" => "Clé",
            "required" => true,
            "size" => 50,
            "description" => "Cette clé est utilisée pour effectuer les tâches".
                " cron. Elle ne doit pas être communiquée. ".
                "Pensez à la sauvegarder."
        ));
        $this->addElement("text", "user_email", array(
            "label" => "E-Mail",
            "required" => true
        ));
        $this->addElement("password", "user_password", array(
            "label" => "Mot de passe",
            "required" => true
        ));
        $this->addDisplayGroup(array("host", "user", "password", "dbname"), "db", array(
            "legend" => "Base de données"
        ));
        $this->addDisplayGroup(array("email"), "emailConfig", array(
            "legend" => "Envoi des mails"
        ));
        $this->addDisplayGroup(array("key"), "other", array(
            "legend" => "Autre"
        ));
        $this->addDisplayGroup(array("user_email", "user_password"), "firstuser", array(
            "legend" => "Créer votre premier compte utilisateur"
        ));
        $this->addElement("submit", "register", array(
            "label" => "Installer",
            "ignore" => true
        ));
    }
}