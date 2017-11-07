<?
class Ktmpl_TokenParser_Sandbox extends Ktmpl_TokenParser
{

    public function parse(Ktmpl_Token $token)
    {
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(Ktmpl_Token::BLOCK_END_TYPE);
        return new Ktmpl_Node_Sandbox($body, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Ktmpl_Token $token)
    {
        return $token->test('endsandbox');
    }

    public function getTag()
    {
        return 'sandbox';
    }
}