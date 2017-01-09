<?php

namespace form\formBundle\Prix;

use Symfony\Component\Validator\Constraints\DateTime;

class formPrix
{
	public static function getPrix($date, $reduit, $duree, $commande, $prix)
	{
		if($duree == 1)
		{
			$multiplicateur = 0.75;
		}
		else
		{
			$multiplicateur = 1;
		}
		$resultat = $commande->diff($date)->format('%Y');

		if($reduit == 1)
		{
			return 10*$multiplicateur;
		}
		else
		{
			switch ($resultat)
			{
				case ($resultat < 4):
				return $prix[0]*$multiplicateur;
				break;

				case($resultat > 4 && $resultat <= 12):
				return $prix[1]*$multiplicateur;
				break;

				case($resultat > 12 && $resultat < 60):
				return $prix[2]*$multiplicateur;
				break;

				case($resultat >= 60):
				return $prix[3]*$multiplicateur;
				break;
			}
		}

	}
}