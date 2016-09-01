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

    /** @var DateTime */
    public $updatedAt;

    /** @var CustomerProjection */
    public $reporter;
}
