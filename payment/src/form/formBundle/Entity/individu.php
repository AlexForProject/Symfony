<?php

namespace form\formBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * individu
 *
 * @ORM\Table(name="individu")
 * @ORM\Entity(repositoryClass="form\formBundle\Repository\individuRepository")
 */
class individu
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reduit", type="boolean")
     */
    private $reduit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="anniversaire", type="date")
     */
    private $anniversaire;

    /**
    * @ORM\ManyToOne(targetEntity="form\formBundle\Entity\commande", inversedBy="individus", cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $commande;


    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return individu
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return individu
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set billet
     *
     * @param string $billet
     *
     * @return individu
     */
    public function setBillet($billet)
    {
        $this->billet = $billet;

        return $this;
    }

    /**
     * Get billet
     *
     * @return string
     */
    public function getBillet()
    {
        return $this->billet;
    }

    /**
     * Set anniversaire
     *
     * @param \DateTime $anniversaire
     *
     * @return individu
     */
    public function setAnniversaire($anniversaire)
    {
        $this->anniversaire = $anniversaire;

        return $this;
    }

    /**
     * Get anniversaire
     *
     * @return \DateTime
     */
    public function getAnniversaire()
    {
        return $this->anniversaire;
    }


    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return individu
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return integer
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set commande
     *
     * @param \OC\PlatformBundle\Entity\commande $commande
     *
     * @return individu
     */
    public function setCommande(\form\formBundle\Entity\commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \OC\PlatformBundle\Entity\commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set commandeId
     *
     * @param \form\formBundle\Entity\commande $commandeId
     *
     * @return individu
     */
    public function setCommandeId(\form\formBundle\Entity\commande $commandeId)
    {
        $this->commande_id = $commandeId;

        return $this;
    }

    /**
     * Get commandeId
     *
     * @return \form\formBundle\Entity\commande
     */
    public function getCommandeId()
    {
        return $this->commande_id;
    }

    /**
     * Set reduit
     *
     * @param boolean $reduit
     *
     * @return individu
     */
    public function setReduit($reduit)
    {
        $this->reduit = $reduit;

        return $this;
    }

    /**
     * Get reduit
     *
     * @return boolean
     */
    public function getReduit()
    {
        return $this->reduit;
    }
}
