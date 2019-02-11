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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="users")
     * @ORM\JoinColumn(name="`user`", nullable=false, referencedColumnName="user_id")
     */
    private $user;

    /**
     * Many Book have Many Orders.
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="books_orders",
     *      joinColumns={@ORM\JoinColumn(name="order", referencedColumnName="order_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="`user`", referencedColumnName="user_id", unique=true)}
     *      )
     */
    private $users;//проверить не надо ли заменить на ордер

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


    /** @ORM\Column(type="string") * */
    private $orderdate;

    /** @ORM\Column(type="string") * */
    private $status;

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('orderdate', new Assert\Type('string'));
        $metadata->addPropertyConstraint('user', new Assert\Type('integer'));
        $metadata->addPropertyConstraint('status', new Assert\Type('string'));
    }

    public function addBook(Book $book)
    {
        $book->addOrder($this); // synchronously updating inverse side
        $this->books[] = $book;
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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
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
