<?
class Ktmpl_Extension_Core extends Ktmpl_Extension
{
    public function getTokenParsers()
    {
        return array(
            new Ktmpl_TokenParser_For(),
            new Ktmpl_TokenParser_If(),
            new Ktmpl_TokenParser_Extends(),
            new Ktmpl_TokenParser_Include(),
            new Ktmpl_TokenParser_Block(),
            new Ktmpl_TokenParser_Filter(),
            new Ktmpl_TokenParser_Macro(),
            new Ktmpl_TokenParser_Import(),
            new Ktmpl_TokenParser_From(),
            new Ktmpl_TokenParser_Set(),
            new Ktmpl_TokenParser_Spaceless(),
        );
    }

    public function getFilters()
    {
        $filters = array(
            // formatting filters
            'date'    => new Ktmpl_Filter_Function('ktmpl_date_format_filter'),
            'format'  => new Ktmpl_Filter_Function('sprintf'),
            'replace' => new Ktmpl_Filter_Function('ktmpl_strtr'),

            // encoding
            'url_encode'  => new Ktmpl_Filter_Function('ktmpl_urlencode_filter'),
            'json_encode' => new Ktmpl_Filter_Function('json_encode'),

            // string filters
            'title'      => new Ktmpl_Filter_Function('ktmpl_title_string_filter', array('needs_environment' => true)),
            'capitalize' => new Ktmpl_Filter_Function('ktmpl_capitalize_string_filter', array('needs_environment' => true)),
            'upper'      => new Ktmpl_Filter_Function('strtoupper'),
            'lower'      => new Ktmpl_Filter_Function('strtolower'),
            'striptags'  => new Ktmpl_Filter_Function('strip_tags'),

            // array helpers
            'join'    => new Ktmpl_Filter_Function('ktmpl_join_filter'),
            'reverse' => new Ktmpl_Filter_Function('ktmpl_reverse_filter'),
            'length'  => new Ktmpl_Filter_Function('ktmpl_length_filter', array('needs_environment' => true)),
            'sort'    => new Ktmpl_Filter_Function('ktmpl_sort_filter'),
            'merge'   => new Ktmpl_Filter_Function('ktmpl_array_merge'),

            // iteration and runtime
            'default' => new Ktmpl_Filter_Function('ktmpl_default_filter'),
            'keys'    => new Ktmpl_Filter_Function('ktmpl_get_array_keys_filter'),

            // escaping
            'escape' => new Ktmpl_Filter_Function('ktmpl_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'ktmpl_escape_filter_is_safe')),
            'e'      => new Ktmpl_Filter_Function('ktmpl_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'ktmpl_escape_filter_is_safe')),
        );

        if (function_exists('mb_get_info'))
        {
            $filters['upper'] = new Ktmpl_Filter_Function('ktmpl_upper_filter', array('needs_environment' => true));
            $filters['lower'] = new Ktmpl_Filter_Function('ktmpl_lower_filter', array('needs_environment' => true));
        }

        return $filters;
    }

    public function getFunctions()
    {
        return array(
            'range'    => new Ktmpl_Function_Method($this, 'getRange'),
            'constant' => new Ktmpl_Function_Method($this, 'getConstant'),
            'cycle'    => new Ktmpl_Function_Method($this, 'getCycle'),
        );
    }

    public function getRange($start, $end, $step = 1)
    {
        return range($start, $end, $step);
    }

    public function getConstant($value)
    {
        return constant($value);
    }

    public function getCycle($values, $i)
    {
        if (!is_array($values) && !$values instanceof ArrayAccess)
        {
            return $values;
        }

        return $values[$i % count($values)];
    }

    public function getTests()
    {
        return array(
            'even'        => new Ktmpl_Test_Function('ktmpl_test_even'),
            'odd'         => new Ktmpl_Test_Function('ktmpl_test_odd'),
            'defined'     => new Ktmpl_Test_Function('ktmpl_test_defined'),
            'sameas'      => new Ktmpl_Test_Function('ktmpl_test_sameas'),
            'none'        => new Ktmpl_Test_Function('ktmpl_test_none'),
            'divisibleby' => new Ktmpl_Test_Function('ktmpl_test_divisibleby'),
            'constant'    => new Ktmpl_Test_Function('ktmpl_test_constant'),
            'empty'       => new Ktmpl_Test_Function('ktmpl_test_empty'),
        );
    }

    public function getOperators()
    {
        return array(
            array(
                'not' => array('precedence' => 50, 'class' => 'Ktmpl_Node_Expression_Unary_Not'),
                '-'   => array('precedence' => 50, 'class' => 'Ktmpl_Node_Expression_Unary_Neg'),
                '+'   => array('precedence' => 50, 'class' => 'Ktmpl_Node_Expression_Unary_Pos'),
            ),
            array(
                'or'     => array('precedence' => 10, 'class' => 'Ktmpl_Node_Expression_Binary_Or', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                'and'    => array('precedence' => 15, 'class' => 'Ktmpl_Node_Expression_Binary_And', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '=='     => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_Equal', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '!='     => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_NotEqual', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '<'      => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_Less', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '>'      => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_Greater', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '>='     => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_GreaterEqual', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '<='     => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_LessEqual', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                'not in' => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_NotIn', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                'in'     => array('precedence' => 20, 'class' => 'Ktmpl_Node_Expression_Binary_In', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '+'      => array('precedence' => 30, 'class' => 'Ktmpl_Node_Expression_Binary_Add', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '-'      => array('precedence' => 30, 'class' => 'Ktmpl_Node_Expression_Binary_Sub', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '~'      => array('precedence' => 40, 'class' => 'Ktmpl_Node_Expression_Binary_Concat', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '*'      => array('precedence' => 60, 'class' => 'Ktmpl_Node_Expression_Binary_Mul', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '/'      => array('precedence' => 60, 'class' => 'Ktmpl_Node_Expression_Binary_Div', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '//'     => array('precedence' => 60, 'class' => 'Ktmpl_Node_Expression_Binary_FloorDiv', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '%'      => array('precedence' => 60, 'class' => 'Ktmpl_Node_Expression_Binary_Mod', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                'is'     => array('precedence' => 100, 'callable' => array($this, 'parseTestExpression'), 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                'is not' => array('precedence' => 100, 'callable' => array($this, 'parseNotTestExpression'), 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '..'     => array('precedence' => 110, 'class' => 'Ktmpl_Node_Expression_Binary_Range', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_LEFT),
                '**'     => array('precedence' => 200, 'class' => 'Ktmpl_Node_Expression_Binary_Power', 'associativity' => Ktmpl_ExpressionParser::OPERATOR_RIGHT),
            ),
        );
    }

    public function parseNotTestExpression(Ktmpl_Parser $parser, $node)
    {
        return new Ktmpl_Node_Expression_Unary_Not($this->parseTestExpression($parser, $node), $parser->getCurrentToken()->getLine());
    }

    public function parseTestExpression(Ktmpl_Parser $parser, $node)
    {
        $stream = $parser->getStream();
        $name = $stream->expect(Ktmpl_Token::NAME_TYPE);
        $arguments = null;

        if ($stream->test(Ktmpl_Token::PUNCTUATION_TYPE, '('))
        {
            $arguments = $parser->getExpressionParser()->parseArguments();
        }

        return new Ktmpl_Node_Expression_Test($node, $name->getValue(), $arguments, $parser->getCurrentToken()->getLine());
    }

    public function getName()
    {
        return 'core';
    }
}

function ktmpl_date_format_filter($date, $format = 'F j, Y H:i')
{
    if (!$date instanceof DateTime)
    {
        $date = new DateTime((ctype_digit($date) ? '@' : '').$date);
    }

    return $date->format($format);
}

function ktmpl_urlencode_filter($url, $raw = false)
{
    if ($raw)
    {
        return rawurlencode($url);
    }

    return urlencode($url);
}

function ktmpl_array_merge($arr1, $arr2)
{
    if (!is_array($arr1) || !is_array($arr2))
    {
        throw new Ktmpl_Error_Runtime('The merge filter only work with arrays or hashes.');
    }

    return array_merge($arr1, $arr2);
}

function ktmpl_join_filter($value, $glue = '')
{
    return implode($glue, (array) $value);
}

function ktmpl_default_filter($value, $default = '')
{
    return ktmpl_test_empty($value) ? $default : $value;
}

function ktmpl_get_array_keys_filter($array)
{
    if (is_object($array) && $array instanceof Traversable)
    {
        return array_keys(iterator_to_array($array));
    }

    if (!is_array($array))
    {
        return array();
    }

    return array_keys($array);
}

function ktmpl_reverse_filter($array)
{
    if (is_object($array) && $array instanceof Traversable)
    {
        return array_reverse(iterator_to_array($array));
    }

    if (!is_array($array))
    {
        return array();
    }

    return array_reverse($array);
}

function ktmpl_sort_filter($array)
{
    asort($array);
    return $array;
}

function ktmpl_in_filter($value, $compare)
{
    if (is_array($compare))
    {
        return in_array($value, $compare);
    } 
    elseif (is_string($compare))
    {
        return false !== strpos($compare, (string) $value);
    } 
    elseif (is_object($compare) && $compare instanceof Traversable)
    {
        return in_array($value, iterator_to_array($compare, false));
    }

    return false;
}

function ktmpl_strtr($pattern, $replacements)
{
    return str_replace(array_keys($replacements), array_values($replacements), $pattern);
}

function ktmpl_escape_filter(Ktmpl_Environment $env, $string, $type = 'html')
{
    if (is_object($string) && $string instanceof Ktmpl_Markup)
    {
        return $string;
    }

    if (!is_string($string) && !(is_object($string) && method_exists($string, '__toString')))
    {
        return $string;
    }

    switch ($type)
    {
        case 'js':
            // escape all non-alphanumeric characters
            // into their \xHH or \uHHHH representations
            $charset = $env->getCharset();

            if ('UTF-8' != $charset)
            {
                $string = _ktmpl_convert_encoding($string, 'UTF-8', $charset);
            }

            if (null === $string = preg_replace_callback('#[^\p{L}\p{N} ]#u', '_ktmpl_escape_js_callback', $string))
            {
                throw new Ktmpl_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }

            if ('UTF-8' != $charset)
            {
                $string = _ktmpl_convert_encoding($string, $charset, 'UTF-8');
            }

            return $string;

        case 'html':
            return htmlspecialchars($string, ENT_QUOTES, $env->getCharset());

        default:
            throw new Ktmpl_Error_Runtime(sprintf('Invalid escape type "%s".', $type));
    }
}

function ktmpl_escape_filter_is_safe(Ktmpl_Node $filterArgs)
{
    foreach ($filterArgs as $arg)
    {
        if ($arg instanceof Ktmpl_Node_Expression_Constant)
        {
            return array($arg->getAttribute('value'));
        } 
        else
        {
            return array();
        }

        break;
    }

    return array('html');
}

if (function_exists('iconv'))
{
    function _ktmpl_convert_encoding($string, $to, $from)
    {
        return iconv($from, $to, $string);
    }
}
elseif (function_exists('mb_convert_encoding'))
{
    function _ktmpl_convert_encoding($string, $to, $from)
    {
        return mb_convert_encoding($string, $to, $from);
    }
}
else
{
    function _ktmpl_convert_encoding($string, $to, $from)
    {
        throw new Ktmpl_Error_Runtime('No suitable convert encoding function (use UTF-8 as your encoding or install the iconv or mbstring extension).');
    }
}

function _ktmpl_escape_js_callback($matches)
{
    $char = $matches[0];

    // \xHH
    if (!isset($char[1])) {
        return '\\x'.substr('00'.bin2hex($char), -2);
    }

    // \uHHHH
    $char = _ktmpl_convert_encoding($char, 'UTF-16BE', 'UTF-8');

    return '\\u'.substr('0000'.bin2hex($char), -4);
}

if (function_exists('mb_get_info'))
{
    function ktmpl_length_filter(Ktmpl_Environment $env, $thing)
    {
        return is_scalar($thing) ? mb_strlen($thing, $env->getCharset()) : count($thing);
    }

    function ktmpl_upper_filter(Ktmpl_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset()))
        {
            return mb_strtoupper($string, $charset);
        }

        return strtoupper($string);
    }

    function ktmpl_lower_filter(Ktmpl_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset()))
        {
            return mb_strtolower($string, $charset);
        }

        return strtolower($string);
    }

    function ktmpl_title_string_filter(Ktmpl_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset()))
        {
            return mb_convert_case($string, MB_CASE_TITLE, $charset);
        }

        return ucwords(strtolower($string));
    }

    function ktmpl_capitalize_string_filter(Ktmpl_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset()))
        {
            return mb_strtoupper(mb_substr($string, 0, 1, $charset)).
                         mb_strtolower(mb_substr($string, 1, mb_strlen($string), $charset), $charset);
        }

        return ucfirst(strtolower($string));
    }
}
else
{
    function ktmpl_length_filter(Ktmpl_Environment $env, $thing)
    {
        return is_scalar($thing) ? strlen($thing) : count($thing);
    }

    function ktmpl_title_string_filter(Ktmpl_Environment $env, $string)
    {
        return ucwords(strtolower($string));
    }

    function ktmpl_capitalize_string_filter(Ktmpl_Environment $env, $string)
    {
        return ucfirst(strtolower($string));
    }
}

function ktmpl_ensure_traversable($seq)
{
    if (is_array($seq) || (is_object($seq) && $seq instanceof Traversable))
    {
        return $seq;
    } 
    else
    {
        return array();
    }
}

function ktmpl_test_sameas($value, $test)
{
    return $value === $test;
}

function ktmpl_test_none($value)
{
    return null === $value;
}

function ktmpl_test_divisibleby($value, $num)
{
    return 0 == $value % $num;
}

function ktmpl_test_even($value)
{
    return $value % 2 == 0;
}

function ktmpl_test_odd($value)
{
    return $value % 2 == 1;
}

function ktmpl_test_constant($value, $constant)
{
    return constant($constant) === $value;
}

function ktmpl_test_defined($name, $context)
{
    return array_key_exists($name, $context);
}

function ktmpl_test_empty($value)
{
    return null === $value || false === $value || '' === (string) $value;
}