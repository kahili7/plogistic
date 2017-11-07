<?
class Ktmpl_Markup
{
    protected $content;

    public function __construct($content)
    {
        $this->content = (string) $content;
    }

    public function __toString()
    {
        return $this->content;
    }
}