<?php

// Kickstart the framework
$f3=require('lib/base.php');

$f3->set('DEBUG',1);
if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

$db = new DB\SQL(
	'mysql:host=localhost; port=3306; dbname=gleb', 'root', '');

// Load configuration
$f3->config('config.ini');

$f3->route('GET /',
	function($f3) {
		$f3->set('content','welcome.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('POST /bron',
	function($f3) use ($db) {
			$userMapper = new \DB\SQL\Mapper($db, 'admin');
			$auth = new \Auth($userMapper, array('id' =>
				'login', 'pw' => 'pass'));
			$login_result = $auth->login($f3->get('POST.login_aut'), 
				$f3->get('POST.pass_aut'));
			if ($login_result) {
				$f3->set('SESSION.login_aut',
				$f3->get('POST.login_aut'));
				$f3->reroute('/admin');
			}
			else {
				$f3->set('content','welcome.htm');
				echo View::instance()->render('layout.htm');
			}
		}		
);

$f3->route('GET /admin',
	function ($f3) {
		$f3->set('content','admin.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /logout',
	function ($f3) {
		$f3->set('SESSION.login_aut', 0);
		$f3->reroute('/');
	}
);

$f3->route('GET /contacts',
	function ($f3) {
		$f3->set('content','contacts.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('POST /contacts',
		function($f3,$params) use ($db) {
		$newUser = new DB\SQL\Mapper($db,'users');		
		$newUser->name = $f3->get('POST.name_user');
		$newUser->mail = $f3->get('POST.mail_user');
		$newUser->tel = $f3->get('POST.tel_user');
		$newUser->comment = $f3->get('POST.comments_user');
		
		$newUser->save();
		$f3->reroute('/contacts');
	}
);

$f3->route('GET /layout',
	function ($f3) {
		$f3->set('content','welcome.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /klav',
	function ($f3) use ($db) {
		$klavs = new DB\SQL\Mapper($db, 'klav');
		$klavs = $klavs->find();
		$f3->set('klavs', $klavs);

		$f3->set('content','klav.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /mih',
	function ($f3) use ($db) {
		$mihs = new DB\SQL\Mapper($db, 'mih');
		$mihs = $mihs->find();
		$f3->set('mihs', $mihs);

		$f3->set('content','mih.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /nab',
	function ($f3) use ($db) {
		$nabs = new DB\SQL\Mapper($db, 'nab');
		$nabs = $nabs->find();
		$f3->set('nabs', $nabs);

		$f3->set('content','nab.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /admin',
	function($f3) use ($db) {
		$admins = new DB\SQL\Mapper($db, 'admin');
		$admins = $admins->find();
		$f3->set('admins', $admins);
		$f3->set('content', 'admin.htm');
		echo View::instance()->render('layout.htm');
	}
);

$f3->route('GET /admin/delete/@id',
	function($f3,$params) use ($db) {
		$admin = new DB\SQL\Mapper($db,'admin');
		$admin = $admin->load(['id=?', $params['id']]);
		$admin->erase();
		$f3->reroute('/admin');
	}
);

$f3->route('GET /korz',
	function($f3) use ($db) {
		$korzs = new DB\SQL\Mapper($db, 'korz');
		$korzs = $korzs->find();
		$f3->set('korzs', $korzs);
		$f3->set('content', 'korz.htm');
		echo View::instance()->render('layout.htm');
	}
	);

$f3->route('POST /admin/add',
		function($f3,$params) use ($db) {
		$newUser = new DB\SQL\Mapper($db,'admin');		
		$newUser->login = $f3->get('POST.login_admin');
		$newUser->pass = $f3->get('POST.pass_admin');
		
		$newUser->save();
		$f3->reroute('/admin');
	}
);

$f3->route('POST /zakaz',
		function($f3,$params) use ($db) {
		$newUser = new DB\SQL\Mapper($db,'zakaz');		
		$newUser->name = $f3->get('POST.name_zakaz');
		$newUser->art = $f3->get('POST.art_zakaz');
		$newUser->comment = $f3->get('POST.comment_zakaz');
		
		$newUser->save();
		$f3->reroute('/');
	}
);

$f3->route('POST /klavkorz/@id',
		function($f3,$params) use ($db) {
		$klav = new DB\SQL\Mapper($db,'klav');	
		$klav = $klav->load(['id=?', $params['id']]);	

		$newKorz = new DB\SQL\Mapper($db,'korz');	
		$newKorz->name = $klav->name;
		$newKorz->photo = $klav->photo;
		$newKorz->cena = $klav->cena;
		$newKorz->opis = $klav->opis;
		
		$newKorz->save();
		$f3->reroute('/klav');
	}
);

$f3->route('POST /nabkorz/@id',
		function($f3,$params) use ($db) {
		$nab = new DB\SQL\Mapper($db,'nab');	
		$nab = $nab->load(['id=?', $params['id']]);	

		$newKorz = new DB\SQL\Mapper($db,'korz');	
		$newKorz->name = $nab->name;
		$newKorz->photo = $nab->photo;
		$newKorz->cena = $nab->cena;
		$newKorz->opis = $nab->opis;
		
		$newKorz->save();
		$f3->reroute('/nab');
	}
);

$f3->route('POST /mihkorz/@id',
		function($f3,$params) use ($db) {
		$mih = new DB\SQL\Mapper($db,'mih');	
		$mih = $mih->load(['id=?', $params['id']]);	

		$newKorz = new DB\SQL\Mapper($db,'korz');	
		$newKorz->name = $mih->name;
		$newKorz->photo = $mih->photo;
		$newKorz->cena = $mih->cena;
		$newKorz->opis = $mih->opis;
		
		$newKorz->save();
		$f3->reroute('/mih');
	}
);



$f3->run();
