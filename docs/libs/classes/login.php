<?php
#############################################
#                                           #
# 	Autores: Roberto Beraldo Chaiben (Beraldo), Eduardo Asafe #
#      E-Mail: rbchaiben@gmail.com
#		E-Mail: asafe.eduardo@gmail.com
#                                           #
#############################################

/*
   Classe para autenticação de usuários segundo níveis de acesso, sendo eles:
   
   1 -> Conta de Administradores
   2 -> Conta de Usuário Comuns
   
   * Adaptada por asafe para cumprimir com algumas exigências específicas da aplicação
*/

define ("SYS_ROOT_DIR", "http://localhost:9999/Site_Trabalho");

class Login{
	/*
	   Função RegistrarUsuario
	   Esta função é utilizada para inserir um novo cadastro na tabela de usuários, além de chamar a função EnviarAtivação(), a qual usa a classe PHPMailer para enviar a mensagem para ativação de registro no sistema.
	*/
	public function RegistrarUsuario ($nome, $email, $login, $senha1, $senha2, $dicaSenha){
		$erro = array();
		if ($email == NULL || $email == '')
		    $erro[] = "Informe seu e-mail.";
		if (!ValidarEmail ($email))
		    $erro[] = "O e-mail informado é inválido.";
		if ($login == NULL || $login == '')
		    $erro[] = "Escolha um login (nome de usuário).";
		if (strlen ($login) > 30)
		    $erro[] = "O login não pode ter mais de 30 caracteres.";
		if ($senha1 == NULL || $senha1 == '')
		    $erro[] = "Digite uma senha.";
		if ($senha2 == NULL || $senha2 == '')
		    $erro[] = "Você não confirmou sua senha.";
		if ($senha1 != $senha2)
		    $erro[] = "Você digitou duas senhas diferentes.";
		if ($dicaSenha == NULL || $dicaSenha == '')
		    $erro[] = "Você não digitou uma dica de senha.";
		
		if (count ($erro) > 0){// se ocorrer(em) erro(s)
			$msg = "";
			foreach ($erro as $v) 
			    $msg .= $v."\\n";
				echo "<script type=\"text/javascript\">alert ('".$msg."');location.href = '".SYS_ROOT_DIR."/registro.php';</script>";
			
			return false;
		}
		$senha = md5($senha1);
		$My = new MySQLiConnection();
		$tb_name = "usuarios";
		$sql = @$My->query ("INSERT INTO ".$tb_name." VALUES (NULL, '".$nome."', '".$email."', '".$login."', '".$dicaSenha."',  '".$senha."')");
		if ($sql === true){
		    if ($My->affected_rows == 1){
			    echo "<script type=\"text/javascript\">alert ('Usuario cadastrado com sucesso!');location.href = '".SYS_ROOT_DIR."/login.php';</script>";
			    return true;
		    }
		}
		elseif ($My->errno != 0){
		 //a consulta falhou
			$error_code = $My->errno;
			if (function_exists ("Erro".$error_code)){//se houver a função de erro personalizada
				switch ($error_code){
					case 1051:
					  Erro1051 ($tb_name);
					  break;
					case 1062:
					  echo "<script = 'text/javascript'>alert('O nome de login escolhido já existe');</script>";
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
			echo "<script = 'text/javascript'>window.location.href='registro.php'</script> ";
		}
			
	}
	
	public function AutenticarUsuario($user, $senha){
		if ($user == NULL || $user == ''){
			echo "
			<script type=\"text/javascript\">
			alert ('Digite um nome de usuário');
			location.href = '".SYS_ROOT_DIR."';
			</script>
			";
			return false;
		}
		if ($senha == md5 (NULL) || $senha == md5 ('')){
			echo "
			<script type=\"text/javascript\">
			alert ('Digite uma senha');
			location.href = '".SYS_ROOT_DIR."';
			</script>
			";
			return false;
		}
		
		$My = new MySQLiConnection();
		$sql = $My->query ("Select * From usuarios Where login = '".$user."'");
		$total = $sql->num_rows;
		if ($total == 0){
		    // usuário não encontrado
			session_destroy();
		    echo "
		    <script type=\"text/javascript\">
		    alert ('Usuário \"".$user."\" não encontrado');
		    location.href = '".SYS_ROOT_DIR."/login.php';
		    </script>
		    ";
		    return false;
		}
		if ($total == 1){
			$f = $sql->fetch_object();
			$id_bd = $f->id;
			$nome_bd = $f->nome;
			$email_bd = $f->email;
			$user_bd = $f->login;
			$senha_bd = $f->senha;
			$dicaSenha_bd = $f->dica_senha;
			
			 if ($senha_bd == $senha){ //senha válida
					
			      $_SESSION['login']['id_usuario'] = $id_bd;
				  $_SESSION['login']['nome'] = $nome_bd;
				  $_SESSION['login']['email'] = $email_bd;
			      $_SESSION['login']['user'] = $user_bd;
			      $_SESSION['login']['senha'] = $senha_bd;
				  $_SESSION['login']['dica-senha'] = $dicaSenha_bd;
			      $_SESSION['login']['auth'] = md5 (1);
				  header ("Location: ".SYS_ROOT_DIR ."/home/home.php");
			      return true;
		        }
			    else{   //senha inválida
		            session_destroy();
		            echo "
		            <script type=\"text/javascript\">
		            alert ('Senha inválida para o usuário \"".$user."\".');
		            location.href = '".SYS_ROOT_DIR."/login.php';
		            </script>
		            ";
		            return false;
		     }
		}
	}
	
	/*
	   Função AlterarUsuario
	   Esta função é utilizada para alterar os dados do usuario
	*/
	public function AlterarUsuario ($nome, $email, $login, $senha1, $senha2, $dicaSenha){
		$erro = array();
		if ($email == NULL || $email == '')
		    $erro[] = "Informe seu e-mail.";
		if (!ValidarEmail ($email))
		    $erro[] = "O e-mail informado é inválido.";
		if ($login == NULL || $login == '')
		    $erro[] = "Escolha um login (nome de usuário).";
		if (strlen ($login) > 30)
		    $erro[] = "O login não pode ter mais de 30 caracteres.";
		if ($senha1 == NULL || $senha1 == '')
		    $erro[] = "Digite uma senha.";
		if ($senha2 == NULL || $senha2 == '')
		    $erro[] = "Você não confirmou sua senha.";
		if ($senha1 != $senha2)
		    $erro[] = "Você digitou duas senhas diferentes.";
		if ($dicaSenha == NULL || $dicaSenha == '')
		    $erro[] = "Você não digitou uma dica de senha.";
		
		if (count ($erro) > 0){// se ocorrer(em) erro(s){
			$msg = "";
			foreach ($erro as $v)
			    $msg .= $v."\\n";
			echo "
			<script type=\"text/javascript\">
			alert ('".$msg."');
			location.href = '".SYS_ROOT_DIR."/home/profile.php';
			</script>
			";
			return false;
		}
		$senha = md5($senha1);
		$My = new MySQLiConnection();
		
		//nome da tabela na qual são inseridos os dados dos usuários
		$tb_name = "usuarios";
		$sql = @$My->query ("UPDATE ".$tb_name." SET  nome ='".$nome."', email ='".$email." ', login = '".$login."', dica_senha ='".$dicaSenha."', senha ='".$senha."' WHERE login = '".$login."'; ");
		if ($sql === true){
		    if ($My->affected_rows == 1){
				  
		    }
		}
		elseif ($My->errno != 0){//a consulta falhou
			$error_code = $My->errno;
			if (function_exists ("Erro".$error_code)){//se houver a função de erro personalizada
				switch ($error_code){
					case 1051:
					  Erro1051 ($tb_name);
					  break;
					case 1062:
					  echo "<script = 'text/javascript'>alert('O nome de login escolhido já existe');</script>";
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
			echo "<script = 'text/javascript'>location.href = '".SYS_ROOT_DIR."/home/profile.php'</script> ";
		}
			
	}
	/*
	   Função RecuperarSenha
	   Esta função é utilizada para recuperar a senha do usuario tendo passado o email e a dica de senha corretamente, o sistema 'reseta' a senha do usuario para o padrão: 123
	*/	
	public function RecuperarSenha ($email, $dicaSenha){
		$erro = array();
		if ($email == NULL || $email == '')
		    $erro[] = "Informe seu e-mail.";
		if (!ValidarEmail ($email))
		    $erro[] = "O e-mail informado é inválido.";
		if ($dicaSenha == NULL || $dicaSenha == '')
		    $erro[] = "Você não digitou uma dica de senha.";
		
		if (count ($erro) > 0){// se ocorrer(em) erro(s)
			$msg = "";
			foreach ($erro as $v) 
			    $msg .= $v."\\n";
				echo "<script type=\"text/javascript\">alert ('".$msg."');location.href = '".SYS_ROOT_DIR."/login.php';</script>";
			
			return false;
		}
		$senha = md5("123");
		$My = new MySQLiConnection();
		$sql = @$My->query ("SELECT id,email, dica_senha FROM usuarios WHERE email='".$email."' AND dica_senha='". $dicaSenha ."'");
		$total = $sql->num_rows;
		 if ($total == 1){
		 		$f = $sql->fetch_object();
				$id = $f->id;
			    $sql = @$My->query ("UPDATE usuarios SET senha='". $senha ."' WHERE id= ".$id."");
				if ($sql === true){
					if ($My->affected_rows == 1){
						echo "<script type=\"text/javascript\">alert ('A senha foi alterada para a senha padrao: 123');location.href = '".SYS_ROOT_DIR."/login.php';</script>";
					}
					if ($My->affected_rows == 0){
						echo "<script type=\"text/javascript\">alert ('Sua senha ja esta no padrao: 123');location.href = '".SYS_ROOT_DIR."/login.php';</script>";
					}
				
				}
		 } elseif($total == 0) {
			echo "<script type=\"text/javascript\">alert ('Nenhum registro foi encontrado com os dados fornecidos!');location.href = '".SYS_ROOT_DIR."/recuperarSenha.php';</script>";
		 }
		elseif ($My->errno != 0){
		 //a consulta falhou
			$error_code = $My->errno;
			if (function_exists ("Erro".$error_code)){//se houver a função de erro personalizada
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
			echo "<script = 'text/javascript'>window.location.href='login.php'</script> ";
		}
			
	}
	
	
}
?>