<?php

/**
 * @package toolkit
 */
/**
 * The `DatabaseExceptionHandler` provides a render function to provide
 * customised output for database exceptions. It displays the exception
 * message as provided by the Database.
 */
class DatabaseExceptionHandler extends ExceptionHandler
{
    /**
     * The render function will take a `DatabaseException` and output a
     * HTML page.
     *
     * @param Throwable $e
     *  The Throwable object
     * @return string
     *  An HTML string
     */
    public static function render($e)
    {
        // Validate the type, resolve to a 404 if not valid
        if (!static::isValidThrowable($e)) {
            $e = new FrontendPageNotFoundException();
        }

        $trace = $queries = null;

        foreach ($e->getTrace() as $t) {
            $trace .= sprintf(
                '<li><code><em>[%s:%d]</em></code></li><li><code>&#160;&#160;&#160;&#160;%s%s%s();</code></li>',
                $t['file'],
                $t['line'],
                (isset($t['class']) ? $t['class'] : null),
                (isset($t['type']) ? $t['type'] : null),
                $t['function']
            );
        }

        if (is_object(Symphony::Database())) {
            $debug = Symphony::Database()->getLogs();

            if (!empty($debug)) {
                foreach ($debug as $query) {
                    $queries .= sprintf(
                        '<li><em>[%01.4f]</em><code> %s;</code> </li>',
                        (isset($query['execution_time']) ? $query['execution_time'] : null),
                        htmlspecialchars($query['query'])
                    );
                }
            }
        }

        $html = sprintf(
            file_get_contents(self::getTemplate('fatalerror.database')),
            !self::$enabled ? 'Database error' : $e->getDatabaseErrorMessage(),
            !self::$enabled ? '' : $e->getQuery(),
            !self::$enabled ? '' : $trace,
            !self::$enabled ? '' : $queries
        );

        $html = str_replace('{ASSETS_URL}', ASSETS_URL, $html);
        $html = str_replace('{SYMPHONY_URL}', SYMPHONY_URL, $html);
        $html = str_replace('{URL}', URL, $html);
        $html = str_replace('{PHP}', PHP_VERSION, $html);
        $html = str_replace('{MYSQL}', Symphony::Database()->getVersion(), $html);

        return $html;
    }
}