<?php 
	require_once 'classes/Validate.php';
	require_once 'classes/Input.php';
	require_once 'classes/DB.php';
	require_once 'classes/Token.php';
	require_once 'classes/Sanitize.php';
	require_once 'classes/Hash.php';
	require_once 'classes/User.php';
	require_once 'classes/Session.php';
	require_once 'classes/SendMail.php';
?>
<HTML lang="pl-PL">
<HEAD>
	<?php include_once 'inc.head.html'; ?>
</HEAD>
<BODY>
<?php
include 'inc.menu.html';
	$user = new User();
	if($user->isLoggedIn()) {
		header('Location: home.php');
	}
	
	if(Input::exists()) {
		
		if(Token::check(Input::get('token'))) {
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'username' => array(
					'required' => true,
					'min' => 3,
					'max' => 40,
					'unique' => 'users'
				),
				'password' => array(
					'required' => true,
					'min' => 4,
					'max' => 30,
					'strongPassword' => true
				),
				'password_again' => array(
					'required' => true,
					'matches' => 'password'
				),
				'name' => array(
					'required' => true,
					'min' => 2,
					'max' => 50,
					'isLetters' => true
				),
				'surname' => array(
					'required' => true,
					'min' => 3,
					'max' => 40,
					'isLetters' => true
				),
				'email' => array(
					'required' => true,
					'min' => 3,
					'max' => 40,
					'unique' => 'users'
				),
				'number_phone' => array(
					'required' => true,
					'min' => 7,
					'max' => 40,
					'unique' => 'users'
				),
				'date_birdth' => array(
					'required' => true,
					'date' => true
				),
				'city' => array(
					'required' => true,
					'min' => 3,
					'max' => 40,
					'isLetters' => true
				),
				'street' => array(
					'required' => true,
					'min' => 3,
					'max' => 40
				),
				'no_house' => array(
					'required' => true,
					'min' => 1,
					'max' => 40
				),
				'no_flat' => array(
					'max' => 10
				),
				'consent_rodo' => array(
					'required' => true
				)
			));
		
			if($validation->passed()) {
				$salt = Hash::salt(32);
				
				try {
					
					$user->create(array(
						'username' => Input::get('username'),
						'password' => Hash::make(Input::get('password'), $salt),
						'salt' => Sanitize::escape($salt),
						'password_date' => @date('Y-m-d H:i:s'),
						'name' => Input::get('name'),
						'surname' => Input::get('surname'),
						'email' => Input::get('email'),
						'number_phone' => Input::get('number_phone'),
						'date_birdth' => Input::get('date_birdth'),
						'city' => Input::get('city'),
						'street' => Input::get('street'),
						'no_house' => Input::get('no_house'),
						'no_flat' => (strlen(Input::get('no_house')) > 0) ? Input::get('no_house') : NULL,
						'joined' => @date('Y-m-d H:i:s'),
						'group' => 1,
						'consent_rodo' => Input::get('consent_rodo')
					));

					$HTML = '
					<HTML><HEAD></HEAD>
					<BODY>
						<p><h2>Witaj '. Input::get('name') .'!</h2></p>
						<p>Zapraszyam do zapoznania się z naszym portalem. Mamy dużo nowości i cały czas się orzwijamy. Zapraszyam do zapoznania się z naszym portalem. Mamy dużo nowości i cały czas się orzwijamy.Zapraszyam do zapoznania się z naszym portalem. Mamy dużo nowości i cały czas się orzwijamy.</p> <p>
						<h3>Założyciele portalu</h3>
						<h5>Mail: mail.test.app@wp.pl</h5>
						</p>
					</BODY>
					</HTML>
					';

					//send message for a new user
					$send = new SendMail(true);
					$send->createMessage( Input::get('email'), Input::get('Name') .' '. Input::get('surname'), 'Witaj w naszym portalu!', $HTML);
					
					Input::destroy('username');
					Input::destroy('password');
					Input::destroy('password_again');
					Input::destroy('name');
					Input::destroy('surname');
					Input::destroy('email');
					Input::destroy('number_phone');
					Input::destroy('date_birdth');
					Input::destroy('city');
					Input::destroy('street');
					Input::destroy('no_house');
					Input::destroy('no_flat');
					Input::destroy('consent_rodo');
					
					Session::flash('user_information', 'You have been registered and can now you log in!');
					header('Location: index.php');
				
				} catch(Exception $e) {
					die($e->getMessage());
				}
			} else {
				foreach($validation->errors() as $error => $details) {
					echo $details . '<br />';
				}
			}
		}
	}

?>

	<table>
	<form method="POST">
		<tr>
			<td>
				Login: <br/>
				<input type="text" name="username" value="<?php echo Sanitize::escape(Input::get('username')); ?>" />
			</td>
		</tr><tr>
			<td>
				Hasło: <br/>
				<input type="password" name="password" />
			</td>
		</tr><tr>
			<td>
				Powtórz hasło: <br/>
				<input type="password" name="password_again" />
			</td>
		</tr><tr>
			<td>
				Imię: <br/>
				<input type="text" name="name" value="<?php echo Sanitize::escape(Input::get('name')); ?>" />
				</td>
		</tr><tr>
			<td>
				Nazwisko: <br/>
				<input type="text" name="surname" value="<?php echo Sanitize::escape(Input::get('surname')); ?>" />
				</td>
		</tr><tr>
			<td>
				E-mail: <br/>
				<input type="email" name="email" value="<?php echo Sanitize::escape(Input::get('email')); ?>" />
				</td>
		</tr><tr>
			<td>
				Numer telefonu: <br/>
				<input type="text" name="number_phone" value="<?php echo Sanitize::escape(Input::get('number_phone')); ?>" />
				</td>
		</tr><tr>
			<td>
				Data urodzenia: <br/>
				<input type="date" name="date_birdth" value="<?php echo @date('Y-m-d'); ?>" />
				</td>
		</tr><tr>
			<td>
				Miasto: <br/>
				<input type="text" name="city" value="<?php echo Sanitize::escape(Input::get('city')); ?>" />
				</td>
		</tr><tr>
			<td>
				Ulica: <br/>
				<input type="text" name="street" value="<?php echo Sanitize::escape(Input::get('street')); ?>" />
				</td>
		</tr><tr>
			<td>
				Numer domu: <br/>
				<input type="text" name="no_house" value="<?php echo Sanitize::escape(Input::get('no_house')); ?>" />
				</td>
		</tr><tr>
			<td>
				Numer mieszkania: <br/>
				<input type="text" name="no_flat" value="<?php echo Sanitize::escape(Input::get('no_flat')); ?>" />
				</td>
		</tr><tr>
			<td>
				<input type="checkbox" name="consent_rodo" value="1" /> Akceptuję <a href="#">regulamin</a>
				</td>
		</tr><tr>
			<td>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
				<input type="submit" value="Zarejestruj" />
			</td>
		</tr>
	</form>
	</table>
	
	<?php
		Input::destroy('username');
		Input::destroy('password');
		Input::destroy('password_again');
		Input::destroy('name');
	?>
</BODY>
</HTML>
