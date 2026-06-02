<?php

declare(strict_types=1);

namespace Spark\Exchange;

/**
 * Thrown when a requisition exceeds spare capacity. Per the protocol the
 * shelf is closed: refuse, never silently clamp (except the final cap at 100).
 */
final class RequisitionRefused extends \RuntimeException
{
}
