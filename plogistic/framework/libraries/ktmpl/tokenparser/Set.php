<?
class Ktmpl_TokenParser_Set extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $names = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $capture = false;

        if ($stream->test(Ktmpl_Token::OPERATOR_TYPE, '='))
        {
            $stream->next();
            $values = $this->parser->getExpressionParser()->parseMultitargetExpression();
            $stream->expect(Ktmpl_Token::BLOCK_END_TYPE);

            if (count($names) !== count($values))
            {
                throw new Ktmpl_Error_Syntax("When using set, you must have the same number of variables and assignements.", $lineno);
            }
        } 
        else
        {
            $capture = true;

            if (count($names) > 1)
            {
                throw new Ktmpl_Error_Syntax("When using set with a block, you cannot have a multi-target.", $lineno);
            }

            $stream->expect(Ktmpl_Token::BLOCK_END_TYPE);
            $values = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            $stream->expect(Ktmpl_Token::BLOCK_END_TYPE);
        }

        return new Ktmpl_Node_Set($capture, $names, $values, $lineno, $this->getTag());
    }

    public function decideBlockEnd(Ktmpl_Token $token)
    {
        return $token->test('endset');
    }

    public function getTag()
    {
        return 'set';
    }
}