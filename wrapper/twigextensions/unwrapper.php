<?php
namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class wrapper extends \Twig_Extension {

  public function getName() {
    return Craft::t('Unwrapper');
  }

  public function getFilters() {
    return array(
      'unwrap' => new Twig_Filter_Method( $this, 'unwrapFilter', array('is_safe' => array('html')) )
    );
  }

  // $allow retains any specific tags you want to keep
  // {{ "<h1><span><cite> title </cite></span></h1>"|unwrap('h1') }} - Removees all tags from the string, apart from the h1
  public function unwrapFilter($html, $allow=null) {
    if (!is_null($allow) && is_string($html)) {
      // Remove any '<' and '>' if they exists
      $allow = str_replace(array('<', '>'), ' ', $allow);
      // Clear any empty elements and add everything to the allow array, so they should just be words/letters
      $allow = array_filter(explode(' ', $allow));
      // Now we have a clean array, regardless of how the allow conditions were passed.
      // Lets reapply the '<' and '>' bits to each element.
      array_walk($allow, function(&$item) { $item = '<'.$item.'>'; }); // or ;
      // And convert it all back to a string
      $allow = '"'.implode('', $allow).'"';
    }

    return strip_tags($html, $allow).html_entity_decode('');
  }
}
