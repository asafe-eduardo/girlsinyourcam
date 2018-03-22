<?php
#############################################
#                                           #
# 	Autor: Eduardo Asafe #
#		E-Mail: asafe.eduardo@gmail.com
#                                           #
#############################################

/*
   Classe para gerenciamento das despesas

*/
class Despesa{
	
	// Função ImprimirDespesas
	// Responsável por imprimir as linhas da tabela na página despesas.php, recebe o id do usuario como parametro e pesquisa no banco as despesas relacionadas a este usuario
	public function ImprimirDespesas($id_usuario){
		$dinheiro = 0;
		$My = new MySQLiConnection();
		$sql = $My->query ("SELECT * FROM despesas WHERE id_usuario = '".$id_usuario."' ORDER BY data_despesa ASC");
		$total = $sql->num_rows;
		if ($total == 0)
		{
		    echo "
		    <script type=\"text/javascript\">
		    alert ('Nenhum registro de despesa encontrado para o usuario');
		    </script>
		    ";
		    return false;
		}
			
		while($f = $sql->fetch_object()){
			$date = new DateTime( $f->data_despesa);
			$date2 = new DateTime( $f->data_despesa_hora);
			if( strtolower($f->categoria) == 'compra'){
				$dinheiro += $f->valor;						
			}
			if( strtolower($f->categoria) == 'venda'){
				$dinheiro -= $f->valor;						
			}
			echo "<tr>
						<td>". $date->format('d/m/y')."</td>
						<td>". $date2->format('H:i:s')."</td>
						<td><a href=alterar_despesas.php?id_despesa=".$f->id.">". $f->titulo ."</a></td>
						<td>". $f->descricao ."</td>
						<td>". $f->categoria ."</td>
						<td>". number_format($f->valor, 2, ",", ".")." $</td>";		
		}
			echo "<td>".number_format($this->TotalGYCoins($dinheiro) , 2, ".", ".") ." G</td>
						</tr>";		
			
	}

	//	Função retornaDespesas 
	// Primeiramente a função inicializa 3 arrays, o array $dados contém duas posições denominadas como 'Compras' e 'Vendas' que serão preenchidas com outro array($compras ou $ vendas) conforme a posição do array $dados exige. 
	// Feito isto é realizado a conexão com MySQLi pesquisando a soma dos meses da categoria respectiva (isto é 'compra' ou 'venda') agrupando pelo mes.
	// O retorno da pesquisa e preenchido pelo array correspondente ($compras ou $vendas) logo após isso os dois arrays preenchidos com os dados dos meses serão armazenados no array $dados.
	// Importante: já que os dados são armazenados no array sem nenhuma verificação a quantidade de meses no banco de dados e as posições do array importam.(para possíveis erros de dados).
	public function retornaDespesas($id_usuario){
		$dados = array( "Compras" => 0, "Vendas" => 0);
		$compras = array();
		$vendas = array();
		
		$My = new MySQLiConnection();
		
		// COMPRAS
		$sql = $My->query ("SELECT SUM(valor) as soma, MONTH(data_despesa),categoria FROM despesas WHERE categoria = 'Compra' AND  id_usuario ='".$id_usuario."' GROUP BY MONTH(data_despesa)");
		$total = $sql->num_rows;
		if ($total == 0){echo "<script type=\"text/javascript\">alert ('Nenhum registro de compra encontrado para o usuario'); </script> ";return false;}
		while($f = $sql->fetch_object()){array_push($compras, $f->soma);}
				
		// VENDAS
		$sql = $My->query ("SELECT SUM(valor) as soma,MONTH(data_despesa),categoria FROM despesas WHERE categoria = 'Venda' AND  id_usuario ='".$id_usuario."' GROUP BY MONTH(data_despesa)");
		$total = $sql->num_rows;
		if ($total == 0){echo "<script type=\"text/javascript\"> alert ('Nenhum registro de venda encontrado para o usuario');</script> ";return false;}
		while($f = $sql->fetch_object()){array_push($vendas, $f->soma);}	
		
		// RETORNO
		return $dados = array( "Compras" => $compras, "Vendas" => $vendas);

	}
	
	//	Função retornaDespesasAnual
	// O algoritmo e semelhante a função retornaDespesas todavia deve se atentar para sintaxe da pesquisa MySQLi que agora vai pegara a soma do ano e agrupar pelo ano.
	public function retornaDespesasAnual($id_usuario){
		$dados = array( "Compras" => 0, "Vendas" => 0);
		$compras = array();
		$vendas = array();
		
		$My = new MySQLiConnection();
		
		// COMPRAS
		$sql = $My->query ("SELECT SUM(valor) as soma,YEAR(data_despesa),categoria FROM despesas WHERE categoria = 'compra' AND  id_usuario ='".$id_usuario."' GROUP BY  YEAR(data_despesa)");
		$total = $sql->num_rows;
		if ($total == 0){echo "<script type=\"text/javascript\">alert ('Nenhum registro de compra encontrado para o usuario'); </script> ";return false;}
		while($f = $sql->fetch_object()){array_push($compras, $f->soma);}
				
		// VENDAS
		$sql = $My->query ("SELECT SUM(valor) as soma,YEAR(data_despesa),categoria FROM despesas WHERE categoria = 'venda' AND  id_usuario ='".$id_usuario."' GROUP BY YEAR(data_despesa)");
		$total = $sql->num_rows;
		if ($total == 0){echo "<script type=\"text/javascript\"> alert ('Nenhum registro de venda encontrado para o usuario');</script> ";return false;}
		while($f = $sql->fetch_object()){array_push($vendas, $f->soma);}	
		
		// RETORNO
		return $dados = array( "Compras" => $compras, "Vendas" => $vendas);

	}
	
	// Função que quando chamada preenche uma $_SESSION com os dados de uma determinada despesa
	public function retornaDespesaPorId($id){
		$My = new MySQLiConnection();

		$sql = $My->query ("SELECT * FROM despesas WHERE id='".$id."'");
		$total = $sql->num_rows;
		$f = $sql->fetch_object();
		$date = new DateTime( $f->data_despesa);
		$date2 = new DateTime( $f->data_despesa_hora);
		$_SESSION['despesas']['id'] = $f->id;
		$_SESSION['despesas']['titulo'] = $f->titulo;
		$_SESSION['despesas']['descricao'] = $f->descricao;
		$_SESSION['despesas']['valor'] = $f->valor;
		$_SESSION['despesas']['categoria'] = $f->categoria;
		$_SESSION['despesas']['data'] = $date->format('d/m/y');
		$_SESSION['despesas']['hora'] = $date2->format('H:i:s');

	}
	
	// Função TotalGYCoins
	// Essa função só serve para calcular (virtualmente) quantas gycoins (A moeda de troca da plataforma GYCam) o usuario tem.
	private function TotalGYCoins($dinheiro){
		// 1 Real equivale a 10 GYCoins
		return  $gycoins = ($dinheiro * 10);
		
	}
	
	// Função InserirDespesas
	// Esta função lida com a inserção de despesas no banco de dados, obviamente ao inserir uma nova despesa a data corrente será usada mas, para fins didáticos o usuario escolhe a data.
	public function InserirDespesas ($titulo, $descricao, $valor, $categoria,$dataDespesa, $horaDespesa, $id_usuario){
		$erro = array();
		if ($titulo == NULL || $titulo == '')
			$erro[] = "Dê um titulo para essa despesa.";
		if ($descricao == NULL || $descricao == '')
		    $erro[] = "Escolha uma descrição para essa despesa";
		if ($categoria == NULL || $categoria == '')
		    $erro[] = "Escolha uma categoria para essa despesa";
		if ($valor == NULL || $valor == '')
		    $erro[] = "Digite um valor para a despesa";
		if ($valor < 0)
		    $erro[] = "O valor não pode ser negativo";
		
		if (count ($erro) > 0){
			$msg = "";
			foreach ($erro as $v) $msg .= $v."\\n";
			echo "<script type=\"text/javascript\">alert ('".$msg."');location.href = '".SYS_ROOT_DIR."/despesas.php';</script>";
			return false;
		}
		
		$My = new MySQLiConnection();
		
		$tb_name = "despesas";
		$sql = @$My->query ("INSERT INTO ".$tb_name." VALUES  (NULL, '".$titulo."', '".$descricao."', ". $valor.", '". $categoria ."', '".$dataDespesa."', ('".$dataDespesa . " + ".$horaDespesa."'),". $id_usuario.");");
		if ($sql === true){
		    if ($My->affected_rows == 1){
			    echo "<script = 'text/javascript'>alert('Nova despesa cadastrada!');window.location.href='despesas.php'</script> ";
		    }
		}
		elseif ($My->errno != 0){
			$error_code = $My->errno;
			if (function_exists ("Erro".$error_code)){
				switch ($error_code){
					case 1051:
					  Erro1051 ($tb_name);
					  break;
					case 1146:
					  Erro1146 ($tb_name);
					  break;
					default:
					  call_user_func ("Erro".$error_code);
					  break;
				}
			}
			else{
				echo $My->error;
			}
			echo "<script = 'text/javascript'>window.location.href='despesas.php'</script> ";
		}
			
	}
	
	// Função responsável pela alteração de uma determinada despesa registrada no banco de dados
	public function AlterarDespesa ($titulo, $descricao, $valor, $categoria,$dataDespesa, $horaDespesa, $id){
		$erro = array();
		if ($titulo == NULL || $titulo == '')
			$erro[] = "Dê um titulo para essa despesa.";
		if ($descricao == NULL || $descricao == '')
		    $erro[] = "Escolha uma descrição para essa despesa";
		if ($categoria == NULL || $categoria == '')
		    $erro[] = "Escolha uma categoria para essa despesa";
		if ($valor == NULL || $valor == '')
		    $erro[] = "Digite um valor para a despesa";
		if ($valor < 0)
		    $erro[] = "O valor não pode ser negativo";
		
		if (count ($erro) > 0){
			$msg = "";
			foreach ($erro as $v) $msg .= $v."\\n";
			echo "<script type=\"text/javascript\">alert ('".$msg."');location.href = '".SYS_ROOT_DIR."/despesas.php';</script>";
			return false;
		}
		
		$My = new MySQLiConnection();
		
		$tb_name = "despesas";
		$sql = @$My->query ("UPDATE ".$tb_name." SET titulo='".$titulo."', descricao='".$descricao." ', valor= ".$valor.", categoria='".$categoria."', data_despesa='".$dataDespesa."', data_despesa_hora=('".$dataDespesa . " + ".$horaDespesa."') WHERE id='".$id."'; ");
		if ($sql === true){
		    if ($My->affected_rows == 1){
			    echo "<script = 'text/javascript'>alert('Despesa alterada!');window.location.href='despesas.php'</script> ";
		    }
		}
		elseif ($My->errno != 0){
			$error_code = $My->errno;
			if (function_exists ("Erro".$error_code)){
				switch ($error_code){
					case 1051:
					  Erro1051 ($tb_name);
					  break;
					case 1146:
					  Erro1146 ($tb_name);
					  break;
					default:
					  call_user_func ("Erro".$error_code);
					  break;
				}
			}
			else{
				echo $My->error;
			}
			echo "<script = 'text/javascript'>window.location.href='despesas.php'</script> ";
		}
			
	}
	
}
?>