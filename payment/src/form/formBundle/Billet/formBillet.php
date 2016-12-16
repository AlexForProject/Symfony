<?php

namespace form\formBundle\Billet\formBillet;

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