<?
class Ktmpl_TokenParser_AutoEscape extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $lineno = $token->getLine();
        $value = $this->parser->getStream()->expect(Ktmpl_Token::NAME_TYPE)->getValue();
        
        if (!in_array($value, array('true', 'false')))
        {
            throw new Ktmpl_Error_Syntax("Autoescape value must be 'true' or 'false'", $lineno);
        }

        $value = 'true' === $value ? 'html' : false;

        if ($this->parser->getStream()->test(Ktmpl_Token::NAME_TYPE))
        {
            if (false === $value)
            {
                throw new Ktmpl_Error_Syntax('Unexpected escaping strategy as you set autoescaping to false.', $lineno);
            }

            $value = $this->parser->getStream()->next()->getValue();
        }

        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        return new Ktmpl_Node_AutoEscape($value, $body, $lineno, $this->getTag());
    }

    public function decideBlockEnd(Ktmpl_Token $token)
    {
        return $token->test('endautoescape');
    }

    public function getTag()
    {
        return 'autoescape';
    }
}