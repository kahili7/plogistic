<?
class Ktmpl_Token
{
    protected $value;
    protected $type;
    protected $lineno;

    const EOF_TYPE         = -1;
    const TEXT_TYPE        = 0;
    const BLOCK_START_TYPE = 1;
    const VAR_START_TYPE   = 2;
    const BLOCK_END_TYPE   = 3;
    const VAR_END_TYPE     = 4;
    const NAME_TYPE        = 5;
    const NUMBER_TYPE      = 6;
    const STRING_TYPE      = 7;
    const OPERATOR_TYPE    = 8;
    const PUNCTUATION_TYPE = 9;

    public function __construct($type, $value, $lineno)
    {
        $this->type   = $type;
        $this->value  = $value;
        $this->lineno = $lineno;
    }

    public function __toString()
    {
        return sprintf('%s(%s)', self::typeToString($this->type, true, $this->lineno), $this->value);
    }

    public function test($type, $values = null)
    {
        if (null === $values && !is_int($type)) 
	{
            $values = $type;
            $type = self::NAME_TYPE;
        }

        return ($this->type === $type) && (null === $values ||
            (is_array($values) && in_array($this->value, $values)) || $this->value == $values);
    }

    public function getLine()
    {
        return $this->lineno;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    static public function typeToString($type, $short = false, $line = -1)
    {
        switch ($type) 
        {
            case self::EOF_TYPE:
                $name = 'EOF_TYPE';
                break;
				
            case self::TEXT_TYPE:
                $name = 'TEXT_TYPE';
                break;
				
            case self::BLOCK_START_TYPE:
                $name = 'BLOCK_START_TYPE';
                break;
				
            case self::VAR_START_TYPE:
                $name = 'VAR_START_TYPE';
                break;
				
            case self::BLOCK_END_TYPE:
                $name = 'BLOCK_END_TYPE';
                break;
				
            case self::VAR_END_TYPE:
                $name = 'VAR_END_TYPE';
                break;
				
            case self::NAME_TYPE:
                $name = 'NAME_TYPE';
                break;
				
            case self::NUMBER_TYPE:
                $name = 'NUMBER_TYPE';
                break;
				
            case self::STRING_TYPE:
                $name = 'STRING_TYPE';
                break;
				
            case self::OPERATOR_TYPE:
                $name = 'OPERATOR_TYPE';
                break;
				
            case self::PUNCTUATION_TYPE:
                $name = 'PUNCTUATION_TYPE';
                break;
				
            default:
                throw new Ktmpl_Error_Syntax(sprintf('Token of type "%s" does not exist.', $type), $line);
        }

        return $short ? $name : 'Ktmpl_Token::'.$name;
    }

    static public function typeToEnglish($type, $line = -1)
    {
        switch ($type) 
		{
            case self::EOF_TYPE:
                return 'end of template';
				
            case self::TEXT_TYPE:
                return 'text';
				
            case self::BLOCK_START_TYPE:
                return 'begin of statement block';
				
            case self::VAR_START_TYPE:
                return 'begin of print statement';
				
            case self::BLOCK_END_TYPE:
                return 'end of statement block';
				
            case self::VAR_END_TYPE:
                return 'end of print statement';
				
            case self::NAME_TYPE:
                return 'name';
				
            case self::NUMBER_TYPE:
                return 'number';
				
            case self::STRING_TYPE:
                return 'string';
				
            case self::OPERATOR_TYPE:
                return 'operator';
				
            case self::PUNCTUATION_TYPE:
                return 'punctuation';
				
            default:
                throw new Ktmpl_Error_Syntax(sprintf('Token of type "%s" does not exist.', $type), $line);
        }
    }
}