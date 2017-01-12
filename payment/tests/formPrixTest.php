<?php

class formPrixTest extends PHPUnit_Framework_TestCase{

	public function testBillet(){

		$bool = 0;
		$this->assertEquals("journée", form\formBundle\Billet\formBillet::getBillet($bool));
	}

	public function testreduit(){
		
		$anniversaire = new \Datetime("1992-04-13");
		$commande = new \Datetime("2017-01-22");
		$reduit = 1;
		$duree = 0;
		$prix = array(0, 8, 16, 12);
		$this->assertEquals(16, form\formBundle\Prix\formPrix::getPrix($anniversaire, $reduit, $duree, $commande, $prix));
		$duree = 1;
	}

	public function testPrix1(){
		$anniversaire = new \Datetime("2005-04-13");
		$commande = new \Datetime("2017-01-22");
		$reduit = 0;
		$duree = 1;
		$this->assertEquals(12, form\formBundle\Prix\formPrix::getPrix($anniversaire, $reduit, $duree, $commande, $prix));
	}
}