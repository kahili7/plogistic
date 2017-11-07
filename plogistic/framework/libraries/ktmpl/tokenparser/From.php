<?
class Ktmpl_TokenParser_From extends Ktmpl_TokenParser
{
    public function parse(Ktmpl_Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect('import');
        $targets = array();

        do {
            $name = $stream->expect(Ktmpl_Token::NAME_TYPE)->getValue();
            $alias = $name;

            if ($stream->test('as'))
            {
                $stream->next();
                $alias = $stream->expect(Ktmpl_Token::NAME_TYPE)->getValue();
            }

            $targets[$name] = $alias;

            if (!$stream->test(Ktmpl_Token::PUNCTUATION_TYPE, ','))
            {
                break;
            }

            $stream->next();
        } while (true);

        $stream->expect(Ktmpl_Token::BLOCK_END_TYPE);
        $node = new Ktmpl_Node_Import($macro, new Ktmpl_Node_Expression_AssignName($this->parser->getVarName(), $token->getLine()), $token->getLine(), $this->getTag());

        foreach($targets as $name => $alias)
        {
            $this->parser->addImportedFunction($alias, $name, $node->getNode('var'));
        }

        return $node;
    }

    public function getTag()
    {
        return 'from';
    }
}