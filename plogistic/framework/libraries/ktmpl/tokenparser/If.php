<?
class Ktmpl_TokenParser_If extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $lineno = $token->getLine();
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideIfFork'));
        $tests = array($expr, $body);
        $else = null;
        $end = false;

        while (!$end)
        {
            switch ($this->parser->getStream()->next()->getValue())
            {
                case 'else':
                    $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
                    $else = $this->parser->subparse(array($this, 'decideIfEnd'));
                    break;

                case 'elseif':
                    $expr = $this->parser->getExpressionParser()->parseExpression();
                    $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
                    $body = $this->parser->subparse(array($this, 'decideIfFork'));
                    $tests[] = $expr;
                    $tests[] = $body;
                    break;

                case 'endif':
                    $end = true;
                    break;

                default:
                    throw new Ktmpl_Error_Syntax(sprintf('Unexpected end of template. Ktmpl was looking for the following tags "else", "elseif", or "endif" to close the "if" block started at line %d)', $lineno), -1);
            }
        }

        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        return new Ktmpl_Node_If(new Ktmpl_Node($tests), $else, $lineno, $this->getTag());
    }

    public function decideIfFork(Ktmpl_Token $token)
    {
        return $token->test(array('elseif', 'else', 'endif'));
    }

    public function decideIfEnd(Ktmpl_Token $token)
    {
        return $token->test(array('endif'));
    }

    public function getTag()
    {
        return 'if';
    }
}