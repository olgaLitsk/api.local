<?php
namespace MyApp\Models\ORM;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Book
 *
 * @ORM\Table(name="books")
 * @ORM\Entity
 */
class Book
{
    /**
     * @ORM\Column(name="book_id", type="integer")
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    private $book_id;

    /** @ORM\Column(type="string") * */
    private $title;

    /** @ORM\Column(type="string") * */
    private $shortdescription;

    /** @ORM\Column(type="float") * */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="books")
     * @ORM\JoinColumn(name="category", nullable=false, referencedColumnName="category_id")
     */
    private $category;

    /**
     * Many Book have Many Authors.
     * @ORM\ManyToMany(targetEntity="Author")
     * @ORM\JoinTable(name="authors_books",
     *      joinColumns={@ORM\JoinColumn(name="book", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="author", referencedColumnName="author_id", unique=true)}
     *      )
     */
    private $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

//    public function addAuthor(Author $author)
//    {
//        $author->addBook($this); // synchronously updating inverse side
//        $this->authors[] = $author;
//    }

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new Assert\NotBlank());
        $metadata->addPropertyConstraint('shortdescription', new Assert\NotBlank());
        $metadata->addPropertyConstraint('price', new Assert\Type('float'));
        $metadata->addPropertyConstraint('price', new Assert\Type('float'));
    }

    public function getBookId()
    {
        return $this->book_id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getShortdescription()
    {
        return $this->shortdescription;
    }

    public function setShortdescription($shortdescription)
    {
        $this->shortdescription = $shortdescription;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getCategory():Category
    {
        return $this->category;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

}