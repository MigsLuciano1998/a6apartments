<?php

if (! function_exists('peso')) {
    function peso(float|int|string|null $amount): string
    {
        return '₱'.number_format((float) $amount, 2);
    }
}
