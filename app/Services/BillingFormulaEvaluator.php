<?php

namespace App\Services;

use InvalidArgumentException;

class BillingFormulaEvaluator
{
    public const WATER_FORMULA = '((current_reading - previous_reading) * 5) * 35.00';

    public const ELECTRICITY_FORMULA = '(current_electricity_reading - previous_electricity_reading) * 13.00';

    public function evaluate(string $formula, float $currentReading, float $previousReading): float
    {
        $expression = strtolower($formula);
        $replacements = [
            'current_electricity_reading' => $this->number($currentReading),
            'previous_electricity_reading' => $this->number($previousReading),
            'current_reading' => $this->number($currentReading),
            'previous_reading' => $this->number($previousReading),
        ];

        foreach ($replacements as $name => $value) {
            $expression = preg_replace('/\b'.preg_quote($name, '/').'\b/', $value, $expression) ?? $expression;
        }

        $expression = preg_replace('/\s+/', '', $expression) ?? '';

        if ($expression === '' || ! preg_match('/^[0-9+\-*\/().]+$/', $expression)) {
            throw new InvalidArgumentException('The billing formula is invalid.');
        }

        $tokens = $this->tokenize($expression);
        $index = 0;
        $value = $this->parseExpression($tokens, $index);

        if ($index !== count($tokens)) {
            throw new InvalidArgumentException('The billing formula is invalid.');
        }

        if (! is_finite($value)) {
            throw new InvalidArgumentException('The billing formula could not be calculated.');
        }

        return round($value, 2);
    }

    private function number(float $value): string
    {
        return sprintf('%.6F', $value);
    }

    /**
     * @return list<string>
     */
    private function tokenize(string $expression): array
    {
        preg_match_all('/\d+(?:\.\d+)?|[+\-*\/()]/', $expression, $matches);

        return $matches[0] ?? [];
    }

    /**
     * @param  list<string>  $tokens
     */
    private function parseExpression(array $tokens, int &$index): float
    {
        $value = $this->parseTerm($tokens, $index);

        while (isset($tokens[$index]) && in_array($tokens[$index], ['+', '-'], true)) {
            $operator = $tokens[$index];
            $index++;
            $right = $this->parseTerm($tokens, $index);
            $value = $operator === '+' ? $value + $right : $value - $right;
        }

        return $value;
    }

    /**
     * @param  list<string>  $tokens
     */
    private function parseTerm(array $tokens, int &$index): float
    {
        $value = $this->parseFactor($tokens, $index);

        while (isset($tokens[$index]) && in_array($tokens[$index], ['*', '/'], true)) {
            $operator = $tokens[$index];
            $index++;
            $right = $this->parseFactor($tokens, $index);

            if ($operator === '/') {
                if (abs($right) < 0.0000001) {
                    throw new InvalidArgumentException('The billing formula cannot divide by zero.');
                }

                $value /= $right;
            } else {
                $value *= $right;
            }
        }

        return $value;
    }

    /**
     * @param  list<string>  $tokens
     */
    private function parseFactor(array $tokens, int &$index): float
    {
        if (! isset($tokens[$index])) {
            throw new InvalidArgumentException('The billing formula is invalid.');
        }

        if ($tokens[$index] === '-') {
            $index++;

            return -$this->parseFactor($tokens, $index);
        }

        if ($tokens[$index] === '(') {
            $index++;
            $value = $this->parseExpression($tokens, $index);

            if (($tokens[$index] ?? null) !== ')') {
                throw new InvalidArgumentException('The billing formula is invalid.');
            }

            $index++;

            return $value;
        }

        if (! is_numeric($tokens[$index])) {
            throw new InvalidArgumentException('The billing formula is invalid.');
        }

        $value = (float) $tokens[$index];
        $index++;

        return $value;
    }
}
