<?php
namespace MyApp\Models\ORM;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User
{
    /**
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    private $user_id;


    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="`customer`",orphanRemoval=true)
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /** @ORM\Column(type="string") * */
    private $firstname;

    /** @ORM\Column(type="string") * */
    private $lastname;

    /** @ORM\Column(type="string") * */
    private $email;

    /** @ORM\Column(type="string") * */
    private $phonenumber;

    /** @ORM\Column(type="string") * */
    private $roles;

    /** @ORM\Column(type="string") * */
    private $username;

    /** @ORM\Column(type="string") * */
    private $password;

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('firstname', new Assert\NotBlank());
        $metadata->addPropertyConstraint('lastname', new Assert\NotBlank());
        $metadata->addPropertyConstraint('email', new Assert\Email());
        $metadata->addPropertyConstraint('phonenumber', new Assert\NotBlank());
        $metadata->addPropertyConstraint('roles', new Assert\NotBlank());
        $metadata->addPropertyConstraint('username', new Assert\NotBlank());
        $metadata->addPropertyConstraint('password', new Assert\NotBlank());
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPhonenumber()
    {
        return $this->phonenumber;
    }

    public function setPhonenumber($phonenumber)
    {
        $this->phonenumber = $phonenumber;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

}