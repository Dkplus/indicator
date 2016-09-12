<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

use DateTime;

class IssueProjection
{
    /** @var string */
    public $id;

    /** @var string */
    public $title;

    /** @var string */
    public $text;

    /** @var string */
    public $state;

    /** @var string */
    public $type;

    /** @var string */
    public $issueNumber;

    /** @var string */
    public $externalServiceId;

    /** @var DateTime */
    public $updatedAt;

    /** @var CustomerProjection */
    public $reporter;
}
