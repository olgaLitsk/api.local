<?php
namespace MyApp\Models\ORM;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Authors
{
    /**
     * @var int
     */
//    private $author_id;
    /**
     * @var string
     */
    private $firstname;
    /**
     * @var string
     */
    private $lastname;
    /**
     * @var string
     */
    private $about;

    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('firstname', new Assert\NotBlank());
        $metadata->addPropertyConstraint('lastname', new Assert\NotBlank());
        $metadata->addPropertyConstraint('about', new Assert\Length(array('min' => 5)));
    }
    /**
     * @ORM\Entity @ORM\Table(name="products")
     **/
    /**
     * Get author_id
     *
     * @return integer
     */
    public function getAuthorId() {
        return $this->author_id;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }
    /**
     * Set title
     *
     * @param string $firstname
     *
     * @return string
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function getAbout() {
        return $this->about;
    }

    public function setAbout($about) {
        $this->about = $about;
    }

}