<?

class TSTYLE
{

    public $background;
    public $backgroundAttachment;
    public $backgroundColor;
    public $backgroundImage;
    public $backgroundPosition;
    public $backgroundRepeat;
    public $border;
    public $borderBottom;
    public $borderBottomColor;
    public $borderBottomStyle;
    public $borderBottomWidth;
    public $borderColor;
    public $borderLeft;
    public $borderLeftColor;
    public $borderLeftStyle;
    public $borderLeftWidth;
    public $borderRight;
    public $borderRightColor;
    public $borderRightStyle;
    public $borderRightWidth;
    public $borderStyle;
    public $borderTop;
    public $borderTopColor;
    public $borderTopStyle;
    public $borderTopWidth;
    public $borderWidth;
    public $clear;
    public $clip;
    public $color;
    public $cursor;
    public $display;
    public $filter;
    public $font;
    public $fontFamily;
    public $fontSize;
    public $fontVariant;
    public $fontWeight;
    public $height;
    public $left;
    public $letterSpacing;
    public $lineHeight;
    public $listStyle;
    public $listStyleImage;
    public $listStylePosition;
    public $listStyleType;
    public $margin;
    public $marginBottom;
    public $marginLeft;
    public $marginRight;
    public $marginTop;
    public $overflow;
    public $padding;
    public $paddingBottom;
    public $paddingLeft;
    public $paddingRight;
    public $paddingTop;
    public $pageBreakAfter;
    public $pageBreakBefore;
    public $position;
    public $styleFloat;
    public $textAlign;
    public $textDecoration;
    public $textDecorationBlink;
    public $textDecorationLineThrough;
    public $textDecorationNone;
    public $textDecorationOverline;
    public $textDecorationUnderline;
    public $textIndent;
    public $textTransform;
    public $top;
    public $verticalAlign;
    public $visibility;
    public $width;
    public $zIndex;

    public function TStyle()
    {
        
    }

    public function toArray()
    {
        $tmp = Array();
        $classFields = get_object_vars($this);

        foreach ($classFields as $key => $value)
        {
            if ($value)
            {
                $tmp[$key] = $value;
            }
        }

        return $tmp;
    }

}

?>