<?
class Ktmpl_TokenParser_Filter extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $name = $this->parser->getVarName();
        $ref = new Ktmpl_Node_Expression_BlockReference(new Ktmpl_Node_Expression_Constant($name, $token->getLine()), true, $token->getLine(), $this->getTag());

        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);

        $block = new Ktmpl_Node_Block($name, $body, $token->getLine());
        $this->parser->setBlock($name, $block);

        return new Ktmpl_Node_Print($filter, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Ktmpl_Token $token)
    {
        return $token->test('endfilter');
    }

    public function getTag()
    {
        return 'filter';
    }
}