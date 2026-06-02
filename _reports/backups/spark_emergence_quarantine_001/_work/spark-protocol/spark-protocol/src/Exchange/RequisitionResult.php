<?php

declare(strict_types=1);

namespace Spark\Exchange;

use Spark\Model\Sheet;

/** Immutable result of a successful Exchange requisition. */
final class RequisitionResult
{
    /** @var Sheet */
    private $sheet;
    /** @var int */
    private $capacitySpent;
    /** @var string */
    private $description;

    public function __construct(Sheet $sheet, int $capacitySpent, string $description)
    {
        $this->sheet = $sheet;
        $this->capacitySpent = $capacitySpent;
        $this->description = $description;
    }

    public function sheet(): Sheet
    {
        return $this->sheet;
    }

    public function capacitySpent(): int
    {
        return $this->capacitySpent;
    }

    public function description(): string
    {
        return $this->description;
    }
}
