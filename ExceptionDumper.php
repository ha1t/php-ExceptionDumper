<?php
/**
 *
 *
 */

class ExceptionDumper
{
    public static function fetch(Exception $e)
    {
        $class_name = get_class($e);
        $stack_trace = self::getStackTrace($e);
        $html = <<<EOD
<h1>{$class_name}</h1>
<pre>
message: {$e->getMessage()}
code: {$e->getCode()}
line: {$e->getLine()}
file: {$e->getFile()}
</pre>
<pre>
{$e->__toString()}
</pre>
{$stack_trace}
EOD;
        return $html;
    }

    public static function getStackTrace(Exception $e)
    {
        $stack_trace = '';
        foreach ($e->getTrace() as $stack_number => $stack) {
            $code = file($stack['file']);
            $start = max(0, $e->getLine() - 10);
            $end = min(count($code) - 1, $e->getLine() + 10);
            $disp_code = array();
            foreach (range($start, $end) as $index) {
                $line_number = $index + 1;
                $disp_code[] = "{$line_number}:" . $code[$index];
            }
            $code_html = htmlspecialchars(implode('', $disp_code));
            $code_html = "<h2>#{$stack_number}</h2><pre>{$code_html}</pre>";
            $stack_trace .= $code_html;
        }

        return $stack_trace;
    }
}

/*
try {
    $pdo = new PDO('errorです');
} catch (Exception $e) {
    $html = ExceptionDumper::fetch($e);
    echo $html;
}
 */
