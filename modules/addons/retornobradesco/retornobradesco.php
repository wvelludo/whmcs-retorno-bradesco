<?php
/**
 * Retorno Bradesco
 *
 * Este Addon trata os arquivos de retorno do banco Bradesco para baixa via boleto bancario.
 *
 * @package    RetornoBradesco
 * @author     Weverton Velludo <wv@brasilnetwork.com.br>
 * @copyright  Copyright (c) Weverton Velludo 2015
 * @license    http://www.brasilnetwork.com.br
 * @version    $Id$
 * @link       http://www.brasilnetwork.com.br
 */

if (!defined("WHMCS")) die("This file cannot be accessed directly");

function retornobradesco_config() {
    $configarray = array(
    "name" => "Retorno Bradesco",
    "description" => "Processamento de arquivos de retorno do banco Bradesco",
    "version" => "1.0",
    "author" => "Weverton Velludo",
    "language" => "portuguese_br",
    "fields" => array(
        "processar" => array ("FriendlyName" => "Processar baixa", "Type" => "yesno", "Size" => "25", "Description" => "Marque se quiser que o módulo processe a baixa do pagamento")
    ));
    return $configarray;
}

function retornobradesco_activate() {

    # Return Result
    return array('status'=>'success','description'=>'Módulo ativado com sucesso');
}

function retornobradesco_deactivate() {

    # Return Result
    return array('status'=>'success','description'=>'Módulo desativado com sucesso');
}

function retornobradesco_upgrade($vars) {

}

function retornobradesco_output($vars) {

    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $option1 = $vars['option1'];
    $option2 = $vars['option2'];
    $option3 = $vars['option3'];
    $option4 = $vars['option4'];
    $option5 = $vars['option5'];
    $LANG = $vars['_lang'];
	
	// Para maior organização aqui é feito o include da classe
	require_once(dirname(__FILE__) . "/sistema.php");
}