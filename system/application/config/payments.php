<?
//Общее: адреса скриптов
define('TESTMODE', 0); //В большинстве случаев при 1 будут проходить тестовые платежи
//Для работы нужно установить в 0

define('SUCCESS_URL',	BASEURL . "syspay/showSuccess");
define('FAIL_URL',		BASEURL . "syspay/showFail");
define('RESULT_URL',	BASEURL . "syspay/showResult");
define('ADMIN_EMAIL',	"info@countrypost.ru");

define('MAX_O2I_RU', 15000);

//WebMoney
define('WM_PURSE',			"R335456041886");
define('WMZ_PURSE',			"Z735510829657");
define('WM_SUCCESS_URL',	BASEURL . "syspay/showSuccess");
define('WM_FAIL_URL',		BASEURL . "syspay/showFail");
define('WM_RESULT_URL',		BASEURL . "syspay/showResult");
define('WMZ_RESULT_URL',	BASEURL . "syspay/showResultWMZ");
define('WM_SECRET_KEY',		"XFgw");
define('WM_IN_TAX', 1.8);
define('WM_IN_EXTRA', 0);
define('WMZ_IN_TAX', 1.8);
define('WMZ_IN_EXTRA', 0);
define('WM_OUT_TAX', 0.8);

//RoboKassa
define('RK_LOGIN', 'Craftsman1');
define('RK_PASS1', 'robokassa1');
define('RK_PASS2', 'robokassa2');
define('RK_IN_TAX', 1.8);
define('RK_RUB_IN_TAX', 2.5);
define('RK_IN_EXTRA', 0);

//W1
define('W1_WALLET', '103853778255');
define('W1_PASS', 'AyDcbD');
define('W1_KEY', 'VGtcWHpuTmIydVJGT3F1OWZ3T2NWWXxnQXhe');
define('W1_FAIL_URL',		BASEURL . "syspay/showFail");
define('W1_SUCCESS_URL',	BASEURL . "syspay/showSuccess");
define('W1_RESULT_URL',		BASEURL . "syspay/showResult");

//Лучше не использовать кнопку "Сгенерировать" в админке, т.к. слишком длинный код иногда вызывает проблемы с ЭЦП
define('W1_IN_TAX', 3);

//LiqPay
define('LP_MERCHANT_ID', 'i2498933264');
define('LP_MERCHANT_SIG1', 'x1XA6xyodERIWefQAR3sSbpdOo1Af0bmoY5Um');
define('LP_MERCHANT_SIG2', 'OPy4OGrEhcbUa1uaiWNlzh970lUfBv93seO8wVLj');
define('LP_RESULT_URL', BASEURL . 'syspay/showResultLP');
define('LP_SERVER_URL', BASEURL . 'syspay/showServerLP');

#$lp_merchant_id="i0327037845";
#$lp_merchant_password="YB1zi3hLHCJeXEo9ZeIfcLMT56Ydw";
#$lp_result_url=BASEURL . "lp_result.php"; // success/fail
#$lp_server_url=BASEURL . "lp.php";
define('LP_IN_TAX', 3);
define('LP_OUT_TAX', 0);

//Sberbank
define('CC_IN_TAX', 2);
define('SO_IN_TAX', 2);
define('OP_IN_TAX', 2);
define('BM_IN_TAX', 1);
define('BM_OUT_TAX', 1);
define('BM_IN_ACCOUNT', '4276838059339327');
define('BM_SERVICE_NAME', 'Сбербанк');

// QIWI
define('QW_IN_TAX', 2.5);
define('QIWI_IN_TAX', 3.5);
define('QIWI_IN_EXTRA', 0);
define('QW_IN_ACCOUNT', '9161279091');
define('QW_LOGIN', '16801');
define('QIWI_PASS', 'WfRx15NPWfAL3LNORWtn');
define('QW_OUT_TAX', 0);
define('QIWI_SUCCESS_URL', BASEURL . "syspay/showResultQW");

// RBK Money
define('RBK_IN_TAX', 2.5);
define('RBK_IN_ACCOUNT', 'RU606456384');

// PayPal
define('PP_TEST', 0);
define('PP_IN_TAX', 4);
define('PP_IN_EXTRA', 5);
define('PP_ACCOUNT', 'stuff82@gmail.com');
define('PP_TEST_ACCOUNT', 'stuff82@gmail.com');
define('PP_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('PP_TEST_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
define('PP_RETURN_URL', BASEURL . 'syspay/showResultPP');
define('PP_NOTIFY_URL', BASEURL . 'syspay/callbackPP');
define('PP_CALLBACK_URL', BASEURL . 'syspay/callbackPP');
define('PP_IMAGE_URL', BASEURL . 'static/images/logo.png');
define('PP_CANCEL_URL', BASEURL . 'client');

// UAH
define('PB_IN_TAX', 1.5);
define('PB_IN_ACCOUNT', '4627085825024728');

// BTA
define('BTA_IN_TAX', 2.4);
define('BTA_IN_ACCOUNT', '4256801510176849');
define('BTA_SERVICE_NAME', 'БТА Банк');

// CCR
define('CCR_IN_TAX', 2.4);
define('CCR_IN_ACCOUNT', '4667209610401898');
define('CCR_SERVICE_NAME', 'ЦентрКредит Банк');

// KKB
define('KKB_IN_TAX', 2.4);
define('KKB_IN_ACCOUNT', '6762045559163472');
define('KKB_SERVICE_NAME', 'Казкоммерцбанк');

// NB
define('NB_IN_TAX', 2.4);
define('NB_IN_ACCOUNT', '6762003509188602');
define('NB_SERVICE_NAME', 'Народный Банк');

// TB
define('TB_IN_TAX', 2.4);
define('TB_IN_ACCOUNT', '4392232500449829');
define('TB_SERVICE_NAME', 'Темирбанк');

// ATF
define('ATF_IN_TAX', 2.4);
define('ATF_IN_ACCOUNT', '4052587100780654');
define('ATF_SERVICE_NAME', 'АТФ Банк');

// AB
define('AB_IN_TAX', 2.4);
define('AB_IN_ACCOUNT', '4042428902488290');
define('AB_SERVICE_NAME', 'Альянсбанк');

// SV
define('SV_IN_TAX', 1);
define('SV_IN_ACCOUNT', '5203390539416346');
define('SV_SERVICE_NAME', 'Связной Банк');

// VTB
define('VTB_IN_TAX', 1);
define('VTB_IN_ACCOUNT', 'УНК 10180317 (Тонконогов Юрий Андреевич');
define('VTB_SERVICE_NAME', 'ВТБ Банк');

// Alfa RUB
define('AL_RUB_IN_TAX', 1);
define('AL_USD_IN_TAX', 1);
define('AL_RUB_IN_ACCOUNT', '');
define('AL_USD_IN_ACCOUNT', '');
define('AL_SERVICE_NAME', 'Альфа Банк');

// Western Union
define('WU_RUB_IN_TAX', 1);
define('WU_USD_IN_TAX', 1);
define('WU_RUB_IN_ACCOUNT', 'получатель: TONKONOGOV YURIY ANDREEVICH (Moscow, Russia)');
define('WU_USD_IN_ACCOUNT', 'получатель: TONKONOGOV YURIY ANDREEVICH (Moscow, Russia)');
define('WU_SERVICE_NAME', 'Western Union');

// Contact
define('CON_RUB_IN_TAX', 1);
define('CON_USD_IN_TAX', 1);
define('CON_RUB_IN_ACCOUNT', 'рублевые данные Contact');
define('CON_USD_IN_ACCOUNT', 'долларовые данные Contact');
define('CON_SERVICE_NAME', 'Contact');

// Unistream
define('UNI_RUB_IN_TAX', 1);
define('UNI_USD_IN_TAX', 1);
define('UNI_RUB_IN_ACCOUNT', 'рублевые данные Unistream');
define('UNI_USD_IN_ACCOUNT', 'долларовые данные Unistream');
define('UNI_SERVICE_NAME', 'Unistream');

// Золотая Корона
define('GC_RUB_IN_TAX', 1);
define('GC_USD_IN_TAX', 1);
define('GC_RUB_IN_ACCOUNT', 'рублевые данные Золотая Корона');
define('GC_USD_IN_ACCOUNT', 'долларовые данные Золотая Корона');
define('GC_SERVICE_NAME', 'Золотая Корона');

// Anelik
define('AN_RUB_IN_TAX', 1);
define('AN_USD_IN_TAX', 1);
define('AN_RUB_IN_ACCOUNT', 'рублевые данные Anelik');
define('AN_USD_IN_ACCOUNT', 'долларовые данные Anelik');
define('AN_SERVICE_NAME', 'Anelik');

// Visa/Mastercard
define('VM_RUB_IN_TAX', 1);
define('VM_RUB_IN_ACCOUNT', 'рублевые данные Visa/Mastercard');
define('VM_SERVICE_NAME', 'Visa/Mastercard');

// Нал
define('CUS_USD_IN_TAX', 1);
define('CUS_USD_IN_ACCOUNT', 'долларовые данные нал');
define('CUS_SERVICE_NAME', 'Наличные');