<?
class Ktmpl_TokenParser_Extends extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        if (null !== $this->parser->getParent())
                {
            throw new Ktmpl_Error_Syntax('Multiple extends tags are forbidden', $token->getLine());
        }

        $this->parser->setParent($this->parser->getExpressionParser()->parseExpression());
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        return null;
    }

    public function getTag()
    {
        return 'extends';
    }
}