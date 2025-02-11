<?php
// Credits à https://chatgpt.com/ pour la detection.
// Function to detect SQL injection patterns
function detect_sql_injection($input)
{
    // List of common SQL injection patterns
    $patterns = [
        '/union.*select/i',    // UNION SELECT
        '/select.*from/i',     // SELECT from
        '/drop/i',             // DROP TABLE
        '/--/',                // SQL comment
        '/;/',                 // SQL statement separator
        '/or\s+1=1/i',         // Common OR condition
        '/\b(=|<>|like|not)\s*[\']/i', // SQL operators near quotes
        '/\b(and|or)\s+\d+\s*=\s*\d+/i', // AND/OR with numbers
        '/exec/i',             // EXEC command
        '/xp_cmdshell/i',      // SQL Server command execution
        '/char\(/i',           // CHAR() function
        '/convert\(/i'         // CONVERT() function
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            return true; // Potential SQL injection detected
        }
    }
    return false; // No SQL injection patterns detected
}
