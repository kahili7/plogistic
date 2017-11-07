<?
class Ktmpl_TokenParser_Macro extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $lineno = $token->getLine();
        $name = $this->parser->getStream()->expect(Ktmpl_Token::NAME_TYPE)->getValue();
        $arguments = $this->parser->getExpressionParser()->parseArguments();
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $this->parser->pushLocalScope();
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        
        if ($this->parser->getStream()->test(Ktmpl_Token::NAME_TYPE))
        {
            $value = $this->parser->getStream()->next()->getValue();

            if ($value != $name)
            {
                throw new Ktmpl_Error_Syntax(sprintf("Expected endmacro for macro '$name' (but %s given)", $value), $lineno);
            }
        }

        $this->parser->popLocalScope();
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $this->parser->setMacro($name, new Ktmpl_Node_Macro($name, $body, $arguments, $lineno, $this->getTag()));
        return null;
    }

    public function decideBlockEnd(Ktmpl_Token $token)
    {
        return $token->test('endmacro');
    }

    public function getTag()
    {
        return 'macro';
    }
}
