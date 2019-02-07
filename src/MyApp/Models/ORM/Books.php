<?php
namespace Models\ORM;

class Books
{
    /**
     * @var int
     */
    private $book_id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $shortdescription;
    /**
     * @var double
     */
    private $price;
    /**
     * @var integer
     */
    private $category;

    /**
     * Get book_id
     *
     * @return integer
     */
    public function getBookId() {
        return $this->book_id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return string
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getShortdescription() {
        return $this->shortdescription;
    }

    public function setShortdescription($shortdescription) {
        $this->shortdescription = $shortdescription;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->shortdescription = $price;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }
}