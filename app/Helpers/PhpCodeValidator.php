<?php

namespace App\Helpers;

class PhpCodeValidator
{
    /**
     * Validate PHP code for syntax errors using token_get_all.
     *
     * @param string $code
     * @return array [bool $isValid, string $error]
     */
    public static function validate(string $code): array
    {
        // Normalize code: add PHP tags if missing
        $code = trim($code);

        if (!str_starts_with($code, '<?php')) {
            $code = '<?php ' . $code;
        }

        if (!str_ends_with($code, ';') && !str_ends_with($code, '?>')) {
            $code .= ';';
        }

        if (!str_ends_with($code, '?>')) {
            $code .= ' ?>';
        }

        try {
            // Parse the code to check for syntax errors
            token_get_all($code, TOKEN_PARSE);
            return [true, ''];
        } catch (\ParseError $e) {
            return [false, $e->getMessage()];
        } catch (\Exception $e) {
            return [false, 'Invalid PHP code: ' . $e->getMessage()];
        }
    }
}
