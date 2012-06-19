<?php
/**
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
            $start = 0;
            $end = count($code) - 1;
            if (count($code) > 20) {
                $start = max($start, $e->getLine() - 10);
                $end = min($end, $e->getLine() + 10);
            }
            $place = strlen((string)$end);
            $disp_code = array();
            foreach (range($start, $end) as $index) {
                $line_number = sprintf("%0{$place}d", $index + 1);
                $line_code = htmlspecialchars($code[$index], ENT_QUOTES);
                if ($e->getLine() == ($index + 1)) {
                    $disp_code[] = '<span style="color: red;">' . "{$line_number}:{$line_code}</span>";
                } else {
                    $disp_code[] = "{$line_number}:" . $line_code;
                }
            }
            $code_html = implode('', $disp_code);
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
