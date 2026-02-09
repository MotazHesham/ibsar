<?php

namespace App\Services\Workflow;

use Illuminate\Support\Arr;

/**
 * ConditionEvaluator
 *
 * Safely evaluates simple boolean expressions against a context array,
 * without using PHP eval. The supported expression syntax is intentionally
 * small but sufficient for most workflow routing cases.
 *
 * Supported:
 *  - Logical AND / OR:   "a > 0 && b == 1", "flag == true || count >= 3"
 *  - Comparison operators: ==, !=, >=, <=, >, <
 *  - Identifiers: treated as keys in the given context array (dot notation supported)
 *  - Literals: numbers, booleans (true/false), and quoted strings ('value' or "value")
 *
 * Example:
 *   beneficiaries_count >= required_capacity && stock_available == true
 */
class ConditionEvaluator
{
    /**
     * Evaluate an expression against the provided context.
     */
    public function evaluate(?string $expression, array $context = []): bool
    {
        if ($expression === null || trim($expression) === '') {
            // Empty expression is treated as "no extra condition" and thus true.
            return true;
        }

        // First split by OR, then by AND inside each clause.
        $orClauses = preg_split('/\s*\|\|\s*/', $expression);

        foreach ($orClauses as $clause) {
            if ($clause === '') {
                continue;
            }

            $andParts = preg_split('/\s*&&\s*/', $clause);
            $andResult = true;

            foreach ($andParts as $part) {
                $part = trim($part);
                if ($part === '') {
                    continue;
                }

                if (!$this->evaluateComparison($part, $context)) {
                    $andResult = false;
                    break;
                }
            }

            if ($andResult) {
                return true;
            }
        }

        return false;
    }

    protected function evaluateComparison(string $expr, array $context): bool
    {
        $pattern = '/^([a-zA-Z_][a-zA-Z0-9_\.]*)\s*(==|!=|>=|<=|>|<)\s*(.+)$/';

        if (!preg_match($pattern, $expr, $matches)) {
            // If we cannot parse, be safe and treat as false so it doesn't accidentally open a path.
            return false;
        }

        [, $leftKey, $operator, $rightRaw] = $matches;

        $leftValue = $this->resolveContextValue($leftKey, $context);
        $rightValue = $this->parseLiteralOrIdentifier(trim($rightRaw), $context);

        return $this->compare($leftValue, $rightValue, $operator);
    }

    protected function resolveContextValue(string $key, array $context)
    {
        // Dot notation access.
        return Arr::get($context, $key);
    }

    protected function parseLiteralOrIdentifier(string $raw, array $context)
    {
        // Quoted string.
        if ((str_starts_with($raw, "'") && str_ends_with($raw, "'")) ||
            (str_starts_with($raw, '"') && str_ends_with($raw, '"'))) {
            return substr($raw, 1, -1);
        }

        // Boolean.
        if (strcasecmp($raw, 'true') === 0) {
            return true;
        }
        if (strcasecmp($raw, 'false') === 0) {
            return false;
        }

        // Numeric.
        if (is_numeric($raw)) {
            return $raw + 0;
        }

        // Otherwise treat as another identifier in context.
        return $this->resolveContextValue($raw, $context);
    }

    protected function compare($left, $right, string $operator): bool
    {
        switch ($operator) {
            case '==':
                return $left == $right;
            case '!=':
                return $left != $right;
            case '>=':
                return $left >= $right;
            case '<=':
                return $left <= $right;
            case '>':
                return $left > $right;
            case '<':
                return $left < $right;
            default:
                return false;
        }
    }
}

