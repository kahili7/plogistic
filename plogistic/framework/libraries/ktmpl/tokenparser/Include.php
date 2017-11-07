<?
class Ktmpl_TokenParser_Include extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $variables = null;

        if ($this->parser->getStream()->test(Ktmpl_Token::NAME_TYPE, 'with'))
        {
            $this->parser->getStream()->next();
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;

        if ($this->parser->getStream()->test(Ktmpl_Token::NAME_TYPE, 'only'))
        {
            $this->parser->getStream()->next();
            $only = true;
        }

        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        return new Ktmpl_Node_Include($expr, $variables, $only, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'include';
    }
}