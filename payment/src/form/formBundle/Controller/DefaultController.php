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

      global $commandeGlobale;
      $commandeGlobale = new commande();

    	$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $commande, array(
        'method' => 'POST'
        ));
    	$formBuilder
       ->add('date', DateType::class, array(
          'widget' => 'single_text',
          'html5' => false,
          'attr' => ['class' => 'datepicker', 'glyphicon glyphicon-th', 'data-provide' => 'datepicker'],
          ))
        ->add('billet',         CheckboxType::class, array(
          'label' => 'Demi-journée',
          'required' => false,))
      	->add('commander',      SubmitType::class);

    	$form = $formBuilder->getForm();

      if($request->isMethod('POST'))
      {

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          $ajd = new \Datetime();
          $ajdAnnee = $ajd->format('Y');
          $ajdMois = $ajd->format('m');
          $ajdJour = $ajd->format('d');
          $ajdHeure = $ajd->format('H');
 
          $date=$form->get('date')->getData();
          $interval = $date->diff($ajd);
          $billet = $form->get('billet')->getData();

          $interval = $ajd->diff($date)->format('%R%a');
          $session->set('daysInterval', $interval);
          
          $servicePrix = $this->container->get('form_form.prix');
          $serviceBillet = $this->container->get('form_form.billet');
          $typeBillet = $serviceBillet->getBillet($billet);
          //$session->set('billet', $typeBillet);

          $repositoryCommande=$this->getDoctrine()->getManager()->getRepository('formformBundle:commande');
          $jourAnnee = $date->format('w');
          $jourMois = $date->format('d');
          $mois = $date->format('m');
          $annee = $date->format('Y');

          if(($annee == $ajdAnnee) && ($mois == $ajdMois) && ($jourMois == $ajdJour) && ($ajdHeure >= 14))
          {
            $commande->setBillet(1);
          }
          

          if(($jourMois == 25 && $mois == 12) || ($jourMois == 01 && $mois = 05) || ($jourMois == 01 && $mois == 11) || $jourAnnee == 2 || $interval < 0)
          {
            return $this->render('formformBundle:Default:errorDate.html.twig');
          }

          $totalCommande=$repositoryCommande->getQuota($date);

          if($totalCommande>=1000)
          {
            return $this->render('formformBundle:Default:errorNombre.html.twig');
          }

          $em=$this->getDoctrine()->getManager();
          $commande->setNbPlace(0);
          $commande->setPrix(0);
          $commande->setEmail("");
          $commande->setCodeBarre(0);
          $em->persist($commande);
          $em->flush($commande);
          $idCommande = $commande->getId();
          $session->set('id', $idCommande);

          $commande->setNbPlace(0);
          $commande->setPrix(0);
     
          return $this->redirectToRoute('formform_personnes');
        }

      }
      return $this->render('formformBundle:Default:index.html.twig', array('form'=>$form->createView()));
    }


    public function personnesAction(Request $request)
    {
      $session=$this->get('session');

      $id=$session->get('id');
      $servicePrix = $this->container->get('form_form.prix');
      $serviceBillet = $this->container->get('form_form.billet');

      $commandeRepository=$this->getDoctrine()->getManager()->getRepository('formformBundle:commande');
      $commande = $commandeRepository->find($id);
      $date = $commande->getDate();
      $typeBillet=$commande->getBillet();
      $billetString = $serviceBillet->getBillet($typeBillet);

      $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $commande, array(
        'method' => 'POST'
        ));

      $formBuilder
      ->add('email',         TextType::class)
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
          $mailClient=$form->get('email')->getData();
          $commande->setEmail($mailClient);
          $nbPlace = count($personnes);

          $em=$this->getDoctrine()->getManager();
          $prixTotal=0;
          $repositoryIndividu=$this->getDoctrine()->getManager()->getRepository('formformBundle:individu');

          $prixBebe=$this->getParameter('prix_bebe');
          $prixEnfant=$this->getParameter('prix_enfant');
          $prixAdulte=$this->getParameter('prix_adulte');
          $prixRetraite=$this->getParameter('prix_retraite');
          $listePrix=array($prixBebe, $prixEnfant, $prixAdulte, $prixRetraite);

          foreach ($personnes as $individu)
            {
              $session->set('individu', $individu);
              $individu->setCommande($commande);
              $prix = $servicePrix->getPrix($individu->getAnniversaire(), $individu->getReduit(), $commande->getBillet(), $commande->getDate(), $listePrix);
              $individu->setPrix($prix);
              $commande->addIndividus($individu);
              $em->persist($individu);
              $prixTotal += $prix;             
            }

          $commande->setPrix($prixTotal);
          $commande->setNbPlace($nbPlace);
          $em->persist($commande);
          $em->flush();

          return $this->redirectToRoute('formform_recapitulatif');
        }
      }

      return $this->render('formformBundle:Default:personnes.html.twig', array('form' => $form->createView(),'date'=>$date, 'billet'=>$billetString));

    }

    public function recapitulatifAction()
    {
      $session=$this->get('session');

      $id=$session->get('id');

      $commande = $this->getDoctrine()->getManager()->getRepository('formformBundle:commande')->find($id);
      $personnes = $commande->getIndividus();
      
      $date=$commande->getdate();
      $billet=$commande->getBillet();
      $prixTotal=$commande->getPrix();

      $nbPlace=$commande->getNbPlace();

      if($billet == 1)$billet= "demi-journée";
      else $billet = "journée";
      $mailClient = $commande->getEmail();
      return $this->render('formformBundle:Default:recapitulatif.html.twig', array('mailClient'=>$mailClient ,'date'=>$date, 'nbPlace'=>$nbPlace, 'personnes'=>$personnes, 'billet'=>$billet, 'prixTotal'=>$prixTotal));
    }

    public function validationAction(Request $request)
    {
      $session = $this->get('session');
      $id=$session->get('id');

      $commande = $this->getDoctrine()->getManager()->getRepository('formformBundle:commande')->find($id);      
      $individus = $commande->getIndividus();
      $forBillet = $individus[0];
      $mailClient=$commande->getEmail();
      $typeBillet=$commande->getBillet();
      
      $serviceBillet = $this->container->get('form_form.billet');
      $billetString = $serviceBillet->getBillet($typeBillet);

      $prix = $commande->getPrix();
      $prixCent = ($prix*100);

      $date = $commande->getDate();
      $annee = $date->format('Y');
      $mois = $date->format('m');
      $jour = $date->format('d');

      $barreCode = intval($jour+$mois+$annee+$id);

      
      \Stripe\Stripe::setApiKey("sk_test_mHA783JyxDRB9hmTDRd2PhLR");
      $token = $_POST['stripeToken'];

      try {
        $charge = \Stripe\Charge::create(array(
          "amount"      => $prixCent, 
          "currency"    => "eur",
          "source"      => $token,
          "description" => "Achat billet"
          ));

      $message = \Swift_Message::newInstance()
          ->setSubject('Votre commande')
          ->setFrom('project.louvre@gmail.com')
          ->setTo($commande->getEmail())
          ->setBody(
            $this->renderView('formformBundle:Emails:registration.html.twig', array('billet' => $billetString,'barreCode'=>$barreCode,'personnes' => $individus, 'individu' => $forBillet, 'date' => $date, 'commande'=>$commande)),'text/html');
        $this->get('mailer')->send($message);

        $commande->setCodeBarre($barreCode);

        $em=$this->getDoctrine()->getManager();
        $em->persist($commande);
        $em->flush();
        return $this->render("formformBundle:Default:validation.html.twig", array('prix'=>$prix));

      } catch(\Stripe\Error\Card $e) {
        
      }
    }

    public function purgeAction()
    {
      $em=$this->getDoctrine()->getManager();

      $dateReference = date('Y-m-d',strtotime("-1 days"));

      $repositoryCommande = $this->getDoctrine()->getManager()->getRepository('formformBundle:commande');
      $repositoryIndividu = $this->getDoctrine()->getManager()->getRepository('formformBundle:individu');
      $listeCommande = $repositoryCommande->getCommandes($dateReference);

      foreach ($listeCommande as $commande)
      {
        $individus = $repositoryIndividu->getIndividu($commande);
        foreach ($individus as $individu) {
        $em->remove($individu);
        }
      }
      $em->flush();
      foreach ($listeCommande as $commande)
      {
        $em->remove($commande);
      }
      $em->flush();
    return $this->render("formformBundle:Default:supprime.html.twig");
    }

}
