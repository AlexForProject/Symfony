<?php

namespace form\formBundle\Prix;

use Symfony\Component\Validator\Constraints\DateTime;

class formPrix
{
	public function getPrix($date, $reduit, $duree)
	{
		if($duree == 1)
		{
			$multiplicateur = 0.75;
		}
		else
		{
			$multiplicateur = 1;
		}
		$ajd = new \DateTime();
		$resultat = $ajd->diff($date)->format('%Y');

		if($reduit == 1)
		{
			return 10;
		}
		else
		{
			switch ($resultat)
			{
				case ($resultat < 4):
				return 0*$multiplicateur;
				break;

				case($resultat > 4 && $resultat < 12):
				return 8*$multiplicateur;
				break;

				case($resultat > 12 && $resultat < 60):
				return 16*$multiplicateur;
				break;

				case($resultat > 60):
				return 12*$multiplicateur;
				break;
			}
		}

	}

	public function getBillet($bool)
	{
		if($bool == 1)
		{
			return "demi-journée";
		}
		else
		{
			return "journée";
		}
	}
}