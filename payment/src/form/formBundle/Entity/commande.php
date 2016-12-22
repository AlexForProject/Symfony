<?php

namespace form\formBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="form\formBundle\Repository\commandeRepository")
 */
class commande
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="billet", type="boolean")
     */
    private $billet;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPlace", type="integer")
     */
    private $nbPlace;

    /**
     * @var int
     *
     * @ORM\Column(name="prix", type="integer")
     */
    private $prix;

    /**
     *
     * @ORM\OneToMany(targetEntity="form\formBundle\Entity\individu", mappedBy="commande", cascade={"persist"}) 
     * @ORM\JoinColumn(nullable=false)
     */
    private $individus;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     * )
     */
     protected $email;

    /**
     * @var int
     *
     * @ORM\Column(name="codeBarre", type="integer")
     */
    private $codeBarre;

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
     * Set date
     *
     * @param \Date $date
     *
     * @return commande
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \Date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set billet
     *
     * @param string $billet
     *
     * @return commande
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
     * Set nbPlace
     *
     * @param integer $nbPlace
     *
     * @return commande
     */
    public function setNbPlace($nbPlace)
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    /**
     * Get nbPlace
     *
     * @return int
     */
    public function getNbPlace()
    {
        return $this->nbPlace;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return commande
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return int
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->individus = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * Add individus
     *
     * @param \form\formBundle\Entity\individu $individus
     *
     * @return commande
     */
    public function addIndividus(\form\formBundle\Entity\individu $individus)
    {
        $this->individus[] = $individus;

        return $this;
    }

    /**
     * Remove individus
     *
     * @param \form\formBundle\Entity\individu $individus
     */
    public function removeIndividus(\form\formBundle\Entity\individu $individus)
    {
        $this->individus->removeElement($individus);
    }

    /**
     * Get individus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndividus()
    {
        return $this->individus;
    }

    /**
     * Set email
     *
     * @param \mail $email
     *
     * @return commande
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return \mail
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set codeBarre
     *
     * @param integer $codeBarre
     *
     * @return commande
     */
    public function setCodeBarre($codeBarre)
    {
        $this->codeBarre = $codeBarre;

        return $this;
    }

    /**
     * Get codeBarre
     *
     * @return integer
     */
    public function getCodeBarre()
    {
        return $this->codeBarre;
    }
}
