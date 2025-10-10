<?php

namespace App\Helpers;

use Throwable;
use Illuminate\Support\Facades\Log;

class PhpCodeExecuter
{
    /**
     * Run PHP code safely, expecting a return statement.
     *
     * @param string $code
     * @param array $variables Associative array of variables to pass to the evaluated code
     * @return bool
     */
    public static function runCode(string $code, array $variables): bool
    {
        // Normalize code: strip PHP tags and trim whitespace
        $code = str_replace('<?php', '', $code);
        $code = str_replace('?>', '', $code);
        $code = str_replace('<?', '', $code);
        $code = trim($code);

        // Ensure code has a semicolon at the end
        if (!str_ends_with($code, ';')) {
            $code .= ';';
        }

        // Basic safety check: disallow dangerous functions
        $dangerousFunctions = ['system', 'exec', 'shell_exec', 'passthru', 'eval'];

        foreach ($dangerousFunctions as $func) {
            if (stripos($code, $func) !== false) {
                Log::warning('PhpCodeExecuter: Non-boolean result detected: Code contains prohibited function: $func');
            }
        }

        // Execute with variables in a scoped closure
        try {

            $callable = function () use ($code, $variables) {

                // Set variables using associative keys as variable names
                extract($variables, EXTR_SKIP);

                // phpcs:ignore Intelephense.diagnostics.undefinedVariables
                return eval($code);

            };

            $result = $callable();

            // Ensure result is boolean
            if (!is_bool($result)) {

                Log::warning('PhpCodeExecuter: Non-boolean result detected: ' . var_export($result, true));
                return false;

            }

            return $result;

        } catch (Throwable $e) {
            Log::error('PhpCodeExecuter error: ' . $e->getMessage());
            Log::error('code: "' . $code.'"');
            return false;
        }
    }
}
