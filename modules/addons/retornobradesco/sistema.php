<?php
/**
 * Processa Retorno
 *
 * Faz o processamento do arquivo de retorno
 *
 * @package    RetornoBradesco
 * @author     Weverton Velludo <wv@brasilnetwork.com.br>
 * @copyright  Copyright (c) Weverton Velludo 2015
 * @license    http://www.brasilnetwork.com.br
 * @version    $Id$
 * @link       http://www.brasilnetwork.com.br
 */

if (!defined("WHMCS")) die("This file cannot be accessed directly");

echo '<b>Sistema para processamento e baixa de arquivos de retorno do banco Bradesco</b><br>';

if ($vars['processar'] == "on") {
	echo '<br>As baixas serão processadas automaticamente.<br><br>';
} else {
	echo '<br>Será feita apenas uma simulação de baixa, para processamento altere a configuração do addon.<br><br>';
}

echo '<table style="background: #f5f5f5; vertical-align:top; border:0px; padding: 5px;"><tr><td><form method=post enctype="multipart/form-data"><b>Selecione o arquivo:</b> <input type=file name=arquivo><input type=submit value=Enviar></form></td></tr></table><br>';

if ($_POST && $_FILES['arquivo']['error'] != 0) {

	echo "<br><b>Ocorreu um erro ao fazer o upload do arquivo de retorno, por favor tente novamente.</b><br>";

} elseif ($_POST) {

	$arquivo = file($_FILES["arquivo"]["tmp_name"]);

	foreach ($arquivo as $dados) {

		if ($i > 0 && $i < count($arquivo)-1) {

			$documento = intval(substr($dados,126,20));
			$valor     = intval(substr($dados,	253, 13));
			$valor     = (int) substr($valor, 0, strlen($valor)-2) . "." . substr($valor, -2);
			$tarifa    = intval(substr($dados,	175, 13));
			$tarifa    = (int) substr($tarifa, 0, strlen($tarifa)-2) . "." . substr($tarifa, -2);

			$invoice = localAPI("getinvoice", array("invoiceid"=>$documento));
				
			// Se a fatura constar como não paga
			if ($invoice["status"] == "Unpaid") {
	
				// Se o valor pago for maior que o faturado registra como juros
				if ($valor > $invoice["subtotal"] && $vars['option2'] == "on") {
					$multa = $valor - $invoice["subtotal"];
				    $command = "updateinvoice";
				    $values["invoiceid"] = $documento;
				    $values["newitemdescription"] = array("Juros / Multa");
				    $values["newitemamount"] = array($multa);
				    $results = localAPI("updateinvoice",$values);
				}
	
				// Faz a baixa do pagamento
				if ($vars['processar'] == "on") {
				    $command = "addinvoicepayment";
				    $values["invoiceid"] = $documento;
				    $values["transid"] = "BRADESCO-".$documento;
				    $values["amount"] = $valor;
					$values["fees"] = $tarifa;
				    $values["gateway"] = "boletobradesco";
				    $results = localAPI($command,$values);
		
					$invoice["status"] = "Pagamento processado";
				}
				$consultacliente = "S";
			} elseif ($invoice["status"] == "error") {
				$invoice["status"] = "Não encontrado";
				$consultacliente = "N";
			} else {
				$invoice["status"] = "Fatura já está paga";
				$consultacliente = "S";
			}

			if ($consultacliente == "S") {
			    $command = "getclientsdetails";
			    $values["clientid"] = $invoice["userid"];
			    $clientedados = localAPI($command,$values);					
				$cliente = $clientedados["fullname"] . " - " . $clientedados["companyname"];
			} else {
				$cliente = "---";
			}

			$retorno[] = array("documento"=>$documento,"valor"=>$valor,"tarifa"=>$tarifa,"status"=>$invoice["status"],"cliente"=>$cliente);
		}
		$i++;
	}

	if($i >= 2) {

		echo "<br><table style='background: #f5f5f5; vertical-align:top; border:0px; padding: 10px;'><tr><td colspan=5><b>Detalhes do Processamento:</b></td></tr><tr><td width=100><b>Invoice</b></td><td><b>Cliente</b></td><td width=100  style='padding-left:10px;'><b>Valor</b></td><td width=100><b>Tarifa</b></td><td><b>Status</b></td></tr>";
		foreach ($retorno as $dados) {
			echo "<tr style='padding:3px;'><td>" . $dados["documento"] . "</td><td>" . $dados["cliente"] . "</td><td style='padding-left:10px;'>" . $dados["valor"] . "</td><td>" . $dados["tarifa"] . "</td><td>" . $dados["status"] . "</td></tr>";
		}
		echo "</table>";

	}
}	
?>