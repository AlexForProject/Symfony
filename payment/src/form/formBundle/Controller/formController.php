<?php

namespace form\formBundle\Controller;

use form\formBundle\Entity\Advert;
use form\formBundle\Entity\identifiant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class DefaultController extends Controller
{
    public function indexAction()
    {

        return $this->render('formformBundle:Default:index.html.twig');

    }

    public function identifiantAction()
    {
    	$date = $_POST['date'];
    	$nbPlace = $_POST['nbPlace'];

    }

}
