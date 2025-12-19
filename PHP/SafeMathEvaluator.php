<?php

class SafeMathEvaluator
{
    private $functions = ['round', 'ceil', 'floor'];

    public function evaluate(string $expr): float
    {
        $tokens = $this->tokenize($expr);
        $rpn    = $this->toRPN($tokens);
        return $this->computeRPN($rpn);
    }

    private function tokenize(string $expr): array
    {
        $expr = strtolower(str_replace(' ', '', $expr));

        preg_match_all(
            '/round|ceil|floor|\d+(\.\d+)?|[+\-*\/\(\)]/',
            $expr,
            $matches
        );

        if (implode('', $matches[0]) !== $expr) {
            throw new Exception('Invalid characters in expression');
        }

        return $matches[0];
    }

    // private function precedence(string $op): int
    // {
    //     return match ($op) {
    //         '+', '-' => 1,
    //         '*', '/' => 2,
    //         default  => 0
    //     };
    // }

    private function precedence(string $op): int
    {
        switch ($op) {
            case '+':
            case '-':
                return 1;

            case '*':
            case '/':
                return 2;

            default:
                return 0;
        }
    }

    private function toRPN(array $tokens): array
    {
        $output = [];
        $stack  = [];

        foreach ($tokens as $token) {

            if (is_numeric($token)) {
                $output[] = $token;
            } elseif (in_array($token, $this->functions)) {
                $stack[] = $token;
            } elseif (in_array($token, ['+', '-', '*', '/'])) {
                while (!empty($stack)) {
                    $top = end($stack);
                    if (
                        in_array($top, ['+', '-', '*', '/']) &&
                        $this->precedence($top) >= $this->precedence($token)
                    ) {
                        $output[] = array_pop($stack);
                    } else {
                        break;
                    }
                }
                $stack[] = $token;
            } elseif ($token === '(') {
                $stack[] = $token;
            } elseif ($token === ')') {
                while (!empty($stack) && end($stack) !== '(') {
                    $output[] = array_pop($stack);
                }
                array_pop($stack); // remove '('

                if (!empty($stack) && in_array(end($stack), $this->functions)) {
                    $output[] = array_pop($stack);
                }
            }
        }

        while (!empty($stack)) {
            $output[] = array_pop($stack);
        }

        return $output;
    }

    private function computeRPN(array $tokens): float
    {
        $stack = [];

        foreach ($tokens as $token) {

            if (is_numeric($token)) {
                $stack[] = (float) $token;
            } elseif (in_array($token, ['+', '-', '*', '/'])) {
                $b = array_pop($stack);
                $a = array_pop($stack);

                if ($token === '/' && $b == 0) {
                    throw new Exception('Division by zero');
                }

                $stack[] = match ($token) {
                    '+' => $a + $b,
                    '-' => $a - $b,
                    '*' => $a * $b,
                    '/' => $a / $b
                };
            } elseif (in_array($token, $this->functions)) {
                $a = array_pop($stack);

                $stack[] = match ($token) {
                    'round' => round($a),
                    'ceil'  => ceil($a),
                    'floor' => floor($a)
                };
            }
        }

        return array_pop($stack);
    }
}


$evaluator = new SafeMathEvaluator();

try {
    echo $evaluator->evaluate('round(10 / (5 - 3)) + ceil(2.2)');
} catch (Exception $e) {
    echo $e->getMessage(); // "Division by zero" or "Invalid characters"
}
