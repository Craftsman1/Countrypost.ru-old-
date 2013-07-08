<?php
/**
 * Created by JetBrains PhpStorm.
 * User: CGS
 * Date: 24.06.13
 * Time: 18:55
 */
function check_ssl()
{
    $CI =& get_instance();
    $class = $CI->router->fetch_class();
    $ssl = array('client','profile','manager','admin');
    $partial =  array('');
    $segment2 = $CI->uri->segment(2);
    if(in_array($class,$ssl) )
    {
        force_ssl();
    }
    else if(in_array($class,$partial) 
			|| (strpos($segment2,'avatar') !== false) 
			|| (strpos($segment2,'order') !== false)
			|| (strpos($segment2,'showPublicScreenshot') !== false)
			|| (strpos($segment2,'loginAjax') !== false)
			|| (strpos($segment2,'addProduct') !== false)			
			)
    {
        force_ssl();
    }
    else
    {
        unforce_ssl();
    }
}

function force_ssl()
{
    $CI =& get_instance();
    $CI->load->helper('url');
    $CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
    if (!(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")) redirect($CI->uri->uri_string());
}

function unforce_ssl()
{
    $CI =& get_instance();
    $CI->load->helper('url');
    $CI->config->config['base_url'] = str_replace('https://', 'http://', $CI->config->config['base_url']);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") redirect($CI->uri->uri_string());
}