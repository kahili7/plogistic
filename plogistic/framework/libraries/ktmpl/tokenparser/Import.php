<?
class Ktmpl_TokenParser_Import extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect('as');
        $var = new Ktmpl_Node_Expression_AssignName($this->parser->getStream()->expect(Ktmpl_Token::NAME_TYPE)->getValue(), $token->getLine());
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        return new Ktmpl_Node_Import($macro, $var, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'import';
    }
}