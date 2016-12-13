<?php

namespace form\formBundle\Controller;

use form\formBundle\Entity\jour;
use form\formBundle\Entity\individu;
use form\formBundle\Form\jourType;
use form\formBundle\Form\individuType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\Date;


class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
      $session = new Session();
    	$jour = new jour();
    	$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $jour, array(
        'method' => 'POST'
        ));
    	$formBuilder
      	->add('date',           DateType::class)
      	->add('commander',      SubmitType::class);

    	$form = $formBuilder->getForm();

      if($request->isMethod('POST'))
      {

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          $date=$form->get('date')->getData();
          $session->set('Date', $date);

          $jourRecup = new jour();
          $repository=$this->getDoctrine()->getManager()->getRepository('formformBundle:jour');
          $jourRecup= $repository->findOneByDate($date);

          if(null == $jourRecup)
          {
            $jourAjout = new jour();
            $jourAjout->setDate($date);
            $jourAjout->setNbPersonne(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($jourAjout);
            $em->flush();
            //Condition de récupération effectuée.
            $jourRecup= $repository->findOneByDate($date);
          }

          $nombre = $jourRecup -> getNbPersonne();
          if($nombre>=1000)
          {
            $validationNombre = false;
          }
          else
          {
            $validationNombre = true;
          }

          $ajd = new Date();
          if($date < $ajd)
          {
            $validationJour = false;
          }
          else
          {
            $validationJour = true;
          }

          if($validationJour==true && $validationNombre==true)
          {
            return $this->redirectToRoute('formform_nom');
          }
        }

      }
      return $this->render('formformBundle:Default:index.html.twig', array('form'=>$form->createView()));
  }
    public function identifiantAction(Request $request)
    {
      $session=$this->get('session');
      $date = $session->get('Date');


      return $this->render('formformBundle:Default:identifiant.html.twig', array('date'=>$date));
    }

    public function commandeAction()
    {
      return $this->render('formformBundle:Default:commande.html.twig');
    }
}
