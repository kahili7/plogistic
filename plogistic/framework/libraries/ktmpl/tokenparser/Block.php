<?
class Ktmpl_TokenParser_Block extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = $stream->expect(Ktmpl_Token::NAME_TYPE)->getValue();

        if ($this->parser->hasBlock($name))
        {
            throw new Ktmpl_Error_Syntax("The block '$name' has already been defined", $lineno);
        }

        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);

        if ($stream->test(Ktmpl_Token::BLOCK_END_TYPE))
        {
            $stream->next();
            $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);

            if ($stream->test(Ktmpl_Token::NAME_TYPE))
            {
                $value = $stream->next()->getValue();

                if ($value != $name)
                {
                    throw new Ktmpl_Error_Syntax(sprintf("Expected endblock for block '$name' (but %s given)", $value), $lineno);
                }
            }
        } 
        else
        {
            $body = new Ktmpl_Node(array(
                new Ktmpl_Node_Print($this->parser->getExpressionParser()->parseExpression(), $lineno),
            ));
        }

        $stream->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $block = new Ktmpl_Node_Block($name, $body, $lineno);
        $this->parser->setBlock($name, $block);
        $this->parser->popBlockStack();
        $this->parser->popLocalScope();

        return new Ktmpl_Node_BlockReference($name, $lineno, $this->getTag());
    }

    public function decideBlockEnd(Ktmpl_Token $token)
    {
        return $token->test('endblock');
    }

    public function getTag()
    {
        return 'block';
    }
}