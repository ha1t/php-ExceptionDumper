<?php
/**
 *
 * TODO $stack['args']をうまいこと表示したい
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
            if (!file_exists($stack['file'])) {
                $stack_trace .= "<h2>#{$stack_number}:{$stack['function']}</h2>" . PHP_EOL;
                $stack_trace .= "<div>{$stack['file']}</div>";
                continue;
            }
            $code = file($stack['file']);
            $start = 0;
            $end = count($code) - 1;
            if (count($code) > 20) {
                $start = max($start, $stack['line'] - 10);
                $end = min($end, $stack['line'] + 10);
            }
            $place = strlen((string)$end);
            $disp_code = array();
            foreach (range($start, $end) as $index) {
                $line_number = sprintf("%0{$place}d", $index + 1);
                $line_code = htmlspecialchars($code[$index], ENT_QUOTES);
                if ($stack['line'] == ($index + 1)) {
                    $disp_code[] = '<span style="color: red;">' . "{$line_number}:{$line_code}</span>";
                } else {
                    $disp_code[] = "{$line_number}:" . $line_code;
                }
            }
            $code_html = implode('', $disp_code);
            $stack_trace .= "<h2>#{$stack_number}:{$stack['function']}</h2>" . PHP_EOL;
            $stack_trace .= "<div>{$stack['file']}</div><br /><pre>{$code_html}</pre>";
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
