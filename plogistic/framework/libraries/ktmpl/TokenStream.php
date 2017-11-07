<?
class Ktmpl_TokenStream
{
    protected $tokens;
    protected $current;
    protected $filename;

    public function __construct(array $tokens, $filename = null)
    {
        $this->tokens     = $tokens;
        $this->current    = 0;
        $this->filename   = $filename;
    }

    public function __toString()
    {
        return implode("\n", $this->tokens);
    }

    public function next()
    {
        if (!isset($this->tokens[++$this->current])) 
        {
            throw new Ktmpl_Error_Syntax('Unexpected end of template');
        }

        return $this->tokens[$this->current - 1];
    }

    public function expect($type, $value = null, $message = null)
    {
        $token = $this->tokens[$this->current];
	
        if (!$token->test($type, $value)) 
        {
            $line = $token->getLine();
            throw new Ktmpl_Error_Syntax(sprintf('%sUnexpected token "%s" of value "%s" ("%s" expected%s)',
                $message ? $message.'. ' : '',
                Ktmpl_Token::typeToEnglish($token->getType(), $line), $token->getValue(),
                Ktmpl_Token::typeToEnglish($type, $line), $value ? sprintf(' with value "%s"', $value) : ''),
                $line
            );
        }
		
        $this->next();
        return $token;
    }

    public function look($number = 1)
    {
        if (!isset($this->tokens[$this->current + $number])) 
		{
            throw new Ktmpl_Error_Syntax('Unexpected end of template');
        }

        return $this->tokens[$this->current + $number];
    }

    public function test($primary, $secondary = null)
    {
        return $this->tokens[$this->current]->test($primary, $secondary);
    }

    public function isEOF()
    {
        return $this->tokens[$this->current]->getType() === Twig_Token::EOF_TYPE;
    }

    public function getCurrent()
    {
        return $this->tokens[$this->current];
    }

    public function getFilename()
    {
        return $this->filename;
    }
}