<?
class Ktmpl_TokenParser_Spaceless extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $lineno = $token->getLine();
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideSpacelessEnd'), true);
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        return new Ktmpl_Node_Spaceless($body, $lineno, $this->getTag());
    }

    public function decideSpacelessEnd(Ktmpl_Token $token)
    {
        return $token->test('endspaceless');
    }

    public function getTag()
    {
        return 'spaceless';
    }
}