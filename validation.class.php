<?php
/**
 * @name Validation
 * @package radig
 * @subpackage validation
 * 
 * @author Cauan Cabral <contato@cauancabral.net>, Jefferson Estanislau da Silva
 * @copyright radig.com.br, exceto a função validCPF
 * @license MIT License, except the method validCPF
 * 
 *
 * Exemplo de uso da função:
 *
 * $cpf = '123.456.789.10';
 * echo '<br />Validando cpf: '.$cpf.'<br />';
 * echo Validation::isValid($cpf, 'cpf');
 * 
 *
 * @param string $valor: uma string de qualquer natureza
 * @param string $tipo: uma string com qualquer um dos seguintes valores:
 *    - cpf : valida um cpf da forma 000.111.222-33
 *    - cep : valida um cep da forma 11111-222
 *    - fone : valida um telefone da forma 055663333-4444 ou 3333-4444
 *    - email : valida um email da forma nome@host.com
 *    - data : valida uma data da forma 01/01/1001
 *    - texto : valida um texto da forma abcdefgh
 *    - alphanum : valida um texto com números
 *    - num : aceita apenas números
 *    - all : aceita qualquer coisa
 *
 * @return mixed Retorna o valor limpo e validado, ou então false.
 */

class Validation {

	/**
	 * Atributo que guardará os valores que serão validados
 	 */
	private $values = array();
	/**
	 * Atributo que amarmazena os possíveis erros de validação encontrados
	 * para cada elemento validado e presente no atributo $this->values
	 */  
	private $errors = array();
	
	/**
	 * Método construtor, inicializa classe para que seja feito a validação de vários valores
	 * 
	 * @param array $values - array associativo
	 *  com a estrutura array('valor' => 'tipo esperado', 'valor2' => 'tipo esperado2')
	 *
	 */
	public function Validation($values)
	{
		if(is_array($values))
		{
			$this->values = $values;
		}
		else
		{
			$this->errors = 'Você deve passar um  array associativo como parâmetro para inicializar a classe';
			return false;
		}
	}
	
	public function hasErrors()
	{
		
	}
	
	/*
	 * Método que executa a validação para todas as entradas do parâmetro $values
	 *  e armazena os erros encontrados no parâmetro $errors
	 * 
	 */
	protected function validateAll()
	{
		if(is_array($this->values) && !empty($this->values))
		{
			foreach($this->values as $value => $rule)
			{
				if(is_string($value) && is_string($rule))
				{
					//executa a validação
					$this->errors[$value] = Validation::isValid($value, $rule);
				}
				else
				{
					$this->errors[$value] = 'Este não pode ser validado. Verifique se o tipo de validação foi passado';
				}
			}
		}
		else
		{
			$this->errors = 'O parâmetro $values não está definido, ou foi definido incorretamente. Ele deve ser um array';
			return false;
		}
	}
	
	/**
	 * Método estático que verifica se um valor é de um determinado tipo
	 *
	 */
	static public function isValid( $valor, $tipo )
	{
		$erCPF = '/[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/';
		$erCEP = '/[0-9]{5}\-[0-9]{3}/';
		$erFONE = '/(0((([0-9]{2}){2})|([0-9]{2})))?[0-9]{3,5}\-[0-9]{4}/';
		$erEMAIL = '/[[:alnum:]]\@[[:alnum:]]+(\.[[:alnum:]])+/';
		$erDATA = '/(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/[12][0-9]{3}/';
		$erTEXTO = '/([a-z][A-Z]\ )*/';//aceita somente letras
		$erALPHANUM = '/[[:alnum:]]*/';//aceita numeros e letras
		$erNUM = '/[0-9]*/';//aceita somente numeros
		$erALL = '.*';//aceita qualquer coisa
		
		//funções buil-in do PHP para validação
		$builtin = array('is_numeric', 'is_int', 'is_string', 'is_array', 'is_boolean', 'is_float');
		 
		$padrao = 'er'.strtoupper( trim($tipo) ); //faço concatenação do prefixo 'er' com o tipo, para formar o nome da variavel com o padrao correto.
		 
		if( $$padrao == NULL && !in_array($tipo, $builtin) )
			die('Tipo não suportado para validação');
		
		$valor = strip_tags( htmlentities(trim($valor)), '<a><b><i><p>' ); //limpo a variavel valor, retirando possíveis funções do PHP ou tags HTML que possam ser prejudiciais ao sistema, mas permitindo algumas que podem ser uteis em determinados casos

		
		//valida utilizando uma função buil-ip do PHP
		if(in_array($tipo, $builtin))
		{
			if( eval($tipo.'('.$valor.');') )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else if( preg_match( $$padrao, $valor ) )
		{
			 
			//podemos validar melhor alguns dados
			switch( $tipo )
			{
				case 'CPF':
					if ( !validCPF( $valor ) )
						return false;
					break;
					//você pode por um 'case' para cada tipo de dado que quiser validar com alguma formula especifica
				default:
					break;
			}
			 
			return true;
		}
		else
			return false;
	}

	/***********************************************
	 função criada por: Jefferson Estanislau da Silva, disponibilizado no endereço: http://www.vivaolinux.com.br/scripts/verScript.php?codigo=401
	 alterada por: Cauan Cabral, cauancabral.net em 10/02/2008
	 */
	protected function validCPF($cpf)
	{
		$nulos = array('12345678909','11111111111','22222222222','33333333333',
	                 '44444444444','55555555555','66666666666','77777777777',
	                 '88888888888','99999999999','00000000000');
		/* Retira todos os caracteres que nao sejam 0-9 */
		$cpf = preg_replace('/[^0-9]/', '', $cpf);
		 
		/*Retorna falso se houver letras no cpf */
		if (!(preg_match('/[0-9]/',$cpf)))
		return 0;
		 
		/* Retorna falso se o cpf for nulo */
		if( in_array($cpf, $nulos) )
		return 0;
		 
		/*Calcula o penúltimo dígito verificador*/
		$acum=0;
		for($i=0; $i<9; $i++) {
			$acum+= $cpf[$i]*(10-$i);
		}

		$x = $acum % 11;
		$acum = ($x>1) ? (11 - $x) : 0;
		/* Retorna falso se o digito calculado eh diferente do passado na string */
		if ($acum != $cpf[9]){
			return 0;
		}
		/*Calcula o último dígito verificador*/
		$acum=0;
		for ($i=0; $i<10; $i++){
			$acum+= $cpf[$i]*(11-$i);
		}

		$x=$acum % 11;
		$acum = ($x > 1) ? (11-$x) : 0;
		/* Retorna falso se o digito calculado eh diferente do passado na string */
		if ( $acum != $cpf[10]){
			return 0;
		}
		/* Retorna verdadeiro se o cpf eh valido */
		return 1;
	}

	static public function limpa($valor)
	{
		if( !empty($valor) )
		{
			if (!get_magic_quotes_gpc()) {
				return addslashes(strip_tags(htmlentities(trim($valor))));
			} else {
				return strip_tags(htmlentities(trim($valor)));
			}
		}
		return false;
	}

}