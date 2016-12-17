<?php

namespace form\formBundle\Controller;

use form\formBundle\Entity\jour;
use form\formBundle\Entity\individu;
use form\formBundle\Entity\commande;
use form\formBundle\Form\jourType;
use form\formBundle\Form\individuType;
use form\formBundle\form\commandeType;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\DateTime;


class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
      $session = new Session();
    	$commande = new commande();

    	$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $commande, array(
        'method' => 'POST'
        ));
    	$formBuilder
      	->add('date',           DateType::class, array('years'=>range(date('Y'), date('Y')+5), 'data' => new \DateTime("tomorrow")))
        ->add('billet',         CheckboxType::class, array(
          'label' => 'Préférer la demi-journée au lieu de la journée',
          'required' => false,))
      	->add('commander',      SubmitType::class);

    	$form = $formBuilder->getForm();

      if($request->isMethod('POST'))
      {
        //on choppe la requête
        $form->handleRequest($request);
        // si on a soumis le formulaire et est valide, on check la date et le nombre.
        if($form->isSubmitted() && $form->isValid())
        {
          $date=$form->get('date')->getData();
          $billet = $form->get('billet')->getData();
          $session->set('date', $date);
          
          $servicePrix = $this->container->get('form_form.prix');
          $typeBillet = $servicePrix->getBillet($billet);
          $session->set('billet', $typeBillet);

          $repositoryCommande=$this->getDoctrine()->getManager()->getRepository('formformBundle:commande');
          
          $totalCommande=$repositoryCommande->getQuota($date);

          //check la date
          $ajd = new \DateTime();

          if($date < $ajd)
          {
            return $this->render('formformBundle:Default:error.html.twig');
          }

          if($totalCommande>=1000)
          {
            return $this->render('formformBundle:Default:error.html.twig');
          }

          $em=$this->getDoctrine()->getManager();
          $commande->setNbPlace(0);
          $commande->setPrix(0);
          $em->persist($commande);
          $em->flush($commande);
          $idCommande = $commande->getId();
          $session->set('id', $idCommande);
          return $this->redirectToRoute('formform_personnes');
        }

      }
      return $this->render('formformBundle:Default:index.html.twig', array('form'=>$form->createView()));
    }


    public function personnesAction(Request $request)
    {
      $session=$this->get('session');

      $date=$session->get('date');
      $typeBillet=$session->get('billet');
      $id=$session->get('id');
      $ajd=$session->get('ajd');
      $servicePrix = $this->container->get('form_form.prix');


      $commandeRepository=$this->getDoctrine()->getManager()->getRepository('formformBundle:commande');
      $commande = $commandeRepository->find($id);

      $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $commande, array(
        'method' => 'POST'
        ));

      $formBuilder
      ->add('individus',    CollectionType::class, array(
        'entry_type'    => individuType::class,
        'allow_add'     => true,
        'allow_delete'  => true))
      ->add('commander',      SubmitType::class);
      
      $form=$formBuilder->getForm();
      
      if($request->isMethod('POST'))
      {
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          $personnes = $form->get('individus')->getData();
          $session->set('individus', $personnes);
          $nbPlace = count($personnes);
          $session->set('nbPlace', $nbPlace);

          $em=$this->getDoctrine()->getManager();

          for($i =0 ; $i < $nbPlace ; $i++)
          {
            $personnes[$i]->setCommande($commande);
            $reduit =  $personnes[$i]->getReduit();
            $prix = $servicePrix->getPrix($personnes[$i]->getAnniversaire(), $reduit);
            $personnes[$i]->setPrix($prix);
            $commande->addIndividus($personnes[$i]);
            $em->persist($personnes[$i]);
          }
          $em->flush($personnes[$i]);
          $commande->setNbPlace($nbPlace+1);
          $em->persist($commande);
          $em->flush($commande);
          return $this->redirectToRoute('formform_recapitulatif');
        }
      }

      return $this->render('formformBundle:Default:personnes.html.twig', array('form' => $form->createView(),'date'=>$date, 'billet'=>$typeBillet));

    }

    public function recapitulatifAction()
    {
      $session=$this->get('session');

      $date=$session->get('date');
      $billet=$session->get('billet');
      $id=$session->get('id');
      $commande = $this->getDoctrine()->getManager()->getRepository('formformBundle:commande')->find($id);
      $personnes = $commande->getIndividus();
      $nbPlace = count($personnes);
      $prixTotal = 0;

      for($i=0 ; $i<$nbPlace ; $i++)
      {
        $reduit = $personnes[$i]->getReduit();
        $prixTotal=($prixTotal + $personnes[$i]->getPrix($date, $reduit));  
      }

    

      return $this->render('formformBundle:Default:recapitulatif.html.twig', array('date'=>$date, 'nbPlace'=>$nbPlace, 'individus'=>$personnes, 'billet'=>$billet, 'nbPlace'=>$nbPlace, 'prixTotal'=>$prixTotal));
    }

    public function commandeAction()
    {
      return $this->render('formformBundle:Default:commande.html.twig');
    }

}
