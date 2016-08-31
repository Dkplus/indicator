<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\Event\CustomerWasRegistered;
use Prooph\EventSourcing\AggregateRoot;

class Customer extends AggregateRoot
{
    /** @var CustomerId */
    private $id;

    /** @var string */
    private $name;

    public static function register(CustomerId $id, string $name): self
    {
        $result = new self();
        $result->recordThat(CustomerWasRegistered::withIdAndName($id, $name));
        return $result;
    }

    protected function aggregateId()
    {
        return (string) $this->id;
    }

    public function reportIssue(IssueId $id, string $title, string $text): Issue
    {
        return Issue::reportWith($id, $this->id, $title, $text);
    }

    protected function whenCustomerWasRegistered(CustomerWasRegistered $data)
    {
        $this->id = CustomerId::fromString($data->aggregateId());
        $this->name = $data->name();
    }

    public function id()
    {
        return $this->id;
    }
}
