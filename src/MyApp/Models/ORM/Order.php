<?php
namespace MyApp\Models\ORM;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Order
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Order
{
    /**
     * @ORM\Column(name="order_id", type="integer")
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    private $order_id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="customer", nullable=false, referencedColumnName="user_id")
     */
    private $customer;

    /**
     * Many Order have Many Books.
     * @ORM\ManyToMany(targetEntity="Book")
     * @ORM\JoinTable(name="books_orders",
     *      joinColumns={@ORM\JoinColumn(name="`order`", referencedColumnName="order_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="book", referencedColumnName="book_id")}
     *      )
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /** @ORM\Column(type="string") * */
    private $orderdate;

    /** @ORM\Column(type="string") * */
    private $status;

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('orderdate', new Assert\Type('string'));
//        $metadata->addPropertyConstraint('customer', new Assert\Type('integer'));
        $metadata->addPropertyConstraint('status', new Assert\Type('string'));
    }
    public function setBook($books)
    {
        $this->books = new ArrayCollection($books);
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function getOrderdate()
    {
        return $this->orderdate;
    }

    public function setOrderdate($orderdate)
    {
        $this->orderdate = $orderdate;
    }

    public function getUser():User
    {
        return $this->customer;
    }

    public function setUser(User $customer)
    {
        $this->customer = $customer;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}
