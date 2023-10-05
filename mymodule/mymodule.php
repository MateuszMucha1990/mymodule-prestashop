<?php

if (!defined('_PS_VERSION_')) {
    exit;
};

class MyModule extends Module
{
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'mateusz mucha';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6.0',
            'max' => '1.7.9',];
        $this->bootstrap = true;

        parent:: __construct();
        
        $this->displayName = $this->l('Pierwszy modul', 'mymodule');
        $this->description = $this->l('opis modułu.','mymodule');    
   
        $this->confirmUninstall = $this->l('Chcesz odinstalować');
        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided.');
        }
    }    


    // public function install()
    // {
    //     if (!parent::install() 
    //     || !$this->registerHook('displayHome')
    //     ) {
    //         return false;
    //     }
    //     return true;
    // } 
 

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
    
        return parent::install() &&
            // $this->registerHook('displayLeftColumn') &&

            //CSS
            $this->registerHook('actionFrontControllerSetMedia') &&

            //DISPLAY HOME
            $this->registerHook('displayHome') &&
            Configuration::updateValue('MYMODULE_NAME', 'my friend');

            
    }



public function uninstall()
    {
        return parent::uninstall();
    }


public function displayForm()
{
    $form = [
        'form' => [
            'legend' => [
                'title' => $this->l('Dane'),
            ],
            'input' => [
                [
                    'type' => 'textarea',
                    'label' => $this->l('Wprowadź Imie'),
                    'name' => 'MYMODULE_CONFIG',
                    'size' => 20,
                    'required' => true,
                    'autoload_rte' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Wprowadź nazwisko'),
                    'name' => 'MYMODULE_CONFIG',
                    'size' => 20,
                    'required' => true,
                    'autoload_rte' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ],
    ];

    $helper = new HelperForm();

    $helper->table = $this->table;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
    $helper->submit_action = 'submit' . $this->name;


    $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

    $helper->fields_value['MYMODULE_CONFIG'] = Tools::getValue('MYMODULE_CONFIG', Configuration::get('MYMODULE_CONFIG'));

    return $helper->generateForm([$form]);
}



public function getContent()
{
    $output = '';

    if (Tools::isSubmit('submit' . $this->name)) {
        $configValue = (string) Tools::getValue('MYMODULE_CONFIG');

        if (empty($configValue) || !Validate::isGenericName($configValue)) {
            $output = $this->displayError($this->l('Invalid Configuration value'));
        } else {
            Configuration::updateValue('MYMODULE_CONFIG', $configValue);
            $output = $this->displayConfirmation($this->l('Settings updated'));
        }
    }
    return $output . $this->displayForm();
}


public function hookDisplayHome($params)
    {
        $this->context->smarty->assign(
            array('myfirstmodule' => Configuration::get('myfirstmodule'))
        );
        return $this->display(__FILE__, '/views/templates/myfirstmodule.tpl');
    }


public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'mymodule-style',
            $this->_path.'views/css/mymodule.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );

        $this->context->controller->registerJavascript(
            'mymodule-javascript',
            $this->_path.'views/js/mymodule.js',
            [
                'position' => 'bottom',
                'priority' => 1000,
            ]
        );
    }    

};
