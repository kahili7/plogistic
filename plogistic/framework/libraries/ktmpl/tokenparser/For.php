<?
class Ktmpl_TokenParser_For extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $lineno = $token->getLine();
        $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $this->parser->getStream()->expect(Ktmpl_Token::OPERATOR_TYPE, 'in');
        $seq = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideForFork'));

        if ($this->parser->getStream()->next()->getValue() == 'else')
        {
            $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse(array($this, 'decideForEnd'), true);
        } 
        else
        {
            $else = null;
        }

        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);

        if (count($targets) > 1)
        {
            $keyTarget = $targets->getNode(0);
            $valueTarget = $targets->getNode(1);
        } 
        else
        {
            $keyTarget = new Ktmpl_Node_Expression_AssignName('_key', $lineno);
            $valueTarget = $targets->getNode(0);
        }

        return new Ktmpl_Node_For($keyTarget, $valueTarget, $seq, $body, $else, $lineno, $this->getTag());
    }

    public function decideForFork(Ktmpl_Token $token)
    {
        return $token->test(array('else', 'endfor'));
    }

    public function decideForEnd(Ktmpl_Token $token)
    {
        return $token->test('endfor');
    }

    public function getTag()
    {
        return 'for';
    }
}