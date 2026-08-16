<?php

namespace NotificationChannels\GoogleChat\Concerns;

use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;

trait ValidatesCardComponents
{
    /**
     * Ensure that the provided array contains only instances of the provided type, or
     * throw an exception otherwise.
     */
    protected function guardOnlyInstancesOf(string $class, array $bucket): self
    {
        foreach ($bucket as $item) {
            if (! $item instanceof $class) {
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                $call = $trace[1] ?? [];

                throw CouldNotSendNotification::invalidArgument(
                    ($call['class'] ?? '').'::'.($call['function'] ?? '').'()',
                    $class,
                    $item
                );
            }
        }

        return $this;
    }
}
