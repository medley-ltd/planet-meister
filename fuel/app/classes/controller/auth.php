<?php
class Controller_Auth extends Controller
{
	private $_config = null;

	public function before()
	{
		Package::load( 'opauth' );

            if ( !isset( $this->_config ) )
		{
			$this->_config = Config::load( 'opauth', 'opauth' );
		}
	}

	public function action_login( $_provider = null )
	{
		if ( array_key_exists( Inflector::humanize( $_provider ), Arr::get( $this->_config, 'Strategy' ) ) )
		{
			$_oauth = new Opauth( $this->_config, true );
		}
		else
		{
			return Response::forge( 'Strategy not supported' );
		}
	}

	public function action_callback()
	{
		$_opauth = new Opauth( $this->_config, false );
		 
		switch ( $_opauth->env['callback_transport'] )
		{
			case 'session':
				session_start();
				$response = $_SESSION['opauth'];
				unset( $_SESSION['opauth'] );
				break;
		}
		 
		if ( array_key_exists( 'error', $response ) )
		{
			echo '<strong style="color: red;">Authentication error: </strong> Opauth returns error auth response.' . "\n";
		}
		else
		{
			if ( empty( $response['auth'] ) 
				|| empty( $response['timestamp'] ) 
				|| empty( $response['signature'] ) 
				|| empty( $response['auth']['provider'] ) 
				|| empty( $response['auth']['uid'] ) )
			{
				echo '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.' . "\n";
			}
			elseif ( !$_opauth->validate( sha1( print_r($response['auth'], true ) ), $response['timestamp'], $response['signature'], $reason ) )
			{
				echo '<strong style="color: red;">Invalid auth response: </strong>' . $reason . ".\n";
			}
			else
			{
				echo '<strong style="color: green;">OK: </strong>Auth response is validated.'."\n";
			}
		}
		 
		return Response::forge( var_dump( $response ) );
	}
}