<?php

namespace form\formBundle\Billet;

class formBillet
{
	public function getBillet($billet)
	{
		if($billet)
		{
			return "demi-journée";
		}
		else
		{
			return "journée";
		}
	}
}