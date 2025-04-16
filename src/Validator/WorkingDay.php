<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class WorkingDay extends Constraint
{
    public const MODE_WORKING = 'working';
    public const MODE_HOLIDAY = 'holiday';

    public string $workingMessage = 'The date should be a working day.';
    public string $holidayMessage = 'The date should not be a working day.';

    // You can use #[HasNamedArguments] to make some constraint options required.
    // All configurable options must be passed to the constructor.
    public function __construct(
        public string $mode = self::MODE_WORKING,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
