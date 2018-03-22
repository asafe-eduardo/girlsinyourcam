<?php
#############################################
#                                           #
# Autor: Roberto Beraldo Chaiben (Beraldo)  #
#      E-Mail: rbchaiben@gmail.com          #
#                                           #
#############################################

/*
   Classe para conexуo com banco de dados MySQL usando a extensуo MySQLi, disponэvel a partir do PHP 5.
   A classe contщm o construtor de conexуo, registrando os possэveus erros de conexуo no arquivo db_errors.log, e o destrutor, para fechar a conexуo. As demais fgunчѕes de banco de dados sуo padrѕes da classe myslqi, devendo ser usadas, por exemplo, desta forma:
   
   $this->query();


   Caso vocъ nуo tenha um arquivo de inicializaчуo que defina as constantes para conexуo com o banco de dados e o caminho para o diretѓrio dos arquivos de logs de erro, descomente a parte do cѓdigo que usa a funчуo define() e configure-a com as informaчѕes para conexуo.

*/

// Constantes paea conexуo com o banco de dados:
/*
define ("DB_SERVIDOR", "localhost");
define ("DB_USUARIO", "root");
define ("DB_SENHA", "asxz123");
define ("DB_NOME", "testes");
define ("LOGS_PATH", "logs/");
*/


class MySQLiConnection extends mysqli
{
	public function __construct()
	{
		try
		{
			//@mysqli_connect (DB_SERVIDOR, DB_USUARIO, DB_SENHA, DB_NOME);
			parent::__construct (DB_SERVIDOR, DB_USUARIO, DB_SENHA, DB_NOME);
			if (mysqli_connect_errno() != 0)
			    throw new Exception (mysqli_connect_errno() . " - " . mysqli_connect_error());
		}
		catch (Exception $db_error)
		{
			$mensagem = $db_error->getMessage();
			$arquivo = $db_error->getFile();
			$data = date ("Y-m-d H:i:s");
			$ip_visitante = $_SERVER['REMOTE_ADDR'];
			
			if (!file_exists (LOGS_PATH))
			    mkdir (LOGS_PATH);
			
			// mensagem que serс salva no arquivo de logs do banco de dados
			$log = $data . " | " . $mensagem . " | " . $arquivo . " | " . $ip_visitante . "\r\n\r\n";
			error_log ($log, 3, LOGS_PATH . "db_errors.log");
			echo "Erro ao conectar ao banco de dados MySQL. O erro foi reportado e o administrador do sistema tomarс as devidas providъncias.";
			exit;
			
		}
	}
	
	
	public function __destruct()
	{
		if (mysqli_connect_errno() == 0)
			$this->close();
	}
	
}

?>