<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use PhpSpec\Exception\Example\FailureException;
use Prooph\Common\Messaging\DomainEvent;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;

class Failure extends FailureException
{
    public static function expected($subject, $text, ...$arguments)
    {
        $text = sprintf($text, ...$arguments);
        $subjectText = $subject instanceof AggregateRoot
            ? self::aggregateToString($subject)
            : 'the event store';

        return new self("Expected $subjectText $text");
    }

    protected static function aggregateToString($aggregate): string
    {
        return sprintf(
            '[%s:%s]',
            get_class($aggregate),
            (string) AggregateRootDecorator::newInstance()->extractAggregateId($aggregate)
        );
    }

    protected static function argumentToString($eventOrClassName): string
    {
        if (is_callable($eventOrClassName)) {
            return 'an event identified by a callback';
        }
        return $eventOrClassName instanceof DomainEvent
            ? '[' . get_class($eventOrClassName) . ':' . json_encode($eventOrClassName->payload()) . ']'
            : (string) $eventOrClassName;
    }
}
